<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Авторизация</title>
	<link rel="stylesheet" href="/<?=Site::getPathFromRoot(__DIR__)?>/style.css">
</head>


<body>

	<div class="top">
		<a href="/">Главная</a>

		<div class="clr"></div>
	</div>

	<form method="post" action="" class="login">
		<input type="hidden" name="action" value="authorize">
		<p>
			<label for="login">Логин:</label>
			<input type="text" name="login" id="login" value="">
		</p>

		<p>
			<label for="pswd">Пароль:</label>
			<input type="password" name="pswd" id="pswd">
		</p>

		<p class="login-submit">
			<button type="submit" class="login-button">Войти</button>
		</p>

		<!-- <p class="forgot-password">
			<a href="index.html">Забыл пароль?</a> -->
		</p>
	</form>
</body>

</html>