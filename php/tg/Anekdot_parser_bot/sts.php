<style>
.log {
  overflow-wrap: normal;  /* не поддерживает IE, Firefox; является копией word-wrap */
  word-wrap: break-word;
  word-break: keep-all;  /* не поддерживает Opera12.14, значение keep-all не поддерживается IE, Chrome */
  line-break: auto;  /* нет поддержки для русского языка */
	hyphens: manual;  /* значение auto не поддерживается Chrome */
	border: inset 1px #eee;
}
</style>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

define("TG_TEST", 1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";


define("DIR", Path::fromRootStat(__DIR__));

// require_once "../botFuncs.php"; //

require_once "../tg.class.php";

TG::log(['echo "Before declaration \$tg"',], __FILE__, __LINE__);

// $log = '';
$tg = new TG('1052237188:AAFIh-yeUO05Qv--LfAGaJnFmo8vvT9jDjY');

TG::log(['echo "END of ' . basename(__FILE__) . '"',], __FILE__, __LINE__);
