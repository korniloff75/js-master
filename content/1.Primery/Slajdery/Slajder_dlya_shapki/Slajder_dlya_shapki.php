<h3>Пример работы скрипта</h3>

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

<div id="prim">
	<style>
	#headSlider > img {
		max-width: 100%;
		border: 1px solid #999;
		border-radius: 0 0 5px 5px;
		transform-origin: 50% 80%;
		transform: perspective(500px) rotate3d(1, 0, 0, 10deg);
		opacity: 1;
	}
	#headSlider > img.hide {
		opacity: 0;
	}
	</style>

	<div id="headSlider" class="container">
		<img src="/<?=$iPath . $imgs[0]?>" alt="headSlider">
	</div>

	<script type="text/javascript">
	(function() {
		'use strict';
		// Настройки скрипта
		var
			headImg = document.querySelector('#headSlider>img'),
			headSliderBox = document.querySelector('aside#sidebar') || window,
			// Интервал смены изображения - сек.
			delay = 5,
			// Путь к папке с изображениями
			imgFolder = "/<?=$iPath?>",

			/* Массив с именами изображений
			// можно получить серверным php-скриптом:
			<\?php
			$iPath = "path/to/folder";
			$imgs= scandir($iPath);
			foreach ($imgs as $i => &$f) {
				if(!is_file($iPath . $f)) unset($imgs[$i]);
			}
			$imgs = array_values($imgs);
			// print_r($imgs);
			?> */
			headSlides = <?= json_encode($imgs); ?>,
			slideStart = setInterval(slide, delay * 1000);

		headImg.style.transition = "opacity " + delay + "s";
		// console.log(headSlides);

		function slide () {
			headImg.classList.add('hide');

			setTimeout(function () {
				headImg.classList.remove('hide');
				headImg.src= imgFolder + headSlides[Math.floor(Math.random() * (headSlides.length - 1))];
				}, delay/2 * 1000);

		}

		headSliderBox.addEventListener('scroll', function(e) {
			var imgCR = headImg.getBoundingClientRect();
			// console.log(imgCR);

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

<h2>Описание слайдера</h2>

<div>
	<p>Очень простой слайдер c анимацией переходов между изображениями, построенной на CSS-3. Слабо нагружает броузер. Отлично подойдёт для шапки сайта или блога.</p>
	<p>Для ещё большего уменьшения влияния кода на поведение страницы, смена изображений начинается в тот момент, как только слайдер полностью появляется в области видимости страницы броузера, и прекращается в момент его касания границы окна при прокрутке страницы.</p>
	<p>Слайдер выводит изображения в случайном порядке из указанной в переменной <em>imgFolder</em> директории. Список имён изображений хранится в массиве <em>headSlides</em>.</p>
</div>


<h4>Подключение и настройки скрипта (исходный код примера)</h4>
<pre><code for="#prim" data-lib="ES-5 + CSS-3"></code></pre>