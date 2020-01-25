<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<?= $_servData ?>

	<title>BotStats</title>

	<style>
	body {background: #eee;}
	body, body * {max-width: 100%;}
	h3{text-align: center;}
	li {
		list-style: none;
	}
	#response {
		min-width: 50%;
		min-height: 100px;
		border: inset 2px red;
		white-space: pre-line;
		word-break: break-all;
	}
	/* #preloader {
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
	} */
	/* #chartbox {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-around;
	} */
	#chartbox > div[id] {display: none;}

	#chartbox > div[id].uk-active {display: flex;}

	#chartbox > div[id] > div {
		box-sizing: border-box;
		position: relative;
		margin-left: 0;
		margin-right: 0;
		margin-bottom: 64px;
		width: 45%;
		min-width: 350px;
		float: left;
	}
	#chartbox > div[id] > div:last-child {
		width: 90%;
	}
	.tchart canvas{
		width: 100%;
	}
	.note{
		width: 100%;
		min-height: 100px;
		font-size: 1.1em;
	}
	</style>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.1/axios.min.js"></script> -->

</head>


<body>
<form action="http://51.15.11.82:8225/api/v1/" method="get" hidden>
<!-- <button data-method="get_user_data" data-params="user=303986717">get_user_data</button> -->
</form>

<div class="main">
	<?php
		require_once 'mod_auth.php';
		require_once 'mod_menu.php'
	?>

	<div id="content">
		<div id="chartbox" class="uk-flex">
			<!-- Charts -->
		</div>
	</div>
</div>


<footer>
	<?=$_js_compress?>
</footer>

</body>
</html>