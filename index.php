<?php defined('_ESPADA') or die(NO_ACCESS);

define('PREINIT_CONTENTS', ob_get_contents());
ob_end_clean();

/* Defines */
if (!defined('EDEBUG'))
	define('EDEBUG', false);

define('PATH_DATA', PATH_ESITE . '/data');
define('PATH_OVERWRITES', PATH_ESITE . '/overwrites');
define('PATH_SITE', PATH_ESITE . '/site');

/* Error Reporting */
if (ERROR_REPORTING) {
	ini_set('display_errors', 1);
	error_reporting(-1);
} else {
	ini_set('display_errors', 0);
	error_reporting(0);
}

/* Espada */
require(__DIR__ . "/core/Espada.php");

Espada::Create();
