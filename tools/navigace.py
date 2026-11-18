# -*- coding: utf-8 -*-
"""
Varianty tras autobusových linek pro navigace řidičů (GPX).

Volá se z build_data.py. Z GTFS najde všechny varianty každé autobusové linky
(různé sledy zastávek), vyřadí zkrácené spoje (sled zastávek, který je souvislou
částí delší varianty téže linky) a pro každou variantu vytvoří GPX se skutečnou
trasou (GTFS shape) a zastávkami.

Výstup:
  mapa-assets/navigace/<id>.gpx          jedna varianta (trasa + zastávky)
  mapa-assets/navigace/linka-<linka>.gpx všechny varianty linky v jednom souboru
  mapa-assets/data/navigace.json          index variant (pro navigace.php a mapu)
  mapa-assets/data/navigace-zmeny.json    záznam změn variant (nová/změněná/zrušená)
Čte (ručně udržovaný, nepovinný):
  mapa-assets/data/navigace-body.json     ruční opravy odkazů do Mapy.com tam, kde plánovač uhýbá:
                                          {"mezi": ["Viadukt", "Nádraží"], "pozn": "…"} = bod v půlce
                                          trasy mezi dvěma po sobě jdoucími zastávkami (oba směry),
                                          {"lat": …, "lon": …, "pozn": "…"} = bod, kterým trasa vede (do 30 m)

Stálá adresa: id varianty se odvodí z linky, první a poslední zastávky (případně
„přes“ / „bez“ rozlišující zastávky) a při dalším běhu se pro stejný sled zastávek
převezme z předchozího navigace.json – odkaz uložený v telefonu tak vede pořád na
tutéž variantu. GPX neobsahuje čas generování, takže se mění jen se změnou dat.
"""
import collections, datetime, hashlib, json, math, os, re, unicodedata
from xml.sax.saxutils import escape, quoteattr

SITE_URL = "https://tomaskrupicka.cz/liberecke-linky"
NAV_SIMPLIFY_TOL = 0.00001      # ~1 m – navigace chce trasu co nejpřesnější
NAV_FILL_MIN = 30.0             # m – úsek s menším rizikem už body z trasy nedoplňuje
NAV_LONG_W = 0.06               # váha délky úseku bez bodu proti odchylce trasy (1 km ~ 60 m)
MAPY_WAYPOINTS = 15             # limit průjezdních bodů v odkazu Mapy.com (mapy.com/fnc/v1/route)
LIST_TIMES_MAX = 8              # do kolika spojů za typ dne vypsat všechny časy
REMOVED_KEEP_DAYS = 400         # jak dlouho držet zrušené varianty (hláška místo 404)
CHANGES_KEEP = 300              # kolik posledních záznamů změn držet
BUS_ROUTE_TYPES = {"3", "11", "700", "704", "715"}   # bus/trolejbus (tramvaje navigaci nepotřebují)
# Linky bez pevné trasy – poptávková doprava (např. „Poptávková doprava oblast Puškinova“)
# jezdí podle objednávek, varianta trasy pro navigaci nedává smysl.
EXCLUDE_LINE_RE = re.compile(r"popt[aá]vk", re.IGNORECASE)


# ---------------------------------------------------------------- pomocné
def slug(s):
    s = unicodedata.normalize("NFKD", str(s))
    s = "".join(c for c in s if not unicodedata.combining(c))
    s = re.sub(r"[^A-Za-z0-9]+", "-", s).strip("-")
    return s or "x"


def haversine_km(a, b):
    (lon1, lat1), (lon2, lat2) = a, b
    p1, p2 = math.radians(lat1), math.radians(lat2)
    dp, dl = p2 - p1, math.radians(lon2 - lon1)
    h = math.sin(dp / 2) ** 2 + math.cos(p1) * math.cos(p2) * math.sin(dl / 2) ** 2
    return 6371.0 * 2 * math.asin(min(1.0, math.sqrt(h)))


