<?php
if(
	!\AJAX
	&& stripos($_SERVER['REQUEST_URI'], '/content/') !== false
){
$canonical= 'http' . (\Site::is('https')?'s':'') . '://' . $_SERVER["SERVER_NAME"] . str_replace('content/','site/',$_SERVER['REQUEST_URI']);

?>
<link rel="canonical" href="<?=$canonical?>"/>
<?php
}

// tolog(['$_SERVER'=>$_SERVER]);