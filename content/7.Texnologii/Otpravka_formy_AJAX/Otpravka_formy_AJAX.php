<p>Приведённый ниже код не претендует на лучшее решение Аякс-запроса серверу, но представляет собой достаточно простой пример его реализации. Основным преимуществом такого подхода является отсутствие необходимости в подключении сторонних библиотек. Всё работает в чистом нативе.</p>

<p>В реальном использовании рекомендуется добавить валидацию формы на клиенте перед её отправкой.</p>


<h2>Примеры работы</h2>

<h4>Отправляемая форма</h4>

<style>
#sform {
	text-align: center;
}
#sform > form {
	display: inline-block;
	border: 2px outset;
	padding: 5px;
}
#sform > form > * {
	display: block;
	margin: 15px 0;
}
</style>

<div id="sform">
	<form method="POST" name="f" action="/php/TesT.php" onsubmit="return false;">
		<label>Имя -- <input name="name" value="Вова" type="text"></label>
		<label>Фамилия -- <input name="family" value="Пупкин" type="text"></label>
		<label>Anything -- <input name="data" value="value" type="text"></label>
		<label>Вложение -- <input name="file" type="file"></label>
		<button name="submit" onclick="sendForm(event, this.form); return false;">Отправить</button>
	</form>
</div>

<pre><code for="#sform"></code></pre>


<h3>Отправка с нативным кодом</h3>

<div id="prim">
	<script type="text/javascript">
 	'use strict';
	function sendForm (e, f) {
		var button= e.target;
		e.preventDefault();

		// very stuped validation
		if(f.data.value.length) {
			var xhr = new XMLHttpRequest();
			// console.log('xhr= ', xhr);
			xhr.open('POST', f.action, true);
			var fS= new FormData(f);
			xhr.send(fS);
			console.log('fS= ', fS);

			xhr.onreadystatechange = function() {
				if (xhr.readyState != 4) {
					console.log('xhr.readyState= ', xhr.readyState);
					return;
				} else {
					button.textContent = 'Готово!';
					xhr.status == 200 && _H && _H.popup({
						empty: {html: xhr.response}
					});

					// Разблокировка кнопки по таймауту - опционально
					{
						setTimeout(function () {
							button.textContent = 'Отправить';
							button.disabled = false;
						}, 3000);
					}

				}

			}

			button.textContent = 'Загрузка...';
			button.disabled = true;
		}
	}
	</script>
</div>


<h4>Исходный код:</h4>

<pre><code for="#prim"></code></pre>



<h3>Отправка с jQ</h3>

<p>Данный подход будет удобен для тех, кто уже использует в своём сайте <b>jQuery</b>. С её помощью код становится короче, а результат - тот же.<br></p>

<div id="primJQ">
	<button onclick="sendForm_JQ($(document.forms.f), this); return false;">Отправить с jQ</button>

	<script type="text/javascript">
 	'use strict';
	function sendForm_JQ ($f, button) {
		button.textContent = 'Загрузка...';
		$.post($f.attr('action'), $f.serialize()
		).done(function(r) {
			button.textContent = 'Готово!';
			button.disabled = true;
			_H && _H.popup({
				empty: {html: r}
			});
		});

	}

	</script>
</div>

<h4>Исходный код:</h4>
<pre><code for="#primJQ" data-lib="jQuery"></code></pre>