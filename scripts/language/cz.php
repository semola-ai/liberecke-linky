<?php
/** Čeština / Czech **/

// meta + header
$lang['titulekstranky'] = "Liberecké linky — interaktivní mapa MHD, přehledy, historie, videozáznamy";
$lang['popisstranky']   = "Liberecké linky — interaktivní mapa MHD, přehledy, historie, videozáznamy";
$lang['hlavninadpis']   = "Liberecké linky";
$lang['paticka']        = "Spolupracovali: <a style='color:white' target='_blank' href='http://www.boveraclub.com/'>Boveraclub (historické záznamy)</a>, <a style='color:white' target='_blank' href='https://www.youtube.com/channel/UCDnTRePslg2t4wC5MNPKOjQ/'>Liberecká podniková (videozáznamy, korektury)</a>, Tomáš Krupička st. (doplnění a korektura místopisných údajů) a další.<br><a style='color:white' target='_blank' href='https://tomaskrupicka.cz/dopravak/kazda-linka-ma-sve-kouzlo/'>Smysl projektu</a> · <a style='color:white' target='_blank' rel='noopener' href='https://github.com/Grupislav/Liberecke-linky'>Zdrojový kód (GitHub)</a> · <a style='color:white' target='_blank' href='https://tomaskrupicka.cz/'>Blog</a>.";

// tabs
$lang['prehled']       = "Přehled";
$lang['prehled_nav']   = "Přehled linek";
$lang['historie']      = "Historie";
$lang['pohledridice']  = "Videozáznamy";
$lang['mistopis']      = "Místopis";
$lang['fotogalerie']   = "Fotogalerie";

// nadpisy v obsahu
$lang['funkce']           = "Funkce linky";
$lang['seznamzastavek']   = "Seznam zastávek";
$lang['mapa']             = "Mapa";
$lang['provoznilinky']    = "Provozované linky";
$lang['linky_akt_mimo']   = "Linky aktuálně mimo provoz";
$lang['neprovoznilinky']  = "Linky trvale mimo provoz";
$lang['linky_trvale_mimo'] = "Linky trvale mimo provoz";

// nové: hlášky a úvody
$lang['zalozkanedostupna']  = "Záložka bude teprve doplněna.";
$lang['historiecekana']     = "Historie linky čeká na své zpracování.";
$lang['videocakana']        = "Video linky čeká na své zpracování.";
$lang['mistopiscekana']     = "Místopisný článek čeká na své zpracování.";
$lang['fotogaleriecekana']  = "Fotogalerie čeká na své zpracování nebo nejsou známy fotografie této linky. Pokud nějaké máte, <a href='mailto:info@tomaskrupicka.cz'>kontaktujte mě prosím</a>.";
// Šablonové texty z DB sloupce fotogalerie (lokalizace přes strtr ve fotogalerie.php).
$lang['fotogalerie_klik']   = "Pro zobrazení galerie klikněte zde";
$lang['fotogalerie_nejsou'] = "Bohužel nejsou známy fotografie této linky. Pokud nějaké máte a můžete je sdílet <a href='mailto:info@tomaskrupicka.cz'>kontaktujte mě prosím</a>.";
$lang['fotogalerie_alt']    = "Fotografie linky";

// chyby DB / AJAX
$lang['err_db']            = 'Došlo k chybě při připojení k databázi. Zkuste to prosím později.';
$lang['err_db_prepare']   = 'Došlo k chybě při přípravě dotazu. Zkuste to prosím později.';
$lang['err_ajax_tabload']  = 'Obsah záložky se nepodařilo načíst. Zkuste obnovit stránku.';
$lang['mapa_nedostupna']   = 'Mapový podklad není k dispozici.';
$lang['mapa_iframe_title'] = 'Mapa linky %s';