def is_contig_sub(a, b):
    """Je `a` souvislou částí delšího sledu `b`?"""
    n = len(a)
    if n >= len(b):
        return False
    return any(b[i:i + n] == a for i in range(len(b) - n + 1))


def hhmm(sec):
    sec %= 86400
    return "%d:%02d" % (sec // 3600, (sec % 3600) // 60)


def ymd_iso(s):
    return "%s-%s-%s" % (s[:4], s[4:6], s[6:8]) if s and len(s) == 8 else (s or "")


def prague_today():
    try:
        from zoneinfo import ZoneInfo
        return datetime.datetime.now(ZoneInfo("Europe/Prague")).strftime("%Y%m%d")
    except Exception:
        now = datetime.datetime.now(datetime.timezone.utc)
        off = 2 if 4 <= now.month <= 9 else 1
        return (now + datetime.timedelta(hours=off)).strftime("%Y%m%d")


# ---------------------------------------------------------------- GPX
def _xy(pts):
    """lon/lat -> lokální metry (stačí pro vzdálenosti v rámci města)."""
    lat0 = sum(p[1] for p in pts) / len(pts)
    c = math.cos(math.radians(lat0)) * 111320.0
    return [(p[0] * c, p[1] * 111320.0) for p in pts]


def _perp_m(p, a, b):
    bx, by = b[0] - a[0], b[1] - a[1]
    px, py = p[0] - a[0], p[1] - a[1]
    L = bx * bx + by * by
    t = 0.0 if L == 0 else max(0.0, min(1.0, (px * bx + py * by) / L))
    return math.hypot(px - t * bx, py - t * by)


def nav_waypoints(pts, stops=(), pins=(), n=MAPY_WAYPOINTS, stop_names=(), between=()):
    """Vybere nejvýš n průjezdních bodů pro plánovač Mapy.com tak, aby jeho trasa
    autem kopírovala trasu linky. pts = trasa [(lon,lat)], stops = zastávky v pořadí
    [(lon,lat)], stop_names = jejich názvy, pins = ručně zadané body (vynutí se,
    když jimi trasa vede), between = dvojice názvů zastávek (jede-li jimi varianta
    po sobě, v libovolném pořadí, vynutí se bod v půlce trasy mezi nimi).

    Body jsou zastávky – ne jejich souřadnice (leží mimo vozovku, plánovač pak
    dělal zajížďky), ale místo, kde kolem nich projíždí trasa linky, tj. ulice,
    kudy autobus opravdu jede. Má-li varianta víc zastávek než n, postupně se
    vyřazují ty, bez kterých je trasa mezi sousedními body nejméně delší než
    přímá vzdálenost (zajížďky do sídlišť a smyčky zůstanou) a úsek se nejméně
    prodlouží (dlouhý úsek bez bodu láká plánovač na souběžnou rychlostní silnici). Vrací [(lon, lat)] včetně začátku a konce."""
    if len(pts) < 2:
        return list(pts)
    xy = _xy(list(pts) + list(stops) + list(pins))
    P, S, PIN = xy[:len(pts)], xy[len(pts):len(pts) + len(stops)], xy[len(pts) + len(stops):]
    cum = [0.0]
    for i in range(1, len(P)):
        cum.append(cum[-1] + math.hypot(P[i][0] - P[i - 1][0], P[i][1] - P[i - 1][1]))
    last = len(P) - 1

    def nearest(q, lo):
        best, bi = 1e18, None
        for i in range(lo, len(P)):
            d = (P[i][0] - q[0]) ** 2 + (P[i][1] - q[1]) ** 2
            if d < best:
                best, bi = d, i
        return bi, math.sqrt(best)

    # zastávky promítnuté na trasu: každé zastávce průjezd trasy kolem ní tak, aby
    # pořadí odpovídalo pořadí zastávek (DP přes lokální minima vzdálenosti) – u linek
    # tam a zpět po stejné ulici nebo se smyčkou tak zastávka nepadne na jiný průjezd
    R, SKIP = 120.0, 150.0
    passes = []
    for q in S:
        d = [math.hypot(p[0] - q[0], p[1] - q[1]) for p in P]
        loc = [i for i in range(len(P)) if d[i] < R
               and (i == 0 or d[i] <= d[i - 1]) and (i == len(P) - 1 or d[i] <= d[i + 1])]
        passes.append([(i, d[i]) for i in loc])
    # stav = index trasy poslední přiřazené zastávky; table[k][stav] = (náklad, předchozí stav, přiřazeno)
    states = {-1: 0.0}
    table = []
    for cands in passes:
        row = {}
        for prev, c in states.items():                 # zastávku vynechat
            if prev not in row or c + SKIP < row[prev][0]:
                row[prev] = (c + SKIP, prev, False)
        for i, di in cands:                            # zastávku přiřadit průjezdu i
            prevs = [(c, prev) for prev, c in states.items() if prev <= i]
            if prevs:
                c, prev = min(prevs)
                if i not in row or c + di < row[i][0]:
                    row[i] = (c + di, prev, True)
        table.append(row)
        states = {k: v[0] for k, v in row.items()}
    anchor_of = [None] * len(passes)
    cur = min(states, key=states.get)
    for k in range(len(passes) - 1, -1, -1):
        _, prev, assigned = table[k][cur]
        if assigned:
            anchor_of[k] = cur
        cur = prev

    cand = {i for i in anchor_of if i is not None and 50 < cum[i] < cum[last] - 50}

    # ručně vynucené body
    forced = set()
    for q in PIN:
        i, d = nearest(q, 0)
        if i is not None and d < 30 and 0 < i < last:
            forced.add(i)
    pairs = {frozenset((x.strip().lower(), y.strip().lower())) for x, y in between}
    names = [str(x).strip().lower() for x in stop_names]
    for k in range(min(len(names), len(anchor_of)) - 1):
        if frozenset((names[k], names[k + 1])) in pairs:
            x, y = anchor_of[k], anchor_of[k + 1]
            if x is not None and y is not None and y - x > 1:
                mid = (cum[x] + cum[y]) / 2
                forced.add(min(range(x + 1, y), key=lambda i: abs(cum[i] - mid)))

    keep = sorted({0, last} | cand | forced)

    def cost(a, b):
        """Riziko úseku a–b bez dalšího bodu v metrech: o kolik je trasa linky delší
        než přímá vzdálenost (zajížďka do sídliště, smyčka, odbočka – plánovač ji
        mezi a a b neprojede) a délka úseku převedená vahou NAV_LONG_W (dlouhý
        úsek láká na souběžnou rychlejší silnici)."""
        length = cum[b] - cum[a]
        excess = length - math.hypot(P[b][0] - P[a][0], P[b][1] - P[a][1])
        return max(excess, length * NAV_LONG_W)

    while len(keep) - 2 > n:
        best, bk = None, None
        for k in range(1, len(keep) - 1):
            if keep[k] in forced:
                continue
            c = cost(keep[k - 1], keep[k + 1])
            if best is None or c < best:
                best, bk = c, k
        if bk is None:          # zbyly jen vynucené body
            break
        del keep[bk]

    # málo zastávek (expresní spoje, dlouhé úseky bez zastávky): zbylé body doplnit
    # z trasy – do nejrizikovějšího úseku, kde trasa nejvíc uhýbá, nebo do půlky
    def split(a, b):
        dm, im = 0.0, None
        for i in range(a + 1, b):
            d = _perp_m(P[i], P[a], P[b])
            if d > dm:
                dm, im = d, i
        if im is not None and dm >= (cum[b] - cum[a]) * NAV_LONG_W:
            return dm, im
        if b - a > 1:
            mid = (cum[a] + cum[b]) / 2
            return (cum[b] - cum[a]) * NAV_LONG_W, min(range(a + 1, b), key=lambda i: abs(cum[i] - mid))
        return 0.0, None
    while len(keep) - 2 < n:
        segs = [(split(a, b), a, b) for a, b in zip(keep, keep[1:])]
        (c, i), a, b = max(segs, key=lambda t: t[0][0])
        if i is None or c < NAV_FILL_MIN:
            break
        keep = sorted(keep + [i])
    return [pts[i] for i in keep]


def mapy_url(pts, stops=(), pins=(), stop_names=(), between=()):
    """Odkaz, který v aplikaci (nebo na webu) Mapy.com otevře plánování trasy autem
    přes průjezdní body vybrané nav_waypoints(). pts/stops = [(lon, lat)]."""
    wp = nav_waypoints(pts, stops, pins, stop_names=stop_names, between=between)
    f = lambda p: "%.6f,%.6f" % (p[0], p[1])
    url = "https://mapy.com/fnc/v1/route?start=%s&end=%s&routeType=car_fast_traffic" % (f(wp[0]), f(wp[-1]))
    if len(wp) > 2:
        url += "&waypoints=" + ";".join(f(p) for p in wp[1:-1])
    return url + "&navigate=true"


def gpx_doc(name, desc, link, tracks, waypoints):
    """tracks = [(název, [(lon,lat), ...])], waypoints = [(lon, lat, název, popis)]"""
    o = ['<?xml version="1.0" encoding="UTF-8"?>',
         '<gpx version="1.1" creator="Liberecké linky (data DPMLJ, GTFS)" '
         'xmlns="http://www.topografix.com/GPX/1/1">',
         "<metadata>",
         "<name>%s</name>" % escape(name),
         "<desc>%s</desc>" % escape(desc),
         "<link href=%s><text>Liberecké linky</text></link>" % quoteattr(link),
         "</metadata>"]
    for lon, lat, wname, wdesc in waypoints:
        o.append('<wpt lat="%.6f" lon="%.6f"><name>%s</name><desc>%s</desc><type>Zastávka</type></wpt>'
                 % (lat, lon, escape(wname), escape(wdesc)))
    for tname, pts in tracks:
        o.append("<trk><name>%s</name><trkseg>" % escape(tname))
        o.extend('<trkpt lat="%.6f" lon="%.6f"/>' % (lat, lon) for lon, lat in pts)
        o.append("</trkseg></trk>")
    o.append("</gpx>")
    return "\n".join(o) + "\n"


# ---------------------------------------------------------------- hlavní funkce
def build_navigation(out_data, out_gpx, routes, trips, seq_by_trip, station_of, stations,
                     pts_by_shape, calendar, feed, simplify, today=None):
    """Vrátí slovník s počty pro výpis. Viz docstring modulu."""
    route_by_id = {r["route_id"]: r for r in routes}
    days = ["monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]
    services = {c["service_id"]: {"d": "".join("1" if c.get(d) == "1" else "0" for d in days),
                                  "f": c.get("start_date", ""), "t": c.get("end_date", "")}
                for c in calendar}
    feed_from = feed.get("feed_start_date", "") or min((s["f"] for s in services.values() if s["f"]), default="")
    feed_to = feed.get("feed_end_date", "") or max((s["t"] for s in services.values() if s["t"]), default="")

    def stop_name(sid):
        s = stations.get(sid)
        return s["stop_name"].strip() if s else sid

    def stop_ll(sid):
        s = stations.get(sid)
        return (float(s["stop_lon"]), float(s["stop_lat"])) if s else None

    # ---- sledy zastávek (stanic) po spojích, seskupené po linkách -------
    patterns = collections.defaultdict(lambda: collections.defaultdict(list))   # linka -> sled -> [trip]
    long_name = {}
    for t in trips:
        r = route_by_id.get(t["route_id"])
        if not r or r.get("route_type") not in BUS_ROUTE_TYPES:
            continue
        if EXCLUDE_LINE_RE.search(r.get("route_short_name", "") + " " + r.get("route_long_name", "")):
            continue
        seq = []
        for _, sid, dep in seq_by_trip.get(t["trip_id"], []):
            stn = station_of(sid)
            if not seq or seq[-1][0] != stn:
                seq.append((stn, dep))
        if len(seq) < 2:
            continue
        sn = r["route_short_name"]
        long_name[sn] = r.get("route_long_name", "")
        patterns[sn][tuple(s for s, _ in seq)].append((t, seq[0][1]))

    # ---- předchozí stav (kvůli stálým id a záznamu změn) --------------
    idx_path = os.path.join(out_data, "navigace.json")
    chg_path = os.path.join(out_data, "navigace-zmeny.json")
    try:
        prev = json.load(open(idx_path, encoding="utf-8"))
    except (OSError, ValueError):
        prev = None
    try:
        changes = json.load(open(chg_path, encoding="utf-8"))
    except (OSError, ValueError):
        changes = []
    # ručně vynucené průjezdní body pro Mapy.com (oprava míst, kde plánovač uhýbá)
    pins, between = [], []
    try:
        for b in json.load(open(os.path.join(out_data, "navigace-body.json"), encoding="utf-8")):
            if isinstance(b, dict) and isinstance(b.get("mezi"), list) and len(b["mezi"]) == 2:
                between.append((str(b["mezi"][0]), str(b["mezi"][1])))
            elif isinstance(b, dict) and "lat" in b and "lon" in b:
                pins.append((float(b["lon"]), float(b["lat"])))
    except (OSError, ValueError, TypeError):
        pass
    prev_vars = {}          # id -> záznam
    prev_by_seq = {}        # (linka, sled id zastávek) -> id
    if prev:
        for sn, L in prev.get("lines", {}).items():
            for v in L.get("variants", []):
                prev_vars[v["id"]] = dict(v, line=sn)
                prev_by_seq[(sn, tuple(s["id"] for s in v["stops"]))] = v["id"]
    change_date = max(today or prague_today(), feed_from or "")

    def sn_key(x):
        try:
            return (0, int(x), "")
        except ValueError:
            return (1, 0, x)

    lines_out = {}
    used_ids = set()
    new_changes = []
    gpx_files = {}

    for sn in sorted(patterns, key=sn_key):
        pats = patterns[sn]
        seqs = sorted(pats, key=lambda s: (-len(pats[s]), s))
        maximal = [s for s in seqs if not any(is_contig_sub(s, o) for o in seqs if o != s)]
        # zkrácené spoje přiřaď k nejdelší variantě, která je obsahuje
        short_of = collections.Counter()
        for s in seqs:
            if s in maximal:
                continue
            owners = [m for m in maximal if is_contig_sub(s, m)]
            if owners:
                short_of[max(owners, key=len)] += len(pats[s])

        # ---- stálá id: nejdřív převzít podle shodného sledu, pak odvodit z názvu
        ids = {}
        for s in maximal:
            pid = prev_by_seq.get((sn, s))
            if pid and pid not in used_ids:
                ids[s] = pid
                used_ids.add(pid)
        groups = collections.defaultdict(list)
        for s in maximal:
            groups[(s[0], s[-1])].append(s)
        label = {}
        for (a, b), grp in groups.items():
            base = "%s-%s-%s" % (slug(sn), slug(stop_name(a)), slug(stop_name(b)))
            for s in grp:
                others = [o for o in grp if o != s]
                via = bez = None
                if others:
                    oset = set().union(*[set(o) for o in others])
                    via = next((x for x in s[1:-1] if x not in oset), None)
                    if via is None:
                        mine = set(s)
                        bez = next((x for o in others for x in o[1:-1] if x not in mine), None)
                cand = base + ("-pres-" + slug(stop_name(via)) if via else
                               "-bez-" + slug(stop_name(bez)) if bez else "")
                label[s] = (via, bez)
                if s in ids:
                    continue
                k, cid = 2, cand
                while cid in used_ids or cid in ids.values():
                    cid = "%s-%d" % (cand, k); k += 1
                ids[s] = cid
                used_ids.add(cid)

        variants = []
        all_tracks, all_wpts, seen_wpt = [], [], set()
        for s in sorted(maximal, key=lambda s: (-len(pats[s]), ids[s])):
            vid = ids[s]
            tlist = pats[s]
            # geometrie: nejčastější shape spojů varianty
            shp = collections.Counter(t["shape_id"] for t, _ in tlist if t.get("shape_id"))
            pts = None
            for shid, _ in shp.most_common():
                raw = [(lo, la) for _, lo, la in pts_by_shape.get(shid, [])]
                if len(raw) >= 2:
                    pts = simplify(raw, NAV_SIMPLIFY_TOL)
                    break
            approx = pts is None
            if approx:
                pts = [p for p in (stop_ll(x) for x in s) if p]
            pts = [(round(lo, 6), round(la, 6)) for lo, la in pts]
            length = sum(haversine_km(pts[i], pts[i + 1]) for i in range(len(pts) - 1))
            geom_hash = hashlib.sha1(json.dumps(pts).encode()).hexdigest()[:12]

            # kdy jede: počty spojů a časy odjezdu z výchozí zastávky po typech dne
            per = {"po-pa": [], "so": [], "ne": []}
            v_from, v_to = [], []
            for t, dep in tlist:
                sv = services.get(t["service_id"])
                if not sv:
                    continue
                d = sv["d"]
                if "1" in d[:5]: per["po-pa"].append(dep)
                if d[5] == "1": per["so"].append(dep)
                if d[6] == "1": per["ne"].append(dep)
                if sv["f"]: v_from.append(sv["f"])
                if sv["t"]: v_to.append(sv["t"])
            jizdy = {}
            for k, deps in per.items():
                if not deps:
                    continue
                deps = sorted(set(d for d in deps if d is not None))
                e = {"n": len(deps), "first": hhmm(deps[0]), "last": hhmm(deps[-1])}
                if len(deps) <= LIST_TIMES_MAX:
                    e["times"] = [hhmm(d) for d in deps]
                jizdy[k] = e
            vf, vt = (min(v_from) if v_from else ""), (max(v_to) if v_to else "")

            first, last = stop_name(s[0]), stop_name(s[-1])
            via, bez = label[s]
            title = "%s → %s" % (first, last)
            if via:
                title += " (přes %s)" % stop_name(via)
            elif bez:
                title += " (bez %s)" % stop_name(bez)
            rec = {
                "id": vid, "title": title, "from": first, "to": last,
                "via": stop_name(via) if via else "", "without": stop_name(bez) if bez else "",
                "stops": [{"id": x, "name": stop_name(x)} for x in s],
                "km": round(length, 1), "trips": len(tlist), "short_trips": short_of.get(s, 0),
                "jizdy": jizdy, "geom": geom_hash, "mapy": mapy_url(pts, [stop_ll(x) for x in s if stop_ll(x)], pins,
                                 [stop_name(x) for x in s if stop_ll(x)], between),
            }
            if approx:
                rec["approximate"] = True
            if vf and vf > (feed_from or vf):
                rec["valid_from"] = vf
            if vt and vt < (feed_to or vt):
                rec["valid_to"] = vt
            variants.append(rec)

            # GPX varianty
            wpts = []
            for i, x in enumerate(s):
                ll = stop_ll(x)
                if ll:
                    wpts.append((ll[0], ll[1], "%d. %s" % (i + 1, stop_name(x)),
                                 "Linka %s, zastávka %d/%d" % (sn, i + 1, len(s))))
                    if x not in seen_wpt:
                        seen_wpt.add(x)
                        all_wpts.append((ll[0], ll[1], stop_name(x), "Linka %s" % sn))
            desc = ("Linka %s: %s. Trasa podle jízdního řádu DPMLJ (GTFS), platnost %s až %s. "
                    "Zastávek %d, délka %.1f km.%s Data: DPMLJ a.s., trasa může být zjednodušená – "
                    "řiďte se dopravním značením." % (
                        sn, title, ymd_iso(vf or feed_from), ymd_iso(vt or feed_to), len(s), length,
                        " Trasa je jen přibližná (spojnice zastávek)." if approx else ""))
            name = "Linka %s: %s" % (sn, title)
            gpx_files[vid + ".gpx"] = gpx_doc(name, desc, "%s/navigace/%s" % (SITE_URL, slug(sn)),
                                              [(name, pts)], wpts)
            all_tracks.append((name, pts))

            # záznam změn
            old = prev_vars.get(vid)
            if prev is not None:
                if old is None:
                    new_changes.append({"date": ymd_iso(change_date), "line": sn, "id": vid,
                                        "type": "nova", "title": title})
                elif tuple(x["id"] for x in old["stops"]) != s:
                    new_changes.append({"date": ymd_iso(change_date), "line": sn, "id": vid,
                                        "type": "zastavky", "title": title})
                elif old.get("geom") != geom_hash:
                    new_changes.append({"date": ymd_iso(change_date), "line": sn, "id": vid,
                                        "type": "trasa", "title": title})

        lines_out[sn] = {"slug": slug(sn), "long_name": long_name.get(sn, ""), "variants": variants}
        gpx_files["linka-%s.gpx" % slug(sn)] = gpx_doc(
            "Linka %s – všechny varianty" % sn,
            "Linka %s, %d variant tras podle jízdního řádu DPMLJ (GTFS), platnost %s až %s. Data: DPMLJ a.s."
            % (sn, len(variants), ymd_iso(feed_from), ymd_iso(feed_to)),
            "%s/navigace/%s" % (SITE_URL, slug(sn)), all_tracks, all_wpts)

    # ---- zrušené varianty (stálá adresa ukáže hlášku místo chyby) ------
    removed = dict(prev.get("removed", {})) if prev else {}
    for vid, old in prev_vars.items():
        if vid not in used_ids:
            removed[vid] = {"line": old["line"], "title": old.get("title", ""), "date": ymd_iso(change_date)}
            new_changes.append({"date": ymd_iso(change_date), "line": old["line"], "id": vid,
                                "type": "zrusena", "title": old.get("title", "")})
    limit = (datetime.date(int(change_date[:4]), int(change_date[4:6]), int(change_date[6:8]))
             - datetime.timedelta(days=REMOVED_KEEP_DAYS)).isoformat() if len(change_date) == 8 else ""
    removed = {k: v for k, v in removed.items() if k not in used_ids and v.get("date", "") >= limit}

    index = {
        "valid_from": ymd_iso(feed_from), "valid_to": ymd_iso(feed_to),
        "version": feed.get("feed_version", ""),
        "lines": lines_out,
        "removed": dict(sorted(removed.items())),
    }
    changes = (sorted(new_changes, key=lambda c: (c["line"], c["id"])) + changes)[:CHANGES_KEEP] \
        if new_changes else changes

    # ---- zápis -------------------------------------------------------
    os.makedirs(out_gpx, exist_ok=True)
    for fn in os.listdir(out_gpx):          # složka patří generátoru – smaž neplatné GPX
        if fn.endswith(".gpx") and fn not in gpx_files:
            os.remove(os.path.join(out_gpx, fn))
    for fn, body in gpx_files.items():
        path = os.path.join(out_gpx, fn)
        try:
            if open(path, encoding="utf-8").read() == body:
                continue
        except OSError:
            pass
        with open(path, "w", encoding="utf-8", newline="\n") as fh:
            fh.write(body)
    with open(idx_path, "w", encoding="utf-8") as fh:
        json.dump(index, fh, ensure_ascii=False, separators=(",", ":"))
    with open(chg_path, "w", encoding="utf-8") as fh:
        json.dump(changes, fh, ensure_ascii=False, indent=1)

    return {"lines": len(lines_out), "variants": sum(len(L["variants"]) for L in lines_out.values()),
            "changes": len(new_changes), "removed": len(removed)}
