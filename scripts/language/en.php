<?php
/** English **/

// meta + header
$lang['titulekstranky'] = "Liberec Routes — interactive transit map, line overviews, history, videos";
$lang['popisstranky']   = "Liberec Routes — interactive transit map, line overviews, history, videos";
$lang['hlavninadpis']   = "Liberec Routes";
$lang['paticka']        = "Contributors: <a style='color:white' target='_blank' href='http://www.boveraclub.com/'>Boveraclub (historical records)</a>, <a style='color:white' target='_blank' href='https://www.youtube.com/channel/UCDnTRePslg2t4wC5MNPKOjQ/'>Liberecká podniková (videos, proofreading)</a>, Tomáš Krupička Sr. (local facts) and others.<br><a style='color:white' target='_blank' href='https://tomaskrupicka.cz/dopravak/kazda-linka-ma-sve-kouzlo/'>Project idea</a> · <a style='color:white' target='_blank' rel='noopener' href='https://github.com/Grupislav/Liberecke-linky'>Source code (GitHub)</a> · <a style='color:white' target='_blank' href='https://tomaskrupicka.cz/'>Blog</a>.";

// tabs
$lang['prehled']       = "Overview";
$lang['prehled_nav']   = "Line overview";
$lang['historie']      = "History";
$lang['pohledridice']  = "Videos";
$lang['mistopis']      = "Local geography";
$lang['fotogalerie']   = "Photo gallery";

// headings
$lang['funkce']           = "Route function";
$lang['seznamzastavek']   = "List of stops";
$lang['mapa']             = "Map";
$lang['provoznilinky']    = "Operated routes";
$lang['linky_akt_mimo']   = "Routes currently out of service";
$lang['neprovoznilinky']  = "Routes permanently out of service";
$lang['linky_trvale_mimo'] = "Routes permanently out of service";

// messages & placeholders
$lang['zalozkanedostupna'] = "This tab will be added later.";
$lang['historiecekana']    = "The route history is yet to be written.";
$lang['videocakana']       = "The route video is yet to be added.";
$lang['mistopiscekana']    = "The local geography article is yet to be added.";
$lang['fotogaleriecekana'] = "The photo gallery is not available yet. If you have photos of this route, please <a href='mailto:info@tomaskrupicka.cz'>contact me</a>.";
// Template texts from the DB fotogalerie column (localized via strtr in fotogalerie.php).
$lang['fotogalerie_klik']   = "Click here to view the gallery";
$lang['fotogalerie_nejsou'] = "Unfortunately, no photographs of this line are known. If you have any and can share them, please <a href='mailto:info@tomaskrupicka.cz'>get in touch</a>.";
$lang['fotogalerie_alt']    = "Photo of line";

// DB / AJAX errors
$lang['err_db']           = 'Could not connect to the database. Please try again later.';
$lang['err_db_prepare']  = 'Could not prepare the query. Please try again later.';
$lang['err_ajax_tabload'] = 'Could not load this tab. Try refreshing the page.';
$lang['mapa_nedostupna']  = 'Map data is not available.';
$lang['mapa_iframe_title'] = 'Map for route %s';

// Network map page (mapa.php / mapa.js)
$lang['mapa_linky']          = 'Routes';
$lang['mapa_nav']            = 'Live map';
$lang['mapa_titulek']        = 'Live transit map – Liberec | Liberec Routes';
$lang['mapa_popis']          = 'Interactive map of public transit routes and stops in Liberec and Jablonec nad Nisou, based on open data (GTFS).';
$lang['mapa_pozn_poloha']    = 'Vehicle positions are approximate, based on the timetable – no public GPS data is available.';
$lang['mapa_hledat']         = 'Search a route or stop…';
$lang['mapa_zastavky']       = 'Stops';
$lang['mapa_panel']          = 'Panel';
$lang['mapa_skryt_panel']    = 'Hide panel';
$lang['mapa_zobrazit_panel'] = 'Show panel';
$lang['mapa_snap_titulek']   = 'Liberec transit network %s | Liberecké linky';
$lang['mapa_snap_badge']     = 'Historical network %s';
$lang['mapa_snap_live']      = 'live map';
$lang['mapa_snap_nav']       = 'History';
$lang['mapa_hledat_linku']   = 'Search a route…';
$lang['mapa_hledat_zastavku'] = 'Search a stop…';
$lang['mapa_tram']           = 'Trams';
$lang['mapa_bus']            = 'Buses';
$lang['mapa_vse']            = 'In service';
$lang['mapa_filtr_mimo']     = 'Out of service';
$lang['mapa_filtr_historicke'] = 'Historical';
$lang['mapa_nic']            = 'None';
$lang['mapa_zastavka']       = 'Stop';
$lang['mapa_zona']           = 'Zone';
$lang['mapa_bezbarierova']   = 'Wheelchair access';
$lang['mapa_linky_zde']      = 'Routes at this stop';
$lang['mapa_zastavky_linky'] = 'Route stops';
$lang['mapa_zpet']           = 'Back';
$lang['mapa_zobraz_linku']   = 'Show only this route';
$lang['mapa_ano']            = 'yes';
$lang['mapa_ne']             = 'no';
$lang['mapa_neznamo']        = 'unknown';

