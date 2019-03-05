<h2>Описание слайдера</h2>

<div>
	<p>Очень простой слайдер, безо всяких анимаций смены изображений. Практически не нагружает броузер. Отлично подойдёт для шапки сайта или блога.</p>
	<p>Для ещё большего уменьшения влияния кода на поведение страницы, смена изображений прекращается, как только слайдер выходит из зоны видимости окна броузера при прокрутке страницы.</p>
	<p>Слайдер выводит изображения в случайном порядке из указанной в настройках директории.</p>
</div>


<?php
	$iPath = "userfiles/images/examples/";
#	echo $iPath;

	$imgs= scandir($iPath);
	foreach ($imgs as $i => &$f) {
		if(!is_file($iPath . $f)) unset($imgs[$i]);
	}
	$imgs = array_values($imgs);
	// print_r($imgs);
?>

<h3>Пример работы скрипта</h3>

<div id="prim">
	<style>
	#headSlider > img {
		max-width: 100%;
		border: 1px solid #999;
		border-radius: 0 0 5px 5px;
		transform-origin: 50% 80%;
		transform: perspective(500px) rotate3d(1, 0, 0, 10deg);
	}
	</style>

	<div id="headSlider" class="container">
		<img src="/<?=$iPath . $imgs[0]?>" alt="headSlider">
	</div>

	<script type="text/javascript">
	(function() {
		'use strict';
		// Настройки скрипта
		var headImg = document.querySelector('#headSlider>img'),
		// Интервал смены изображения - сек.
		delay = 5,
		// Путь к папке с изображениями
		imgFolder = "/<?=$iPath?>",
		// Массив с именами изображений
		// можно получить серверным скриптом
		headSlides = <?= json_encode($imgs); ?>,
		slideStart = setInterval(slide, delay * 1000);

		// console.log(headSlides);

		function slide () {
			headImg.src= imgFolder + headSlides[Math.round(Math.random() * (headSlides.length - 1))];
			// console.log(headImg.src);
		}

		window.addEventListener('scroll', function(e) {
			var imgCR = headImg.getBoundingClientRect();

			if (document.documentElement.clientHeight - imgCR.top - imgCR.height > 0
			&& imgCR.top > 0
			) {
				slideStart= slideStart || setInterval(slide, delay * 1000);
			} else slideStart= clearInterval(slideStart);

		});
		headImg.onerror= function() {
			slideStart = clearInterval(slideStart);
		}
	})();
	</script>

</div>
<!-- /headSlider -->


<h4>Подключение и настройки скрипта (исходный код примера)</h4>
<pre><code for="#prim"></code></pre>