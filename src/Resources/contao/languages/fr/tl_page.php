<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_page']['useComatrack']       = array('Activer le tracking en arrière-plan', '');
$GLOBALS['TL_LANG']['tl_page']['comatrack_url']      = array('URL de l\'installation Matomo', 'Renseigner l\'adresse de base de l\'installation Matomo (ex: https://matomo.example.com)');
$GLOBALS['TL_LANG']['tl_page']['comatrack_id']       = array('ID du site dans Matomo', 'Tragen Sie hier die ID der in Matomo angelegten Website ein');
$GLOBALS['TL_LANG']['tl_page']['comatrack_token']    = array('Token de sécurité Matomo', 'Le token est utilisé pour le filtrage des IP et les informations GeoIP (si disponible).');
$GLOBALS['TL_LANG']['tl_page']['comatrack_debug']    = array('DEBUG Mode', 'Attention. En mode DEBUG, de nombreuses données sont signalées dans le journal système. Dans le fonctionnement actif, désactivez-le dans tous les cas !');
$GLOBALS['TL_LANG']['tl_page']['comatrack_404']      = array('Traquer les erreurs 404', 'Voulez-vous que les accès aux pages d\'erreur, c\'est-à-dire lorsque l\'URL n\'est pas connue, soient traquées dans Matomo ?');
$GLOBALS['TL_LANG']['tl_page']['comatrack_dnt']      = array('Respecter le Header "DoNotTrack"', 'Les utilisateurs peuvent définir une information "DoNotTrack" dans le navigateur. Voulez-vous le respecter ?');
$GLOBALS['TL_LANG']['tl_page']['comatrack_dim_dnt']  = array('Info additionnelle pour DoNotTrack', 'Si DoNotTrack ne doit pas être pris en compte mais que cette information doit être stockée dans une CustomDimension, veuillez saisir ici l\'ID de la dimension');
$GLOBALS['TL_LANG']['tl_page']['comatrack_ip']       = array('Transmettre l\'IP à Matomo', 'L\'adresse IP doit-elle être transmise à Matomo ? Recommandation : pas de transfert, suivre l\'IP en tant que 127.0.0.1');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_page']['comatrack_legend']      = 'Traçage d\'arrière-plan avec Matomo';