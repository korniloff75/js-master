<p>Данный модуль предназначен для создания стены с пользовательскими фото и комментариями к ним.</p>
<p>Использование стены заключается в размещении фотографии на месте, указанном пользователем. Для этого нужно сделать клик в нужном месте стены, после чего под ней появится форма для добавления фотографии и комментария к ней. Пользователю требуется заполнить обязательные (имя и фото) поля формы и нажать кнопку <em>Загрузить</em>.</p>
<p>После загрузки на сервер на стене появится миниатюра загруженного изображения. При клике по ней ЛКМ - изображение увеличивается в пределах стены. Повторный клик возвращает миниатюру. При клике ПКМ - появляется окно с пользовательским именем и комментарием.</p>
<p>На базе данного модуля можно вполне разработать модуль доски со стикерами, карты с фотографиями и проч.</p>

<?php
global $pth;

require_once(\H::$Dir  . 'assets/Wall.php');

$wall= new Wall('foto', [
		// Размер миниатюры
		'min' => '70px',
		// Максимальный размер файла в байтах
		'max' => 500*1024
	],
# Путь к директории для загрузки пользовательских фото
	\H::$Dir  . 'assets/uploadsWall/'
);

// $wall->__construct();

?>

<style>
	#<?= $wall->wallID; ?> {width:100%; height:500px; cursor:pointer; border:1px solid red; background:#aaa url('/<?=\H::$Dir ?>BrickWall.png') center/cover;}
	img.big {width:100% !important;}
	form>* {display:block; margin-bottom:15px;}
</style>