// integration: header, in-tab preview, map↔detail cross-links
$lang['mapa_sit']            = 'NETWORK MAP';
$lang['mapa_zobraz_v_siti']  = 'Show in the interactive map';
$lang['mapa_detail_linky']   = 'Route detail & history';
$lang['mapa_nahled_alt']     = 'Route %s preview';
$lang['mapa_mimo_provoz_pozn'] = 'Route is approximate.';
$lang['mapa_mimo_nadpis']      = 'Line %s (permanently out of service)';
$lang['mapa_hist_nadpis']      = 'Historical line %s';
$lang['mapa_akt_mimo']         = 'currently out of service';
$lang['mapa_zanikla']          = 'former stop';
$lang['mapa_smer']             = 'To %s';
$lang['mapa_vozidla']          = 'Vehicles';
$lang['mapa_barevne_linky']    = 'Coloured lines';
$lang['mapa_odjezdy']          = 'Departures';
$lang['mapa_odjezdy_zadne']    = 'Nothing departs here right now.';
$lang['mapa_jr_nedostupny']    = 'No timetable is available for today.';
$lang['mapa_jr_spoje']         = 'Trip schedule';

// line-category legend on the map (labels for typy_linek categories)
$lang['mapa_legenda']        = 'Line categories';
$lang['mapa_kat_vylukova']   = 'Replacement';
$lang['mapa_kat_tramvaje']   = 'Trams';
$lang['mapa_kat_autobusy']   = 'Buses';
$lang['mapa_kat_nocni']      = 'Night';
$lang['mapa_kat_pracovni']   = 'Workday';
$lang['mapa_kat_skolni']     = 'School';
$lang['mapa_kat_nakupni']    = 'Commercial';
$lang['mapa_kat_historicke'] = 'Historical';

// singular category names for the route title in the Overview ("Category number: route")
$lang['mapa_katsg_vylukova']   = 'Replacement line';
$lang['mapa_katsg_tramvaje']   = 'Tram';
$lang['mapa_katsg_autobusy']   = 'Bus';
$lang['mapa_katsg_nocni']      = 'Night line';
$lang['mapa_katsg_pracovni']   = 'Workday line';
$lang['mapa_katsg_skolni']     = 'School line';
$lang['mapa_katsg_nakupni']    = 'Commercial line';
$lang['mapa_katsg_historicke'] = 'Historical line';
$lang['mapa_katsg_mimoprovoz'] = 'Out-of-service line';

// intros (no route selected)
$lang['prehled_intro']      = "This tab shows the basic information about the selected route. Please choose a route in the top menu.";
$lang['historie_intro']     = "This tab shows the historical development of the selected route. Please choose a route in the top menu.";
$lang['pohledridice_intro'] = "This tab shows a driver's view video of the selected route and a short commentary. Please choose a route in the top menu.";
$lang['mistopis_intro']     = "This tab contains local geography articles related to places along the selected route. Please choose a route in the top menu.";
$lang['fotogalerie_intro']  = "This tab contains a link to the photo gallery related to the selected route. Please choose a route in the top menu.";

