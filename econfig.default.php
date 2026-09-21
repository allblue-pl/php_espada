<?php

defined( '_ESPADA' ) or die;

define('EDEBUG', true);
define('ERROR_REPORTING', true);

define('ECONFIG_TYPE', 'local, dev');

define('NO_ACCESS', 'No access.');
define('INTERNAL_ERROR_MESSAGE', 'Internal error.');

define('SITE_DOMAIN', 'http://localhost');
define('SITE_BASE', '/');

define('PATH_ESPADA', __DIR__ . '/esite/espada');

define('PATH_ESITE', __DIR__ . '/esite');
define('URI_ESITE', '/esite/');

define('PATH_CACHE', __DIR__ . '/cache/espada');

define('PATH_DATA', __DIR__ . '/data/espada');

define('PATH_MEDIA', __DIR__ . '/media/espada');
define('URI_MEDIA', '/media/espada/');

define('PATH_PRESETS', __DIR__ . '/presets/espada');

define('PATH_TMP', __DIR__ . '/build/espada');
define('URI_TMP', '/build/espada/');

define('EPACKAGES', 'site, ecore');