// stránka Mapa sítě (mapa.php / mapa.js)
$lang['mapa_linky']          = 'Linky';
$lang['mapa_nav']            = 'Živá mapa';
$lang['mapa_titulek']        = 'Živá mapa MHD Liberec | Liberecké linky';
$lang['mapa_popis']          = 'Interaktivní mapa linek a zastávek MHD v Liberci a Jablonci nad Nisou nad otevřenými daty (GTFS).';
$lang['mapa_pozn_poloha']    = 'Polohy vozidel jsou orientační, podle jízdního řádu – veřejná GPS data nejsou k dispozici.';
$lang['mapa_hledat']         = 'Hledat linku nebo zastávku…';
$lang['mapa_zastavky']       = 'Zastávky';
$lang['mapa_panel']          = 'Panel';
$lang['mapa_skryt_panel']    = 'Skrýt panel';
$lang['mapa_zobrazit_panel'] = 'Zobrazit panel';
$lang['mapa_snap_titulek']   = 'Síť MHD Liberec %s | Liberecké linky';
$lang['mapa_snap_badge']     = 'Historická síť %s';
$lang['mapa_snap_live']      = 'živá mapa';
$lang['mapa_snap_nav']       = 'Historie';
$lang['mapa_hledat_linku']   = 'Hledat linku…';
$lang['mapa_hledat_zastavku'] = 'Hledat zastávku…';
$lang['mapa_tram']           = 'Tramvaje';
$lang['mapa_bus']            = 'Autobusy';
$lang['mapa_vse']            = 'Provozní';
$lang['mapa_filtr_mimo']     = 'Mimo provoz';
$lang['mapa_filtr_historicke'] = 'Historické';
$lang['mapa_nic']            = 'Nic';
$lang['mapa_zastavka']       = 'Zastávka';
$lang['mapa_zona']           = 'Zóna';
$lang['mapa_bezbarierova']   = 'Bezbariérová';
$lang['mapa_linky_zde']      = 'Linky v zastávce';
$lang['mapa_zastavky_linky'] = 'Zastávky linky';
$lang['mapa_zpet']           = 'Zpět';
$lang['mapa_zobraz_linku']   = 'Zobrazit jen tuto linku';
$lang['mapa_ano']            = 'ano';
$lang['mapa_ne']             = 'ne';
$lang['mapa_neznamo']        = 'neznámo';

// integrace: nadpis, náhled v záložce, prolinky mapa↔detail
$lang['mapa_sit']            = 'MAPA SÍTĚ';
$lang['mapa_zobraz_v_siti']  = 'Zobrazit v interaktivní mapě';
$lang['mapa_detail_linky']   = 'Detail a historie linky';
$lang['mapa_nahled_alt']     = 'Náhled trasy linky %s';
$lang['mapa_mimo_provoz_pozn'] = 'Trasa je přibližná.';
$lang['mapa_mimo_nadpis']      = 'Linka %s (trvale mimo provoz)';
$lang['mapa_hist_nadpis']      = 'Historická linka %s';
$lang['mapa_akt_mimo']         = 'aktuálně mimo provoz';
$lang['mapa_zanikla']          = 'zaniklá zastávka';
$lang['mapa_smer']             = 'Směr %s';
$lang['mapa_vozidla']          = 'Vozidla';
$lang['mapa_barevne_linky']    = 'Barevné linky';
$lang['mapa_odjezdy']          = 'Odjezdy';
$lang['mapa_odjezdy_zadne']    = 'Odsud teď nic nejede.';
$lang['mapa_jr_nedostupny']    = 'Jízdní řád pro dnešek není k dispozici.';
$lang['mapa_jr_spoje']         = 'Jízdní řád spoje';

// legenda kategorií linek na mapě (popisky ke kategoriím v typy_linek)
$lang['mapa_legenda']        = 'Kategorie linek';
$lang['mapa_kat_vylukova']   = 'Výlukové';
$lang['mapa_kat_tramvaje']   = 'Tramvaje';
$lang['mapa_kat_autobusy']   = 'Autobusy';
$lang['mapa_kat_nocni']      = 'Noční';
$lang['mapa_kat_pracovni']   = 'Pracovní';
$lang['mapa_kat_skolni']     = 'Školní';
$lang['mapa_kat_nakupni']    = 'Komerční';
$lang['mapa_kat_historicke'] = 'Historické';

// singulární názvy kategorií pro nadpis linky v přehledu ("Kategorie číslo: trasa")
$lang['mapa_katsg_vylukova']   = 'Výluková linka';
$lang['mapa_katsg_tramvaje']   = 'Tramvaj';
$lang['mapa_katsg_autobusy']   = 'Autobus';
$lang['mapa_katsg_nocni']      = 'Noční linka';
$lang['mapa_katsg_pracovni']   = 'Pracovní linka';
$lang['mapa_katsg_skolni']     = 'Školní linka';
$lang['mapa_katsg_nakupni']    = 'Komerční linka';
$lang['mapa_katsg_historicke'] = 'Historická linka';
$lang['mapa_katsg_mimoprovoz'] = 'Linka mimo provoz';

