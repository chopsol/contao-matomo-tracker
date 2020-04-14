<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['useComatrack']       = array('Background Tracking aktivieren', '');
$GLOBALS['TL_LANG']['tl_page']['comatrack_url']      = array('URL der Matomo-Installation', 'Bitte geben Sie hier die Basis-URL der Matomo-Installation ein');
$GLOBALS['TL_LANG']['tl_page']['comatrack_id']       = array('Site-ID in Matomo', 'Tragen Sie hier die ID der in Matomo angelegten Website ein');
$GLOBALS['TL_LANG']['tl_page']['comatrack_token']    = array('Token für Matomo', 'Der Token wird u.a. für IP-Filterung und GeoIP-Informationen (sofern vorhanden) benötigt.');
$GLOBALS['TL_LANG']['tl_page']['comatrack_debug']    = array('DEBUG Modus', 'Achtung. Im Debug-Modus werden sehr viele Daten im Systemlog gemeldet. Im Aktiven Betrieb in jedem Fall deaktivieren!');
$GLOBALS['TL_LANG']['tl_page']['comatrack_404']      = array('Fehler 404 tracken', 'Sollen Zugriffe auf Fehlerseiten, d.h. wenn eine URL nicht bekannt ist, in Matomo getrackt werden?');
$GLOBALS['TL_LANG']['tl_page']['comatrack_dnt']      = array('DoNotTrack respektieren', 'Benutzer können im Browser eine DoNotTrack-Informationen festlegen. Soll diese respektiert werden?');
$GLOBALS['TL_LANG']['tl_page']['comatrack_dim_dnt']  = array('DoNotTrack tracken', 'Wenn DoNotTrack nicht berücksichtigt werden soll aber diese Information in einer CustomDimension gespeichert werden soll, geben Sie hier die Dimensions-ID an');
$GLOBALS['TL_LANG']['tl_page']['comatrack_ip']       = array('IP an Matomo übergeben', 'Soll die IP-Adresse an Matomo übergeben werden? Empfehlung: Keine Übergabe, IP als 127.0.0.1 tracken');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['comatrack_legend']      = 'Background-Tracking mit Matomo';