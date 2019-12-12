<?php
// require_once "../../Path.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

require_once "../botFuncs.php";

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

define("TOKEN", '1052237188:AAFIh-yeUO05Qv--LfAGaJnFmo8vvT9jDjY');
define("DIR", Path::fromRootStat(__DIR__));