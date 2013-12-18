<?php

error_reporting(E_ALL);

if (!defined('SEP')) {
	define('SEP', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT_APP')) {
	define('ROOT_APP', dirname(dirname(__FILE__)) . SEP);
}
if (!defined('ROOT_LIB')) {
	define('ROOT_LIB', ROOT_APP . 'apps' . SEP . 'library' . SEP . 'Core' . SEP);
}
if (!defined('CONFIG_SOURCE')) {
	define('CONFIG_SOURCE', ROOT_APP . 'config' . SEP . 'config.php');
}
if (!defined('DATABASE_CONFIG_SOURCE')) {
	define('DATABASE_CONFIG_SOURCE', ROOT_APP . 'config' . SEP . 'database.php');
}

require_once ROOT_LIB . 'Application.php';

try {
	$application = new Application();
	$application -> run();
	
	echo $application -> getOutput();
	
} catch (Exception $e) {
	throw $e;
} 