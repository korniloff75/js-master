<!-- Подбор символов Юникод -->
<style>
	i {
		font-style: normal;
	}

<?php
// 500
$base = 0x1F5F0;
$delta = 300;
for($i=$base; $i<($base+$delta); ++$i) {
	echo '#unicode i:nth-child(' . ($i - $base) . '):before { content: \'\\\\' . dechex($i) . "'; font-size: 2.5em;}\n";
	/* echo '#unicode i:nth-child(' . ($i - $base) . '):after { content: \'\\' . dechex($i) . "'; font-size: 2.5em;}\n"; */
}
?>
</style>

<div id="unicode" >
</div>

<script>
// '\1F612'
var /* $tyle = $('<style />').appendTo('head'), */
	$node = $('#unicode');

for(var i=<?=$base?>; i < <?=($base+$delta)?>; ++i) {
	// $tyle.append('#unicode i:nth-child(' + (i - 600) + '):before { content: \'' + '\\1F' + i + '\'}');
	$item = $('<i />', {
		class: 'flex-column-inline center',
		style: 'margin: 10px;'
	}).appendTo($node);
	$('<input type="text">').appendTo($item)
	.val('\\\' + i);
}
</script>