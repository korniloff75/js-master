<?php
/**
 * Полезные ссылки:
 ** http://51.15.11.82:8225/api/doc
 ** https://coding.studio/tchart/
*/

session_start();
$_SESSION['DEV'] = $_GET['_dev'] ?? $_SESSION['DEV'] ?? false;

/* $_SESSION['DEV'] = empty($_REQUEST['DEV']) ? (
	$_SERVER['HTTP_HOST'] === 'js-master'
	|| $_SESSION['DEV'] ?? ($_GET['id'] ?? $_SESSION['authData']['id'] ?? null) == 673976740
) : $_REQUEST['DEV'] */;

define('DEV', $_SESSION['DEV']);
define('REMOTE', [
	'url' => 'http://51.15.11.82:8225/api/v1/'
]);

//* Data from TGLogin
if(!empty($_GET['id']))
{
	$proofUrl = REMOTE['url'] . 'login';

	$_SESSION['GETstring'] = http_build_query($_GET);
	$_SESSION['authData'] = $_GET;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $_GET);
	curl_setopt($ch, CURLOPT_URL, $proofUrl);
	$response = curl_exec($ch);
	curl_close($ch);

	$respArr = json_decode($response, 1);

	//* Бэк вернул данные
	if($respArr['status'] && empty($respArr['bots']))
	{
		die("\nДля этой учётной записи зарегистрированных ботов нет.");
	}
	elseif($respArr['status'] && $respArr['bots'])
	{
		$_SESSION['bots'] = json_encode($respArr['bots'], JSON_UNESCAPED_UNICODE);

		if(!empty($respArr['authData']))
			setcookie('authData',json_encode($respArr['authData'], JSON_UNESCAPED_UNICODE));

		// var_dump(json_encode($respArr['bots'], JSON_UNESCAPED_UNICODE));
		// var_dump($respArr['bots']);
	}
	//* Fail
	else
	{
		echo '<b>Response</b> = ' . $response;
		die("Данных по ботам нет!");
	}

	// todo 4 test
	// file_put_contents('proof.json', $response);
	// echo "Бэк вернул:";
	// var_dump($response);

	// var_dump($_SERVER);

	$collectURL = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]);

	//! Redirect
	header("Location: $collectURL");

	die('Редирект с очисткой гетов');
}


//* Выводим после редиректа

define('LOCAL', stripos($_SERVER['HTTP_HOST'], '\.ru', -4) === false);
// define('ROOT', stripos($_SERVER['HTTP_HOST'], '\.ru') === false);

require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";
$log = new Logger(basename(__DIR__));
function L(string $message, $level=null, $dump=[])
{
	global $log;
	$log->add($message, $level, $dump);
}
// Log::getInstance(;

//* Collect server data 4 template
ob_start();
?>

<script>
var _remoteUrl = "<?=REMOTE['url']?>";
<?php
if(!empty($_SESSION['bots'])):
	echo 'var _bots = ' . urldecode($_SESSION['bots']) . '; var _authData = ' . urldecode(json_encode($_SESSION['authData'], JSON_UNESCAPED_UNICODE));
?>
	;console.log('_bots = ', _bots, "\n_authData = ", _authData);
<?php endif;?>
</script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.2.3/js/uikit.min.js"></script> -->
<script src="lib/uikit3.3.0/uikit.min.js"></script>
<!-- https://hammerjs.github.io/touch-action/ -->
<script src="lib/hammer.min.js"></script>

<?php
//* Compress header JS

if(DEV):
	include_once 'js_compress.php';
	js_compress('js_compress_header');
?>
	<script src="js_compress_header/1.hammerPluginJq.js"></script>
<?php else:?>
	<script src="js_compress_header.js"></script>
<?php endif;
$_servData = ob_get_clean();


function getCurrentTemplate ($name)
{
	list($n, $ext) = explode('.', $name);
	return "templates/{$n}" . (!empty($_GET["_t"])? "_{$_GET["_t"]}": '') . ".{$ext}";
}


//* Switch Render
$_curJsRender = getCurrentTemplate('Render.js');
//* Switch template
$_curTemplate = getCurrentTemplate('template.php');

if(!file_exists($_curTemplate))
	die('Template <b>' . basename($_curTemplate) . '</b> NOT exist!');

//* Compress defer JS
ob_start();
?>
	<script src="<?=$_curJsRender?>"></script>
	<script src="BotList.js"></script>
	<pre style="white-space:pre-wrap;">
<?php
if(DEV):
	js_compress('js_compress');
	echo @"\n?{$_SESSION['GETstring']}\n";
	// echo "\$_SESSION['respArr']";
	// var_dump($_SESSION['respArr']);
?>

	<h3>Ответ сервера</h3>
	<pre id="response"></pre>
	</pre>
	<script src="js_compress/1.GetResponse.js" defer></script>
	<script src="js_compress/10.tchart_kff.js" defer></script>
<?php else:?>
	<script src="js_compress.js" defer></script>
<?php endif;?>

<div id="preloader" class="uk-position-center" uk-spinner="ratio: 2"></div>

<!-- UIkit CSS -->
<link rel="stylesheet" href="lib/uikit3.3.0/uikit.min.css" />

<script defer src="lib/uikit3.3.0/uikit-icons.min.js" async></script>
<!-- /UIkit JS -->

<!-- tchart.css -->
<link rel="stylesheet" href="tchart.css">
<!-- /tchart.css -->
<?php
$_js_compress = ob_get_clean();


//* Collect content
ob_start();

// var_dump($_SERVER);
// require_once 'BotList.php';
$content = ob_get_clean();



/* if(!file_exists($_curTemplate))
	die('Template <b>' . basename($_curTemplate) . '</b> NOT exist!'); */


//* Output buffer in template
header('Content-type: text/html; charset=utf-8');
require_once $_curTemplate;

die;