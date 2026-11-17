#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Pojistka před přegenerováním dat: rozhodne, jestli nový GTFS feed (gtfs/) smí
nahradit stávající mapa-assets/data/*.json.

DPMLJ nahrává nový feed obvykle 1–2 dny před začátkem jeho platnosti. Kdyby se
data přepsala hned, mapa by do začátku platnosti neměla jízdní řád pro dnešek
(žádná vozidla ani odjezdy). Proto:

  * nový feed má aspoň jednu službu jedoucí DNES (Europe/Prague)   -> nahradit,
  * stávající data dnes nepokrývají (vypršela / chybí)             -> nahradit
    (nový feed je pak i tak lepší než nic),
  * jinak (nový feed začne platit až v budoucnu, stávající data dnes jedou)
                                                                     -> NEnahrazovat;
    denní workflow to zkusí znovu a nahradí v den začátku platnosti.

Výstup: vypíše důvod a do $GITHUB_OUTPUT zapíše `build=true|false`.
Exit kód je vždy 0 (rozhodnutí není chyba).

Použití (z kořene repa):
    python tools/feed_gate.py                  # dnešek v Europe/Prague
    python tools/feed_gate.py --today 20261117 # simulace jiného dne
"""
import argparse, csv, datetime, json, os, sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
GTFS = os.path.join(ROOT, "gtfs")
TIMETABLE = os.path.join(ROOT, "mapa-assets", "data", "timetable.json")
DAYS = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]


def prague_today():
    try:
        from zoneinfo import ZoneInfo
        return datetime.datetime.now(ZoneInfo("Europe/Prague")).strftime("%Y%m%d")
    except Exception:
        # bez tzdata: CET/CEST přibližně (UTC+1, v létě +2) – pro výběr dne stačí
        now = datetime.datetime.now(datetime.timezone.utc)
        off = 2 if 4 <= now.month <= 9 else 1
        return (now + datetime.timedelta(hours=off)).strftime("%Y%m%d")


def weekday(ymd):
    return datetime.date(int(ymd[:4]), int(ymd[4:6]), int(ymd[6:8])).weekday()   # po=0


def read_csv(name):
    path = os.path.join(GTFS, name)
    if not os.path.exists(path):
        return []
    with open(path, encoding="utf-8-sig", newline="") as fh:
        return list(csv.DictReader(fh))


def services_from_gtfs():
    """service_id -> {d: bitmaska po–ne, f, t} – stejný tvar jako timetable.json."""
    out = {}
    for c in read_csv("calendar.txt"):
        out[c["service_id"]] = {
            "d": "".join("1" if c.get(d) == "1" else "0" for d in DAYS),
            "f": c.get("start_date", ""), "t": c.get("end_date", ""),
        }
    return out


def runs_on(services, ymd, added=(), removed=()):
    """Jede v den `ymd` aspoň jedna služba? (calendar + výjimky calendar_dates)"""
    wd = weekday(ymd)
    for sid, s in services.items():
        if sid in removed:
            continue
        if s.get("d", "")[wd:wd + 1] == "1" and s.get("f", "") <= ymd <= s.get("t", ""):
            return True
    return bool(set(added) - set(removed))


def new_feed_runs_on(ymd):
    services = services_from_gtfs()
    added, removed = set(), set()
    for r in read_csv("calendar_dates.txt"):
        if r.get("date") == ymd:
            (added if r.get("exception_type") == "1" else removed).add(r.get("service_id"))
    return runs_on(services, ymd, added, removed), services


def current_runs_on(ymd):
    try:
        tt = json.load(open(TIMETABLE, encoding="utf-8"))
    except (OSError, ValueError):
        return False
    return runs_on(tt.get("services", {}), ymd)


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--today", help="YYYYMMDD (výchozí: dnešek v Europe/Prague)")
    args = ap.parse_args()
    today = args.today or prague_today()

    new_ok, services = new_feed_runs_on(today)
    cur_ok = current_runs_on(today)
    starts = min((s["f"] for s in services.values() if s.get("f")), default="?")

    if new_ok:
        build, why = True, f"nový feed jede dnes ({today}) – nahrazuji data"
    elif not cur_ok:
        build, why = True, (f"stávající data dnes ({today}) nepokrývají – nahrazuji, "
                            f"i když nový feed začíná až {starts}")
    else:
        build, why = False, (f"nový feed začíná až {starts}, stávající data dnes ({today}) "
                             f"pokrývají – ponechávám je, nahradí se v den začátku platnosti")

    print(("BUILD: " if build else "SKIP: ") + why)
    gh = os.environ.get("GITHUB_OUTPUT")
    if gh:
        with open(gh, "a", encoding="utf-8") as fh:
            fh.write(f"build={'true' if build else 'false'}\n")
            fh.write(f"reason={why}\n")
    return 0


if __name__ == "__main__":
    sys.exit(main())
