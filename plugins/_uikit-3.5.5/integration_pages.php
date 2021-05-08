<?php
namespace plugins;

$modDir = \Site::getPathFromRoot(__DIR__);

tolog(basename(__FILE__)." started");

$params= json_decode(
	file_get_contents(__DIR__."/sts.json"), 1
) ?? [
	'include_uikit'=>0,
];


\Site::$Page->headhtml.= '
<!-- UIkit-->
<link rel="stylesheet" href="/'.$modDir.'/css/uikit.min.css" />
<script src="/'.$modDir.'/js/uikit.min.js"></script>';

if(@$params['include_picts'])
{
	\Site::$Page->headhtml.= '
	<!-- UIkit picts-->
	<script src="/'.$modDir.'/js/uikit-icons.min.js"></script>';
}

\Site::$Page->headhtml.= '<!-- /UIkit-->';

// tolog('',null,[$Page]);

if(@$params['use_styles_input'])
{
	// ob_start();
?>
	<script>
	'use strict';
	kff.checkLib('jQuery')
	.then($=>{
		$('input:not([type=checkbox], [type=file])').addClass('uk-input');

		// $('input[type=button]').addClass('uk-input');

		$('select').addClass('uk-select');

		$('textarea').addClass('uk-textarea');

		$('input[type=checkbox]')
		.addClass('uk-checkbox');

		$('input[type=radio]').addClass('uk-radio');

		$('input[type=range]').addClass('uk-range');

		$('input[type=file]')
		.wrap('<div uk-form-custom />')
		.after('<button class="uk-link" style="color:#fff">Загрузить</button>')
		// .text(this.value)
		// .text(this.value||"Загрузить")
	})
	</script>

<?php
}

if(@$params['use_styles_ul'])
{
	echo '<script>
	kff.checkLib(\'jQuery\')
	.then($=>{
		$(\'ul\').addClass(\'uk-list uk-list-striped uk-list-large\');
	})
	</script>';
}



return null;