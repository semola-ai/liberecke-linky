<?php
// ────────────────────────────────────────────────────────────────────────
// NAVIGACE – varianty tras autobusových linek ke stažení jako GPX
// Data: mapa-assets/data/navigace.json (+ navigace-zmeny.json) a GPX soubory
// v mapa-assets/navigace/, vše generuje tools/build_data.py (tools/navigace.py).
//   /navigace              → přehled linek + poslední změny
//   /navigace/<linka>      → varianty linky (?linka=)
//   /navigace/<id>.gpx     → stažení GPX (?gpx=); zrušená varianta → hláška (410)
// Bez databáze.
// ────────────────────────────────────────────────────────────────────────
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/scripts/variableCheck.php"; // nastaví $l, $lang, $jazyky
require_once __DIR__ . "/scripts/fce.php";

$__appBase = isset($appBasePath) ? rtrim((string)$appBasePath, '/') : '';
$faviconHref = $__appBase === '' ? '/favicon.png' : $__appBase . '/favicon.png';
$asset = static function (string $p) use ($__appBase): string {
    return ($__appBase === '' ? '' : $__appBase) . '/' . ltrim($p, '/');
};
$esc = static function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$t = static function (string $key, string $fallback = '') use ($lang) {
    return $lang[$key] ?? $fallback;
};
// odkaz s jazykem (cz = výchozí, bez ?ja)
$href = static function (string $path) use ($asset, $l): string {
    return $asset($path) . ($l !== 'cz' ? '?ja=' . rawurlencode($l) : '');
};
$isoToCz = static function (string $iso) use ($l): string {
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $iso, $m)) return $iso;
    return $l === 'en' ? $iso : ((int)$m[3] . '. ' . (int)$m[2] . '. ' . $m[1]);
};
$ymdToIso = static function (string $ymd): string {
    return preg_match('/^\d{8}$/', $ymd) ? substr($ymd, 0, 4) . '-' . substr($ymd, 4, 2) . '-' . substr($ymd, 6, 2) : $ymd;
};

$nav = json_decode((string)@file_get_contents(__DIR__ . '/mapa-assets/data/navigace.json'), true) ?: [];
$lines = $nav['lines'] ?? [];
$removed = $nav['removed'] ?? [];

