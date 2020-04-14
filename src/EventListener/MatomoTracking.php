<?php

namespace Chopsol\ContaoMatomoTracker\EventListener;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Database;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use MatomoTracker;
use BugBuster\BotDetection\ModuleBotDetection;

class MatomoTracking {

	/**
	 * @var ContaoFrameworkInterface
	 */
	private $framework;
	private $session;

	/**
	 * Constructor.
	 *
	 * @param ContaoFrameworkInterface $framework
	 */
	public function __construct(ContaoFrameworkInterface $framework, SessionInterface $session) {

		$this->framework = $framework;
		$this->session = $session;
	}

	function onKernelRequest(RequestEvent $requestEvent) {

		$request = $requestEvent->getRequest();

		// Ist das Framework oder das pageModel nicht vorhanden (z.B. im Backend)
		// brauchen wir auch nichts tracken
		if( !$this->framework->isInitialized() || !$page = $request->attributes->get('pageModel') ) {
			return;
		}

		if ($rootPage = PageModel::findByPk($page->rootId)) {
			$GLOBALS['COMATRACK_INIT'] = false;
			if (isset($rootPage->useComatrack) && $rootPage->useComatrack == '1') {

				// Prüfung der Konfigurationsvariablen auf Existenz und Werte
				if ($rootPage->comatrack_url != '' && $rootPage->comatrack_id != '') {
					$GLOBALS['COMATRACK_INIT'] = true;
					$GLOBALS['COMATRACK_SETTINGS'] = array(
						'url' => $rootPage->comatrack_url,
						'id' => $rootPage->comatrack_id,
						'token' => (isset($rootPage->comatrack_token)?$rootPage->comatrack_token:''),
						'debug' => (isset($rootPage->comatrack_debug) && $rootPage->comatrack_debug=='1'?true:false),
						'404' => (isset($rootPage->comatrack_404)&&$rootPage->comatrack_404=='1'?true:false),
						'is_404' => ($page->type == 'error_404'?true:false),
						'dnt' => (isset($rootPage->comatrack_dnt)&&$rootPage->comatrack_dnt=='1'?true:false),
						'dnt_dim' => (isset($rootPage->comatrack_dim_dnt)?$rootPage->comatrack_dim_dnt:''),
						'ip' => (isset($rootPage->comatrack_ip)&&$rootPage->comatrack_ip=='1'?true:false)
					);
				} else {
					// Debug-Meldung das etwas nicht stimmt
					if (isset($rootPage->comatrack_debug) && $rootPage->comatrack_debug == '1') {
						\System::log('Background-Tracking: URL / ID fehlt', __METHOD__, TL_GENERAL);
					}
					return;
				}

				// Sessiondaten laden um zu prüfen ob der Nutzer im Backend angemeldet ist
				$sessionData = $this->session->all();
				// Ist der Nutzer im Backend angemeldet, tracken wir im Frontend nichts.
				if (isset($sessionData['_security_contao_backend'])) {
					if (isset($rootPage->comatrack_debug) && $rootPage->comatrack_debug == '1') {
						\System::log('Background-Tracking: User im Backend angemeldet', __METHOD__, TL_GENERAL);
					}
					$GLOBALS['COMATRACK_INIT'] = false;
					return;
				}

			}
		}

		// Als Initialwert für die Visitor-ID verwenden wir die Session-ID. Da sich diese im
		// laufe einer Sitzung ändern könnte, speichern wir uns unsere Visitor-ID in der Session
		// Matomo verwendet als Visitor-ID eine 16-stellige Hexadezimal-ID. Damit wir diese aus
		// der Session-ID generieren können verwende ich eine MD5 Summe der Session-ID und
		// kürze diese auf 16 Stellen.
		if (!isset($sessionData['comatrackVisitorId'])) {
			$this->session->set('comatrackVisitorId',substr(md5($this->session->getId()), 0, 16));
		}

		// Bot-Erkennung - Bots sollen nicht getrackt werden
		// Um die Bot-Detection innerhalb einer Nutzer-Session nur einmalig zu initialisieren
		// speichern wir die Information dazu ebenfalls in der Session.
		if (!isset($sessionData['comatrackIsBot'])) {
			$ModuleBotDetection = new ModuleBotDetection();
			if ($ModuleBotDetection->checkBotAllTests()) {
				$GLOBALS['COMATRACK_INIT'] = false;
				$GLOBALS['COMATRACK_ISBOT'] = true;
				$this->session->set('comatrackIsBot',true);
				return;
			}
			$this->session->set('comatrackIsBot',false);
		}
		elseif($sessionData['comatrackIsBot'] == true) {
			$GLOBALS['COMATRACK_INIT'] = false;
			$GLOBALS['COMATRACK_ISBOT'] = true;
			return;
		}

	}