// (volitelné) úvodní texty, když není vybraná linka
$lang['prehled_intro']      = "Na této záložce najdete základní informace o dané lince. Pokračujte výběrem linky v horním menu.";
$lang['historie_intro']     = "Na této záložce najdete historii vývoje dané linky. Pokračujte výběrem linky v horním menu.";
$lang['pohledridice_intro'] = "Na této záložce najdete video linky zachycené z kabiny řidiče a krátký komentář. Pokračujte výběrem linky v horním menu.";
$lang['mistopis_intro']     = "Na této záložce najdete místopisné články o místech, kterými daná linka projíždí. Pokračujte výběrem linky v horním menu.";
$lang['fotogalerie_intro']  = "Na této záložce najdete odkaz na fotogalerii vážící se k dané lince. Pokračujte výběrem linky v horním menu.";

// navigace pro řidiče (navigace.php, varianty tras jako GPX)
$lang['nav_nav']          = 'Navigace';
$lang['nav_titulek']      = 'Trasy autobusových linek do navigace (GPX) | Liberecké linky';
$lang['nav_popis']        = 'Všechny varianty tras autobusových linek MHD Liberec a Jablonec n. N. ke stažení jako GPX do navigace, denně aktualizované podle jízdního řádu (GTFS).';
$lang['nav_nadpis']       = 'Trasy linek do navigace';
$lang['nav_intro']        = 'Každá varianta trasy autobusové linky je ke stažení jako soubor GPX: skutečná trasa podle jízdního řádu a zastávky v pořadí. Soubory se automaticky obnovují podle aktuálních dat DPMLJ, odkaz na variantu se nemění. Zkrácené spoje jsou zahrnuté v delší variantě.';
$lang['nav_navod']        = 'Jak na to: soubor GPX otevřete v telefonu v aplikaci OsmAnd a spusťte navigaci po trase. Mapy.com trasu umí jen zobrazit. Načtená trasa je v telefonu kopie: po změně varianty ji načtěte znovu.';
$lang['nav_platnost']     = 'Data platná %s až %s.';
$lang['nav_vyber']        = 'Vyberte linku';
$lang['nav_zmeny']        = 'Poslední změny variant';
$lang['nav_zadne_zmeny']  = 'Zatím bez zaznamenaných změn.';
$lang['nav_zmena_nova']     = 'nová varianta';
$lang['nav_zmena_zastavky'] = 'změna zastávek';
$lang['nav_zmena_trasa']    = 'změna trasy';
$lang['nav_zmena_zrusena']  = 'zrušena';
$lang['nav_linka']        = 'Linka %s';
$lang['nav_variant_1']    = '%d varianta';
$lang['nav_variant_2']    = '%d varianty';
$lang['nav_variant_5']    = '%d variant';
$lang['nav_stahnout']     = 'Stáhnout GPX';
$lang['nav_vse_gpx']      = 'Všechny varianty linky v jednom GPX';
$lang['nav_zastavek']     = '%d zastávek';
$lang['nav_km']           = '%s km';
$lang['nav_spoju']        = 'spojů po celé trase: %d';
$lang['nav_zkracene']     = 'zkrácených spojů: %d';
$lang['nav_dny_po-pa']    = 'Po–Pá';
$lang['nav_dny_so']       = 'So';
$lang['nav_dny_ne']       = 'Ne';
$lang['nav_jen_od']       = 'jede od %s';
$lang['nav_jen_do']       = 'jede do %s';
$lang['nav_priblizna']    = 'Trasa je přibližná (spojnice zastávek), GTFS pro ni nemá geometrii.';
$lang['nav_zastavky']     = 'Zastávky';
$lang['nav_qr']           = 'Naskenujte telefonem a otevřete tuto stránku tam.';
$lang['nav_zpet']         = 'Všechny linky';
$lang['nav_zrusena_nadpis'] = 'Varianta už nejede';
$lang['nav_zrusena_text']   = 'Varianta „%s“ linky %s není od %s v jízdním řádu. Aktuální varianty linky najdete níže.';
$lang['nav_nenalezeno']   = 'Tato trasa ani linka v datech není.';
$lang['nav_mapa_odkaz']   = 'Trasa do navigace (GPX)';
$lang['nav_navod_odkaz'] = 'Podrobný návod';
// návod k GPX – HTML (statický text, vypisuje se bez escapování)
$lang['nav_navod_html'] = <<<'HTML'
<h3>Jak soubor GPX použít</h3>
<p>Soubor GPX obsahuje trasu varianty a zastávky v pořadí (jako body „1. Název“, „2. Název“…). Stáhnete ho tlačítkem <strong>Stáhnout GPX</strong> u varianty. Telefon ho uloží do Stažených souborů nebo rovnou nabídne aplikaci, ve které ho otevřít.</p>
<details open>
  <summary><strong>OsmAnd: navigace po trase</strong> (Android i iPhone, doporučeno)</summary>
  <ol>
    <li>Nainstalujte OsmAnd (Google Play / App Store) a v aplikaci si stáhněte offline mapu Libereckého kraje.</li>
    <li>Na této stránce klepněte na <strong>Stáhnout GPX</strong> a soubor otevřete v OsmAnd. Pokud ho telefon jen uloží, otevřete ho ze Stažených souborů a zvolte OsmAnd, nebo v OsmAnd: <em>Menu → Moje místa → Stopy → Importovat</em>.</li>
    <li>Klepněte na trasu (nebo ji vyberte v seznamu stop) a zvolte <strong>Sledovat trasu</strong> (anglicky <em>Follow track</em>).</li>
    <li>V nastavení sledování nechte volbu <strong>Připojit k silnicím</strong> vypnutou. Jinak by aplikace trasu přepočítala jako pro auto a nemusela by projet buspruhem nebo průjezdem „mimo BUS“.</li>
    <li>Zvolte, jestli vás má aplikace navést na začátek trasy, nebo na nejbližší bod (hodí se, když vyjíždíte z vozovny nebo nastupujete do trasy jinde).</li>
    <li>Spusťte navigaci. Hlasové pokyny se řídí tvarem trasy, u trasy bez křižovatkových údajů mohou ohlašovat i běžné zatáčky.</li>
  </ol>