// driver navigation (navigace.php, route variants as GPX)
$lang['nav_nav']          = 'Navigation';
$lang['nav_titulek']      = 'Bus route variants for navigation apps (GPX) | Liberecké linky';
$lang['nav_popis']        = 'All route variants of Liberec and Jablonec bus lines as GPX files for navigation apps, updated daily from the GTFS timetable.';
$lang['nav_nadpis']       = 'Bus routes for navigation apps';
$lang['nav_intro']        = 'Every route variant of each bus line can be downloaded as a GPX file: the actual path from the timetable and the stops in order. Files are refreshed automatically from current DPMLJ data and the link to a variant stays the same. Short-turn trips are covered by the longer variant.';
$lang['nav_navod']        = 'How to use: open the GPX file on your phone in OsmAnd and start navigation along the track. Mapy.com can only display the track. The imported track is a copy on your phone: re-import it after the variant changes.';
$lang['nav_platnost']     = 'Data valid %s to %s.';
$lang['nav_vyber']        = 'Choose a line';
$lang['nav_zmeny']        = 'Recent variant changes';
$lang['nav_zadne_zmeny']  = 'No changes recorded yet.';
$lang['nav_zmena_nova']     = 'new variant';
$lang['nav_zmena_zastavky'] = 'stops changed';
$lang['nav_zmena_trasa']    = 'path changed';
$lang['nav_zmena_zrusena']  = 'discontinued';
$lang['nav_linka']        = 'Line %s';
$lang['nav_variant_1']    = '%d variant';
$lang['nav_variant_2']    = '%d variants';
$lang['nav_variant_5']    = '%d variants';
$lang['nav_stahnout']     = 'Download GPX';
$lang['nav_vse_gpx']      = 'All variants of the line in one GPX';
$lang['nav_zastavek']     = '%d stops';
$lang['nav_km']           = '%s km';
$lang['nav_spoju']        = 'full-route trips: %d';
$lang['nav_zkracene']     = 'short-turn trips: %d';
$lang['nav_dny_po-pa']    = 'Mon–Fri';
$lang['nav_dny_so']       = 'Sat';
$lang['nav_dny_ne']       = 'Sun';
$lang['nav_jen_od']       = 'runs from %s';
$lang['nav_jen_do']       = 'runs until %s';
$lang['nav_priblizna']    = 'Approximate path (straight lines between stops), GTFS has no geometry for it.';
$lang['nav_zastavky']     = 'Stops';
$lang['nav_qr']           = 'Scan with your phone to open this page there.';
$lang['nav_zpet']         = 'All lines';
$lang['nav_zrusena_nadpis'] = 'This variant no longer runs';
$lang['nav_zrusena_text']   = 'Variant “%s” of line %s is not in the timetable since %s. Current variants of the line are listed below.';
$lang['nav_nenalezeno']   = 'This route or line is not in the data.';
$lang['nav_mapa_odkaz']   = 'Route for navigation (GPX)';
$lang['nav_navod_odkaz'] = 'Step-by-step guide';
// návod k GPX – HTML (statický text, vypisuje se bez escapování)
$lang['nav_navod_html'] = <<<'HTML'
<h3>How to use the GPX file</h3>
<p>The GPX file contains the variant's path and its stops in order (as points “1. Name”, “2. Name”…). Download it with the <strong>Download GPX</strong> button of a variant. Your phone saves it to Downloads or offers an app to open it with.</p>
<details open>
  <summary><strong>OsmAnd: navigation along the track</strong> (Android and iPhone, recommended)</summary>
  <ol>
    <li>Install OsmAnd (Google Play / App Store) and download the offline map of the Liberec Region in the app.</li>
    <li>Tap <strong>Download GPX</strong> on this page and open the file in OsmAnd. If the phone only saves it, open it from Downloads and choose OsmAnd, or in OsmAnd: <em>Menu → My Places → Tracks → Import</em>.</li>
    <li>Tap the track (or pick it from the track list) and choose <strong>Follow track</strong>.</li>
    <li>Keep <strong>Attach to the roads</strong> switched off. Otherwise the app recalculates the route as a car would and may avoid bus lanes or bus-only passages.</li>
    <li>Choose whether to be guided to the start of the track or to its nearest point (useful when leaving the depot or joining the route midway).</li>
    <li>Start navigation. Voice prompts follow the shape of the track and may announce ordinary bends too.</li>
  </ol>
</details>
<details>
  <summary><strong>Mapy.com: display only</strong></summary>
  <ol>
    <li>Importing requires signing in to a Mapy.com account.</li>
    <li>In the app open <em>Places and routes</em> (or <em>My Maps</em>) and choose <strong>Import GPX</strong>, or open the file from Downloads in Mapy.com. On a computer you can drag the file onto the map at mapy.com; it then appears on your phone under the same account.</li>
    <li>The track and stops are shown on the map as a guide. Mapy.com offers no voice navigation along an imported track; if you let Mapy.com plan the route itself, it guides you as a car, not along the bus route.</li>
  </ol>
</details>
<details>
  <summary><strong>Tips</strong></summary>
  <ul>
    <li>Download routes in advance, not while driving. Keep the phone in a fixed holder.</li>
    <li>The <em>All variants of the line in one GPX</em> file is good for seeing where the line runs. For navigation use a specific variant, otherwise the app cannot tell which path to follow.</li>
    <li>Each variant covers one direction. The return direction is a separate variant.</li>
    <li>An imported track does not update itself. When <em>Recent variant changes</em> lists a new entry for the line, download the variant again and delete the old one in the app. The file address stays the same.</li>
    <li>The path comes from the DPMLJ timetable (GTFS) and may be simplified. Road signs and dispatcher instructions always take precedence.</li>
    <li>Menu labels in the apps may differ slightly between versions.</li>
  </ul>
</details>
HTML;
