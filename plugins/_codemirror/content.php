<?php
$uri_dir= '/'.\Site::getPathFromRoot(__DIR__);
?>

<link rel="stylesheet" href="<?=$uri_dir?>/js/lib/codemirror.css">
<script src="<?=$uri_dir?>/js/lib/codemirror.js"></script>

<div id="codemirror"></div>

<script>
  var editor = CodeMirror(document.querySelector('#codemirror'), {
    lineNumbers: true,
		mode:  "text/html",
		// mode: 'application/x-httpd-php',
		matchBrackets: true,
		indentUnit: 2,
		value: document.querySelector('.editor').innerHTML,
  });

	// editor.setValue(document.querySelector('.editor').innerHTML);

  /* var editor = CodeMirror.fromTextArea(codemirror, {
    lineNumbers: true,
		matchBrackets: true,
  }); */
</script>