</details>
<details>
  <summary><strong>Mapy.com: jen zobrazení trasy</strong></summary>
  <ol>
    <li>Import vyžaduje přihlášení k účtu Mapy.com.</li>
    <li>V aplikaci otevřete <em>Místa a trasy</em> (nebo <em>Moje Mapy</em>) a zvolte <strong>Import GPX</strong>, případně soubor otevřete ze Stažených souborů v aplikaci Mapy.com. Na počítači lze soubor na mapy.com přetáhnout do mapy, trasa se pak objeví i v telefonu pod stejným účtem.</li>
    <li>Trasa a zastávky se ukážou v mapě jako vodítko. Hlasovou navigaci po importované trase Mapy.com nenabízí; když necháte Mapy.com naplánovat trasu samy, povedou vás jako auto, ne po trase linky.</li>
  </ol>
</details>
<details>
  <summary><strong>Tipy</strong></summary>
  <ul>
    <li>Trasy si stáhněte předem, ne za jízdy. Telefon mějte v pevném držáku.</li>
    <li>Soubor <em>Všechny varianty linky v jednom GPX</em> se hodí k prohlédnutí, kudy linka jezdí. Pro navigaci použijte konkrétní variantu, jinak aplikace neví, kterou trasou vést.</li>
    <li>Každá varianta je jen jedním směrem. Zpáteční směr je samostatná varianta.</li>
    <li>Načtená trasa se v telefonu sama neaktualizuje. Když je u linky v části <em>Poslední změny variant</em> nový záznam, stáhněte variantu znovu a starou v aplikaci smažte. Adresa souboru zůstává stejná.</li>
    <li>Trasa vychází z jízdního řádu DPMLJ (GTFS) a může být zjednodušená. Vždy platí dopravní značení a pokyny dispečinku.</li>
    <li>Názvy položek v aplikacích se mohou podle verze mírně lišit.</li>
  </ul>
</details>
HTML;
