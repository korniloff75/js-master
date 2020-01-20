<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>BotStats</title>

	<link rel="stylesheet" href="tchart.css">
	<style>
	h3{
		text-align: center;
	}
	#response {
		min-width: 50%;
		min-height: 100px;
		border: inset 2px red;
		white-space: pre-line;
		word-break: break-all;
	}
	#preloader {
		position: absolute;
		left: 50%;
		top: 50%;
		width: 48px;
		height: 48px;
		margin-left: -24px;
		margin-top: -24px;
		border: #31a9df 3px solid;
		border-left: none;
		border-radius: 100%;
	}
	.rotate {
		animation-name: rotating;
		animation-duration: 1s;
		animation-iteration-count: infinite;
		animation-timing-function: linear;
	}
	@keyframes rotating {
		from {
		transform:rotate(0deg);
		}
		to {
		transform:rotate(360deg);
		}
	}
	#chartbox {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-around;
	}
	#chartbox > div {
		box-sizing: border-box;
		position: relative;
		margin-left: 0;
		margin-right: 0;
		margin-bottom: 64px;
		width: 45%;
		min-width: 400px;
		float: left;
	}
	#chartbox > div:last-child {
		width: 90%;
	}
	.tchart canvas{
		width: 100%;
	}
	</style>

	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.1/axios.min.js"></script> -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="tchart_kff.js"></script>
	<!-- <script src="tchart.min.js" type="application/javascript" defer></script> -->
</head>


<body>
<div id="preloader" class="rotate"></div>


<form action="http://51.15.11.82:8225/api/v1/" method="get" hidden>
<!-- <button data-method="get_user_data" data-params="user=303986717">get_user_data</button> -->
</form>

<h3>Ответ сервера</h3>
<pre id="response"></pre>

<h4>Auth</h4>
<a href="tg://AuthKffBot">Авторизироваться</a>

<div id="chartbox">
<!-- Charts -->
</div>


<script src="BotStats.js"></script>
</body>
</html>

<?php
/**
 * Полезные ссылки:
 ** http://51.15.11.82:8225/api/doc
 ** https://coding.studio/tchart/
*/

/* [
	[
		array unix time
		// даты/время по Х в секундах
	],
	[
		array values
		// Ось игрек
	]
] */
// $data = file_get_contents('');