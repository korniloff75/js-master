<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>BotStats</title>

	<style>
	body {background: #eee;}
	/* body, body * {max-width: 100%;} */
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

	#bot_box div[id] > div {
		box-sizing: border-box;
		position: relative;
		margin-left: 0;
		margin-right: 0;
		margin-bottom: 64px;
		width: 45%;
		min-width: 350px;
		float: left;
	}
	#bot_box div[id] > div:last-child {
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

	<script src=<?= LOCAL ? "/js/3.jquery-3.3.1.min.js" : "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"?>></script>

	<?= $_servData ?>

</head>


<body>

<?php require_once 'mod_auth.php' ?>

<div id="main" class="uk-section uk-padding">
		<!-- uk-flex-column -->
	<div id="bot_box" uk-accordion="animation:false;"></div>

</div><!-- /#main -->


<footer>
	<?=$_js_compress?>
</footer>

</body>
</html>