<?php

define('VERSION', 'KFF-2.0');

define('HOME', $_SERVER['DOCUMENT_ROOT'] . '/');

define('LOCALHOST', $_SERVER['SERVER_ADDR'] === '127.0.0.1');

define('HOST', $_SERVER['HTTP_HOST']);

define('DEMO', 0);

$isHttps = !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']);

define('BASE_URL', ($isHttps ? 'https' : 'http') . '://' . HOST . '/');

define('LANG', "ru");

define('CONT', 'content/');

define('SITEMAP', [

	'path' => HOME . 'sitemap.xml',

	'RSSpath' => HOME . 'rss.xml',

	'turbo' => 1,

	'expires' => 10, # сутки

	'gzip' => 7, # int 0...9 || false

]);

# Config

define('CF', [

	'date' => [

		'format' => "Y-m-d H:i:s",

		'delta' => 1510000000,

		'new' => 2592000,

		'newest' => 864000

	],

	// 'counter' => 0,

	'counter' => '<div id=LIcounter class=right></div>

	<script src="/js/modules/liveInternet.js"></script>'



]);



# LESS
// exper

define('UPD_LESS_FROM_BROUSER', 0);



// define('USE_BROWS_LESS', 0);

define('USE_BROWS_LESS', LOCALHOST ? 1 : 0);





# CUSTOM settings

define('ADM', '194.28.91.');

define('ADM_EMAIL', 'info@js-master.ru');

define('OWNER', [

	'name' => 'Корнилов Павел',

	'address' => 'Р. Крым',

	'email' => ['support@js-master.ru'],

	// 'phone' => '+79787767158'

]);



define('HOST_IP', '185.117.153.195');

define('TEMPLATE', 'templates/portfolio/');

define('SITENAME', 'Portfolio');

define('LOGO', SITENAME);

define('DESCRIPTION', 'Фронт-энд веб-программирование');

define('COORDS', [45.47574, 34.21895]);

define('SUBSITE', '');

define('SENIOR', !SUBSITE);

define('MODULES', [

	'comments' => 1,

	'EditDB' => 1,

	'Thumb' => [

		'enable' => 1,

		'alts' => ['alt1', 'alt2']

	]

]);



$_SERVER['SERVER_ADMIN'] = ADM_EMAIL;

