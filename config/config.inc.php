<?php
chdir(realpath(dirname(__FILE__)."/../"));
$start_time = microtime(true);
if(!ini_get("zlib.output_compression")){
	ob_start("ob_gzhandler");
	ob_start();//two buffer for debug control
}
else{
	ob_start();
}
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Cache-control: private', false); // IE 6 FIX
header('Pragma: no-cache');
ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . realpath(dirname
	(__file__) . '/..') . PATH_SEPARATOR . realpath(dirname(__file__) . '/../lib'));
set_time_limit(3600);
error_reporting(E_ALL^E_NOTICE);
ini_set("memory_limit", "2000M");
date_default_timezone_set("America/Bogota");
require_once "DBNative.php";
require_once "utilities.php";
// CONFIG DB
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_SERVER", "localhost");
define("DB_NAME", "LIBROSOFT");
define("DB_USER_LOCAL", "root");
define("DB_PASS_LOCAL", "");
define("DB_SERVER_LOCAL", "localhost");
define("DB_NAME_LOCAL", "LIBROSOFT");
// END CONFIG
// Template PATHs
define("FRONTEND_PATH_TEMPLATES", "web/templates");
define("FRONTEND_PATH_CONTROLLERS", "controller");
if (in_array($_SERVER['SERVER_ADDR'], array("127.0.0.1", "localhost")))
{
	define("DSN", "mysql://" . DB_USER_LOCAL . ":" . DB_PASS_LOCAL . "@" .
		DB_SERVER_LOCAL . "/" . DB_NAME_LOCAL);
} else
	define("DSN", "mysql://" . DB_USER . ":" . DB_PASS . "@" . DB_SERVER . "/" .
		DB_NAME);
// Database Connection
$con = new DBNative(DSN);
// UTF-8 Setup
$utf8 = $con->query("SET NAMES 'utf8'");
$utf81 = $con->query("SET character_set_results = 'utf8'");
$utf82 = $con->query("SET character_set_client = 'utf8'");
$utf83 = $con->query("SET character_set_connection = 'utf8'");
$utf84 = $con->query("SET character_set_database = 'utf8'");
$utf85 = $con->query("SET character_set_server = 'utf8'");
foreach (glob("lib/*.php") as $fileName)
{
	require_once $fileName;
}
foreach (glob("lib/model/*.php") as $fileName)
{
	require $fileName;
}
?>
