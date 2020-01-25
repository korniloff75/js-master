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

<?php require_once 'mod_auth.php' ?>
<?php # require_once 'mod_menu.php' ?>

<div class="main uk-flex uk-flex-wrap">
		<!-- uk-flex-column -->
	<div class="uk-flex uk-flex-wrap uk-flex-left uk-padding-small uk-width-1-4@s uk-flex-wrap-top uk-button-group">
		<div id="bot_box" uk-sticky="show-on-up:true; offset:5; top:50; animation: uk-animation-slide-top"></div>
	</div>

	<div id="content" class="uk-flex-1">
		<!-- uk-switcher  -->
		<div id="chartbox" class="uk-flex">
		<!-- Charts -->
		</div>
	</div>

</div><!-- /.main -->


<footer>
	<?=$_js_compress?>
</footer>

</body>
</html>