// ── stažení GPX ────────────────────────────────────────────────────────
$gpx = isset($_GET['gpx']) ? (string)$_GET['gpx'] : '';
$removedNotice = null;
if ($gpx !== '') {
    if (preg_match('/^[A-Za-z0-9-]{1,150}$/', $gpx)) {
        $file = __DIR__ . '/mapa-assets/navigace/' . $gpx . '.gpx';
        if (is_file($file)) {
            header('Content-Type: application/gpx+xml; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $gpx . '.gpx"');
            header('Content-Length: ' . filesize($file));
            header('Cache-Control: no-cache');          // ať si telefon vždy vezme aktuální verzi
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            readfile($file);
            exit;
        }
        if (isset($removed[$gpx])) {
            $removedNotice = $removed[$gpx] + ['id' => $gpx];
        }
    }
    http_response_code($removedNotice ? 410 : 404);
}

// ── vybraná linka (podle slugu nebo čísla) ─────────────────────────────
$lineKey = null;
$want = isset($_GET['linka']) ? (string)$_GET['linka'] : '';
if ($removedNotice) $want = (string)$removedNotice['line'];
if ($want !== '') {
    foreach ($lines as $sn => $L) {
        if ((string)$sn === $want || ($L['slug'] ?? '') === $want) { $lineKey = (string)$sn; break; }
    }
    if ($lineKey === null && $gpx === '') http_response_code(404);
}
$notFound = ($want !== '' && $lineKey === null && !$removedNotice) || ($gpx !== '' && !$removedNotice);

$changes = json_decode((string)@file_get_contents(__DIR__ . '/mapa-assets/data/navigace-zmeny.json'), true) ?: [];
if ($lineKey !== null) {
    $changes = array_values(array_filter($changes, static function ($c) use ($lineKey) { return (string)$c['line'] === $lineKey; }));
}
$changes = array_slice($changes, 0, $lineKey !== null ? 10 : 25);

$__host = $_SERVER['HTTP_HOST'] ?? 'tomaskrupicka.cz';
$selfUrl = 'https://' . $__host . $asset('navigace' . ($lineKey !== null ? '/' . ($lines[$lineKey]['slug'] ?? $lineKey) : ''))
         . ($l !== 'cz' ? '?ja=' . rawurlencode($l) : '');

$title = $lineKey !== null
    ? sprintf($t('nav_linka', 'Linka %s'), $lineKey) . ' – ' . $t('nav_titulek', 'Trasy linek do navigace (GPX)')
    : $t('nav_titulek', 'Trasy linek do navigace (GPX)');
$dayKeys = ['po-pa', 'so', 'ne'];
$sortedLines = array_keys($lines);
usort($sortedLines, static function ($a, $b) {
    $ia = ctype_digit((string)$a); $ib = ctype_digit((string)$b);
    if ($ia && $ib) return (int)$a <=> (int)$b;
    if ($ia !== $ib) return $ia ? -1 : 1;
    return strcmp((string)$a, (string)$b);
});
?>
<!DOCTYPE html>
<html lang="<?= $esc($l === 'cz' ? 'cs' : $l) ?>">
<head>
  <meta charset="UTF-8">
  <title><?= $esc($title) ?></title>
  <meta name="description" content="<?= $esc($t('nav_popis')) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if ($notFound): ?><meta name="robots" content="noindex"><?php endif; ?>
  <link rel="icon" href="<?= $esc($faviconHref) ?>" type="image/png">
  <link rel="canonical" href="<?= $esc($selfUrl) ?>">
  <link rel="stylesheet" href="<?= $esc($asset('css/css.css')) . av('css/css.css') ?>" type="text/css">
  <link rel="stylesheet" href="<?= $esc($asset('mapa-assets/navigace.css')) . av('mapa-assets/navigace.css') ?>" type="text/css">
</head>
<body class="nav-body">

<div class="roztahovak-modry">
  <div class="hlavicka container">
    <div id="nadpis">
      <h1><a class="nadpis-home" href="<?= $esc($href('') . '#prehled') ?>"><?= $esc($lang['hlavninadpis']) ?></a></h1>
      <span class="nadpis-sep">|</span>
      <span class="nadpis-switch">
        <a href="<?= $esc($href('')) ?>"><?= $esc($lang['prehled_nav'] ?? $lang['prehled']) ?></a>
        <a href="<?= $esc($href('mapa')) ?>"><?= $esc($t('mapa_nav', 'Živá mapa')) ?></a>
        <a class="current" href="<?= $esc($href('navigace')) ?>"><?= $esc($t('nav_nav', 'Navigace')) ?></a>
      </span>
    </div>
    <div id="menu">
      <nav>
        <ul>
          <li class="lang-switch">
            <?php $__other = $l === 'cz' ? 'en' : 'cz'; ?>
            <a href="<?= $esc(url_with_params(['ja' => $__other === 'cz' ? null : $__other])) ?>"><?= strtoupper($esc($__other)) ?></a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</div>

<main class="nav-main">
<?php if ($lineKey === null): ?>
  <h2><?= $esc($t('nav_nadpis', 'Trasy linek do navigace')) ?></h2>
  <?php if ($notFound): ?><p class="nav-warn"><?= $esc($t('nav_nenalezeno')) ?></p><?php endif; ?>
  <p><?= $esc($t('nav_intro')) ?></p>
  <p class="nav-howto"><?= $esc($t('nav_navod')) ?> <a href="#navod"><?= $esc($t('nav_navod_odkaz', 'Podrobný návod')) ?> &darr;</a></p>
  <?php if (!empty($nav['valid_from'])): ?>
  <p class="nav-meta"><?= $esc(sprintf($t('nav_platnost', 'Data platná %s až %s.'), $isoToCz($nav['valid_from']), $isoToCz($nav['valid_to'] ?? ''))) ?> Data: <a href="https://www.dpmlj.cz/opendata" target="_blank" rel="noopener">DPMLJ a.s.</a></p>
  <?php endif; ?>

  <h3><?= $esc($t('nav_vyber', 'Vyberte linku')) ?></h3>
  <ul class="nav-lines">
    <?php foreach ($sortedLines as $sn): $L = $lines[$sn]; ?>
    <li><a href="<?= $esc($href('navigace/' . ($L['slug'] ?? $sn))) ?>" title="<?= $esc($sn . ': ' . ($L['long_name'] ?? '')) ?>">
      <span class="nav-badge"><?= $esc($sn) ?></span>
      <?php $__n = count($L['variants'] ?? []); $__f = $__n === 1 ? '1' : (($__n >= 2 && $__n <= 4) ? '2' : '5'); ?>
      <span class="nav-count"><?= $esc(sprintf($t('nav_variant_' . $__f, '%d'), $__n)) ?></span>
    </a></li>
    <?php endforeach; ?>
  </ul>
<?php else: $L = $lines[$lineKey]; ?>
  <p class="nav-back"><a href="<?= $esc($href('navigace')) ?>">&larr; <?= $esc($t('nav_zpet', 'Všechny linky')) ?></a></p>
  <?php if ($removedNotice): ?>
  <div class="nav-warn">
    <strong><?= $esc($t('nav_zrusena_nadpis', 'Varianta už nejede')) ?></strong><br>
    <?= $esc(sprintf($t('nav_zrusena_text'), $removedNotice['title'] ?? $removedNotice['id'], $lineKey, $isoToCz($removedNotice['date'] ?? ''))) ?>
  </div>
  <?php endif; ?>
  <div class="nav-head">
    <div>
      <h2><?= $esc(sprintf($t('nav_linka', 'Linka %s'), $lineKey)) ?></h2>
      <p class="nav-sub"><?= $esc($L['long_name'] ?? '') ?></p>
      <p class="nav-meta"><?= $esc(sprintf($t('nav_platnost', 'Data platná %s až %s.'), $isoToCz($nav['valid_from'] ?? ''), $isoToCz($nav['valid_to'] ?? ''))) ?></p>
      <p><a class="nav-btn nav-btn-alt" href="<?= $esc($asset('navigace/linka-' . ($L['slug'] ?? $lineKey) . '.gpx')) ?>" download><?= $esc($t('nav_vse_gpx')) ?></a></p>
    </div>
    <figure class="nav-qr" aria-hidden="true">
      <div id="nav-qr" data-url="<?= $esc($selfUrl) ?>"></div>
      <figcaption><?= $esc($t('nav_qr')) ?></figcaption>
    </figure>
  </div>
  <p class="nav-howto"><?= $esc($t('nav_navod')) ?> <a href="#navod"><?= $esc($t('nav_navod_odkaz', 'Podrobný návod')) ?> &darr;</a></p>

  <ol class="nav-variants">
    <?php foreach (($L['variants'] ?? []) as $v): ?>
    <li class="nav-variant" id="<?= $esc($v['id']) ?>">
      <h3><?= $esc($v['title']) ?></h3>
      <p class="nav-facts">
        <?= $esc(sprintf($t('nav_zastavek', '%d zastávek'), count($v['stops']))) ?> ·
        <?= $esc(sprintf($t('nav_km', '%s km'), $l === 'en' ? $v['km'] : str_replace('.', ',', (string)$v['km']))) ?> ·
        <?= $esc(sprintf($t('nav_spoju'), $v['trips'])) ?>
        <?php if (!empty($v['short_trips'])): ?> · <?= $esc(sprintf($t('nav_zkracene'), $v['short_trips'])) ?><?php endif; ?>
      </p>
      <ul class="nav-days">
        <?php foreach ($dayKeys as $dk): if (empty($v['jizdy'][$dk])) continue; $j = $v['jizdy'][$dk]; ?>
        <li><strong><?= $esc($t('nav_dny_' . $dk, $dk)) ?>:</strong>
          <?= $esc(!empty($j['times']) ? implode(', ', $j['times']) : ($j['n'] . '× ' . $j['first'] . '–' . $j['last'])) ?></li>
        <?php endforeach; ?>
        <?php if (!empty($v['valid_from'])): ?><li><?= $esc(sprintf($t('nav_jen_od'), $isoToCz($ymdToIso($v['valid_from'])))) ?></li><?php endif; ?>
        <?php if (!empty($v['valid_to'])): ?><li><?= $esc(sprintf($t('nav_jen_do'), $isoToCz($ymdToIso($v['valid_to'])))) ?></li><?php endif; ?>
      </ul>
      <?php if (!empty($v['approximate'])): ?><p class="nav-warn"><?= $esc($t('nav_priblizna')) ?></p><?php endif; ?>
      <p><a class="nav-btn" href="<?= $esc($asset('navigace/' . $v['id'] . '.gpx')) ?>" download><?= $esc($t('nav_stahnout', 'Stáhnout GPX')) ?></a></p>
      <details>
        <summary><?= $esc($t('nav_zastavky', 'Zastávky')) ?></summary>
        <ol class="nav-stops">
          <?php foreach ($v['stops'] as $s): ?><li><?= $esc($s['name']) ?></li><?php endforeach; ?>
        </ol>
      </details>
    </li>
    <?php endforeach; ?>
  </ol>
<?php endif; ?>

  <section id="navod" class="nav-guide"><?= $t('nav_navod_html') ?></section>

  <h3><?= $esc($t('nav_zmeny', 'Poslední změny variant')) ?></h3>
  <?php if (!$changes): ?>
  <p class="nav-meta"><?= $esc($t('nav_zadne_zmeny')) ?></p>
  <?php else: ?>
  <ul class="nav-changes">
    <?php foreach ($changes as $c):
      $cLine = $lines[(string)$c['line']] ?? null;
      $exists = $c['type'] !== 'zrusena' && $cLine !== null;
    ?>
    <li class="nav-ch-<?= $esc($c['type']) ?>">
      <span class="nav-ch-date"><?= $esc($isoToCz($c['date'])) ?></span>
      <span class="nav-badge nav-badge-s"><?= $esc($c['line']) ?></span>
      <?php if ($exists): ?><a href="<?= $esc($href('navigace/' . ($cLine['slug'] ?? $c['line'])) . '#' . $c['id']) ?>"><?= $esc($c['title']) ?></a>
      <?php else: ?><span><?= $esc($c['title']) ?></span><?php endif; ?>
      <em><?= $esc($t('nav_zmena_' . $c['type'], $c['type'])) ?></em>
    </li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</main>

<div class="roztahovak-modry paticka-wrap">
  <div class="paticka container">
    <p><?= $lang['paticka'] ?></p>
  </div>
</div>

<?php if ($lineKey !== null): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  (function () {
    var el = document.getElementById('nav-qr');
    if (el && window.QRCode) {
      new QRCode(el, { text: el.getAttribute('data-url'), width: 128, height: 128, correctLevel: QRCode.CorrectLevel.M });
    }
  })();
</script>
<?php endif; ?>
</body>
</html>
