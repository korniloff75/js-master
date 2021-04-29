<?php
$uri_dir= '/'.\Site::getPathFromRoot(__DIR__);

if(is_adm()):
?>

<link rel="stylesheet" href="<?=$uri_dir?>/js/lib/codemirror.css">
<link rel="stylesheet" href="<?=$uri_dir?>/js/theme/monokai.css">
<script src="<?=$uri_dir?>/js/lib/codemirror.js"></script>
<script src="<?=$uri_dir?>/js/addon/selection/selection-pointer.js"></script>
<script src="<?=$uri_dir?>/js/mode/xml/xml.js"></script>
<script src="<?=$uri_dir?>/js/mode/javascript/javascript.js"></script>
<script src="<?=$uri_dir?>/js/mode/css/css.js"></script>
<script src="<?=$uri_dir?>/js/mode/htmlmixed/htmlmixed.js"></script>

<!-- <div id="codemirror"></div> -->

<style>
.CodeMirror {
  height: 60vh;
}
</style>

<script type="module" async>
	// todo Разобраться с динамически добавленными элементами и стилями.
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
		$i.append(`<option>CodeMirror</option>`);
		$i.change(e=>{
			let action = i.options[i.selectedIndex].textContent;
			if(action !== 'CodeMirror') return;

			e.stopPropagation();
			e.preventDefault();

			let $area = $i.siblings('.editor'),
			area = $area[0];

			let editor = CodeMirrorInit(area),
				editorArea = editor.display.wrapper;

			// *Загружаем чистый код
			/* $.get(`/${editorArea.dataset.path}`)
			.then(resp=>{
				// console.log({resp});
				editor.setValue(resp);
			}); */
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