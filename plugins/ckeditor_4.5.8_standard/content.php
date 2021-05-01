<?php
$uri_dir= '/'.\Site::getPathFromRoot(__DIR__);

if(is_adm()):

?>
<script type="text/javascript" src="<?=$uri_dir?>/ckeditor/ckeditor.js"></script>

<script type="module" async>
'use strict';
/* CKEDITOR.replaceAll(function( textarea, config ) {
	// An assertion function that needs to be evaluated for the <textarea>
	// to be replaced. It must explicitely return "false" to ignore a
	// specific <textarea>.
	// You can also customize the editor instance by having the function
	// modify the "config" parameter.

	// определяем высоту редактора
	config.height = textarea.style.height != '' ? textarea.style.height : 300;
}); */

// разрешить теги <style>
CKEDITOR.config.protectedSource.push(/<(style)[^>]*>.*<\/style>/ig);
// разрешить теги <script>
CKEDITOR.config.protectedSource.push(/<(script)[^>]*>.*<\/script>/ig);
// разрешить php-код
CKEDITOR.config.protectedSource.push(/<\?[\s\S]*?\?>/g);
// разрешить любой код: <!--dev-->код писать вот тут<!--/dev-->
CKEDITOR.config.protectedSource.push(/<!--dev-->[\s\S]*<!--\/dev-->/g);

// *Запускаем редактор с файловым браузером
// todo
Object.assign(CKEDITOR.config, {
	// filebrowserBrowseUrl: '?module=<?=__DIR__?>/createCKEditorBrowser',
	filebrowserBrowseUrl: '?module=<?=\Site::getPathFromRoot(__DIR__)?>/kff.Explorer/index.php',
	disallowedContent : 'img{width,height}',
	image_removeLinkByEmptyURL: true,
});


var $switchers= $('.editorSwitcher');
// el= document.querySelector('.editor'),

$switchers.each((ind,i)=>{
	let $i= $(i);
	$i.append('<option>CKEditor</option>');
	$i.change(e=>{
		let action = i.options[i.selectedIndex].textContent;
		$i.siblings().find('.cke-save').remove();

		if(action !== 'CKEditor') {
			Object.keys(CKEDITOR.instances).forEach(i=>CKEDITOR.instances[i].destroy())
			return;
		}

		e.stopPropagation();
		e.preventDefault();

		let editor,
		$area = $i.siblings('.editor'),
		area = $area[0];

		// *Загружаем чистый код
		$.post('/api/editContent.php',{
			action: 'load',
			path: area.dataset.path,
		}).then(resp=>{
			// console.log({resp});
			area.contentEditable = true;
			area.innerHTML= resp;
			editor= CKEDITOR.inline(area);
		});

		// console.log({editor});

		// *SAVE btn
		$('<div class="right"><button class="cke-save">SAVE<\/button><\/div>').insertAfter(area)
		.click(e=>{
			console.log({editor,area},editor.getData());
			// save(editor,area);
		});


	});
});


// *Save content
function save(editor,editorArea){
	console.log(editorArea.dataset.path);
	// return;

	$.post('/api/editContent.php', {
		path: editorArea.dataset.path,
		art : editor.getData(),
		action : 'save'
	})
	.then(function(response) {
		console.log(response);

		_H.popup({
			'server' : {
				tag: 'div',
				html: response,
			}
		});

	});
}

</script>

<?php
endif;