<script>
// less options in template
var less = {
	env: "<?=\LOCALHOST ? 'development' : 'production'?>",
	javascriptEnabled: false
}
</script>

<?php
# Не нашел способа получить контент из тега style, либо из результата работы less в броузере.
if(\UPD_LESS_FROM_BROUSER) {
	echo "<script>
		$.post('css/handler.php', {
				css: $('#less:css-less-core')
			},
			function() {}
		);
	</script>";
}