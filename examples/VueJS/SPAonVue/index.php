<!DOCTYPE HTML>
<html lang="ru">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>SPA with VueJS</title>
	<script src="../js/vue.js"></script>
	<script src="../js/axios/0.18.0/axios.min.js"></script>
	<link rel="stylesheet" href="css/styles.css">

	<pre>
	<?php
	$list = new DirectoryIterator('content/');
	$jsonArr = [];
		foreach($list as $item) {
		if($item->isDir()) continue;
		$jsonArr[] = $item->getFilename();
	}
			// print_r($jsonArr);
	?>
	</pre>
		<script>
	var Pages = <?=json_encode($jsonArr)?>;
	// console.log('Pages = ', Pages);
	</script>

</head>


<body>
	<header>
		<h1>SPA with VueJS + Axios</h1>
	</header>


	<div id="main">
		<aside>

			<nav is="menu-items" :pages="pages" :cur-page="defineCurPage"></nav>

		</aside>

		<script type="text/x-template" id="menu-items-template">
			<nav>
				<h3>==МЕНЮ==</h3>
				<ul @click.prevent="navHandler">
					<li v-for="(page, ind) in pages" :class="{active: ind === $root.defineCurPage.ind}" :data-ind="ind">
						<a :href="$root.folder + page[0]" :title="page[2]||page[0]" v-text="page[1]||page[0]"></a>
					</li>
				</ul>
			</nav>
		</script>

		<!--  v-html="html" -->
		<section id="content" v-html="html"></section>
	</div>

	<footer></footer>


	<script src="js/vue_app.js"></script>

</body>

</html>