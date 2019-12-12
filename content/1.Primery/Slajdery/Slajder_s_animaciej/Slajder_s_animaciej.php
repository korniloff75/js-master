<h2>Описание слайдера</h2>

<div>
	<p>Этот слайдер написан на нативном javascript, без использования jQuery.</p>
</div>


<?php
	$iPath = "userfiles/images/examples/";
	#	echo $iPath;

	$imgs= scandir($iPath);
	foreach ($imgs as $i => &$f) {
		if(!is_file($iPath . $f)) unset($imgs[$i]);
	}

	$imgs = json_encode(array_values($imgs), JSON_UNESCAPED_UNICODE);
	// var_dump($imgs);
?>


<h3>Пример работы скрипта:</h3>

<div id="prim" style="text-align:center; max-width:100%;">
	<style type="text/css">
	div#sl{
		width:600px; max-width:100%; overflow:hidden;
		height: 135px;
		border:1px ridge #aaa;
		position: relative;
	}

	div#sl img{
		position: absolute;
		width: 600px;
		top: 0;
		left: 0;
		z-index: 5;
		margin:0;
		transition: left 1s 1s;
	}
	div#sl .shad {
		/* position: relative; */
		left: -600px;
		z-index: 10;
	}

	</style>

	<div id="sl" >
		<img class="shad">
		<img class="main">
	</div>


	<script type="text/javascript">
	'use strict';
	function slide () {
		var path = '<?=$iPath?>',
			imgs = <?=$imgs?>,
			count = 0,
			wrap = document.querySelector('#sl'),
			main = wrap.querySelector('.main'),
			shad = wrap.querySelector('.shad');

		console.log('imgs = ', imgs);

		function iter () {
			count = count === imgs.length -2 ? 0 : count;
			shad.style.zIndex = -1;
			shad.style.left = '-600px';
			// shad.style.left = -parseInt(getComputedStyle(wrap).width) + 'px';

			main.src = '/' + path + (imgs[count]);
			shad.src = '/' + path + (imgs[++count]);

			shad.style.zIndex = 10;
			shad.style.left = 0;

			if(count === 5) clearInterval(I);
		}

		var I = setInterval(iter, 3000);

		// console.log("headSlides= " + headSlides);

	} // slide


	// Run after LESS
	window.addEventListener('DOMContentLoaded', function() {
		// slide.inited = 1;
		if(!window.less) slide();
		else less.pageLoadFinished.then(slide);
	});
	</script>

</div>

<h4>Подключение и настройки скрипта (исходный код примера)</h4>
<pre><code for="#prim"></code></pre>