	function onKernelTerminate(TerminateEvent $event) {

		// Prozesslaufzeit fuer eventuelles Debugging bereitstellen
		$gentime = false;
		if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
			$gentime = ceil((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000);
		}

		// Sonderfall; war der Aufruf von einem Bot und haben wir Debugging aktiv,
		// dann loggen wir den Zugriff um die Prozesslaufzeit sehen zu können
		if (isset($GLOBALS['COMATRACK_ISBOT']) && $GLOBALS['COMATRACK_ISBOT'] === true && $GLOBALS['COMATRACK_SETTINGS']['debug']) {
			\System::log('Background-Tracking: Bot skipped' . ($gentime ? " / " . ($gentime / 1000) . "s" : ''), __METHOD__, TL_GENERAL);
		}

		// Ist das Tracking nicht initialisiert (z.B. wegen einem Zugriff auf dem Backend)
		// oder ist es inaktiv (z.B. auf Grund der Settings) brauchen wir nichts weiter zu tun
		if (!isset($GLOBALS['COMATRACK_INIT']) || $GLOBALS['COMATRACK_INIT'] === false) {
			return;
		}

		// Soll eine 404 Seite nicht geloggt werden und handelt es sich um eine 404 Seite
		// Protokollieren wir das im Logging sofern aktiv, ansonsten brechen wir ab
		if (!$GLOBALS['COMATRACK_SETTINGS']['404'] && $GLOBALS['COMATRACK_SETTINGS']['is_404']) {
			if ($GLOBALS['COMATRACK_SETTINGS']['debug']) {
				\System::log('Background-Tracking: 404 skipped'.($gentime?" / ".($gentime/1000)."s":''), __METHOD__, TL_GENERAL);
			}
			return;
		}

		// Wenn Nutzer mit DNT Header nicht getrackt werden sollen, brechen wir ab...
		if ($GLOBALS['COMATRACK_SETTINGS']['dnt'] && isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == '1') {
			if ($GLOBALS['COMATRACK_SETTINGS']['debug']) {
				\System::log('Background-Tracking: DoNotTrack skipped'.($gentime?" / ".($gentime/1000)."s":''), __METHOD__, TL_GENERAL);
			}
			return;
		}

		// Tracking starten
		$matomoTracker = new MatomoTracker($GLOBALS['COMATRACK_SETTINGS']['id'], $GLOBALS['COMATRACK_SETTINGS']['url']);

		// Zur Übergabe der IP-Adresse wird das Token benötigt; andernfalls werden alle
		// Zugriffe mit der IP-Adresse des Servers protokolliert. Aus Datenschutz-Sicht
		// ist es also sinnnvoll kein Token zu verwenden.
		if ($GLOBALS['COMATRACK_SETTINGS']['token'] != '') {

			$matomoTracker->setTokenAuth($GLOBALS['COMATRACK_SETTINGS']['token']);

			// Einstellung zum Tracking der IP-Adresse
			if ($GLOBALS['COMATRACK_SETTINGS']['ip']) {
				$matomoTracker->setIp(\Environment::get('ip'));
			} else {
				$matomoTracker->setIp('127.0.0.1');
			}

			// GeoIP-Informationen übergeben sofern vom Server vorhanden
			if (isset($_SERVER['GEOIP_COUNTRY_CODE'])) {
				$matomoTracker->setCountry(strtolower($_SERVER['GEOIP_COUNTRY_CODE']));
			}
			if (isset($_SERVER['GEOIP_REGION'])) {
				$matomoTracker->setRegion($_SERVER['GEOIP_REGION']);
			}
			if (isset($_SERVER['GEOIP_CITY'])) {
				$matomoTracker->setCity($_SERVER['GEOIP_CITY']);
			}
			if (isset($_SERVER['GEOIP_LONGITUDE']) && isset($_SERVER['GEOIP_LATITUDE'])) {
				$matomoTracker->setLatitude($_SERVER['GEOIP_LATITUDE']);
				$matomoTracker->setLongitude($_SERVER['GEOIP_LONGITUDE']);
			}

		}

		// Bulk-Tracking wird verwendet um die Daten als POST an Matomo zu übergeben
		// damit keine Details des Trackings im Logfiles des Servers verbleiben
		$matomoTracker->enableBulkTracking();


		$sessionData = $this->session->all();
		$matomoTracker->setVisitorId($sessionData['comatrackVisitorId']);

		// Die aufgerufene URL bereitstellen
		$matomoTracker->setUrl(\Environment::get('uri'));

		// Tracking der Laufzeit des Skriptes bis hier hin. Da dieser Request als eines der letzten
		// Aktionen in Symfony erfolgt, sollte diese Zeit recht aussagekräftig sein.
		if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
			$matomoTracker->setGenerationTime((int)$gentime);
		}
		
		// DoNotTrack in einem CustomDimension schreiben sofern konfiguriert
		if (isset($GLOBALS['COMATRACK_SETTINGS']['dnt_dim']) && (int)$GLOBALS['COMATRACK_SETTINGS']['dnt_dim'] > 0) {
			if (isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == '1') {
				$matomoTracker->setCustomTrackingParameter('dimension'.(int)$GLOBALS['COMATRACK_SETTINGS']['dnt_dim'],'1');
			} else {
				$matomoTracker->setCustomTrackingParameter('dimension'.(int)$GLOBALS['COMATRACK_SETTINGS']['dnt_dim'],'0');
			}
		}

		// Den Pageview Tracken und den Titel der Seite mit übergeben
		$matomoTracker->doTrackPageView(html_entity_decode($GLOBALS['objPage']->pageTitle));

		// Für Debug-Zwecke werden die gesamten Aufrufe an Matomo geloggt - Debug sollte dementsprechend
		// immer deaktiviert sein
		if ($GLOBALS['COMATRACK_SETTINGS']['debug']) {
			foreach ($matomoTracker->storedTrackingActions as $log) {
				\System::log('Background-Tracking: '. $log, __METHOD__, TL_GENERAL);
			}
			\System::log("Background-Tracking: Laufzeit des Skripts: ".($gentime/1000)."s", __METHOD__, TL_GENERAL);
		}

		// Übersenden der Tracking-Infos an Matomo
		$response = $matomoTracker->doBulkTrack();
		// Die Antwort ist normalerweise ein JSON dessen Status wir prüfen können
		if (!$json = json_decode($response,true) || !isset($json['status']) || $json['status'] != "success") {
			if ($GLOBALS['COMATRACK_SETTINGS']['debug']) {
				\System::log('Background-Tracking fehlgeschlagen - Server-Antwort: '.$response, __METHOD__, TL_ERROR);
			}
		}
	}
}
