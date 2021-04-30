<?php
namespace plugins\_codemirror;

// $uri_dir= '/'.\Site::getPathFromRoot(__DIR__);

if(is_adm()):

$cacheCssUri= \Site::sewFiles(
	array_map(function($i){return __DIR__."/js/$i.css";}, ['lib/codemirror','theme/monokai'])
	, __DIR__.'/cacheStyles.css'
);

$cacheUri= \Site::sewFiles(
	array_map(function($i){return __DIR__."/js/$i.js";}, ['lib/codemirror','addon/selection/selection-pointer','mode/xml/xml','mode/javascript/javascript','mode/css/css','mode/htmlmixed/htmlmixed'])
	, __DIR__.'/cache.js', ';'
);

?>
<link rel="stylesheet" href="<?=$cacheCssUri?>">
<script src="<?=$cacheUri?>"></script>

<style>
.CodeMirror {
  height: 60vh;
}
</style>

<script type="module" async>
'use strict';
var $switchers= $('.editorSwitcher'),
// el= document.querySelector('.editor'),
opts= {
	theme: 'monokai',
	mode: {
		name: "htmlmixed",
		scriptTypes: [
			{matches: /\/x-handlebars-template|\/x-mustache/i, mode: null},
		]
	},

	selectionPointer: true,
	// lineNumbers: true,
	// matchBrackets: true,
	indentUnit: 2,
	// value: el.innerHTML,
	lineWrapping: true,
	// viewportMargin: Infinity,
	// extraKeys: {"F11": toggleFullscreenEditing, "Esc": toggleFullscreenEditing},
};

$switchers.each((ind,i)=>{
	let $i= $(i);
	$i.append('<option>CodeMirror</option>');
	$i.change(e=>{
		let action = i.options[i.selectedIndex].textContent;
		$i.siblings().find('.cm-save').remove();

		if(action !== 'CodeMirror') return;

		e.stopPropagation();
		e.preventDefault();

		let $area = $i.siblings('.editor'),
		area = $area[0];

		let editor = CodeMirrorInit(area),
			editorArea = editor.display.wrapper;

		// *Загружаем чистый код
		$.post('/api/editContent.php',{
			action: 'load',
			path: editorArea.dataset.path,
		}).then(resp=>{
			// console.log({resp});
			editor.setValue(resp);
		});

		console.log({editor});

		// *SAVE btn
		$('<div class="right"><button class="cm-save">SAVE<\/button><\/div>').insertAfter(editorArea)
		.click(e=>{
			save(editor,editorArea);
		});


	});
});

function CodeMirrorInit (el) {
	return CodeMirror(function(node){
		node.dataset.path = el.dataset.path;
		el.parentNode.replaceChild(node, el);
		node.classList.add('editor');
		console.log({node});
	}, opts);
}


// *Save content
function save(editor,editorArea){
	console.log(editorArea.dataset.path);
	// return;

	$.post('/api/editContent.php', {
		path: editorArea.dataset.path,
		art : editor.getValue(),
		action : 'save'
	})
	.done(function(response) {
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