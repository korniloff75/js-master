<?php
namespace template;

$Nav = new \php\classes\Navigate;

// var_dump();
?>
<!DOCTYPE html>
<html lang=<?=LANG?>>

<head>
</head>


<body>

<!--DECOR-->
<div id="bg_wraper">
	<div id="bg">
		<!--img src="images/bg.jpg" alt="main background" -->
		<div id="bg_mask">
			<!-- Darking bg-->
		</div>
	</div>
</div>

<!--/DECOR-->

<section id="menu_block">

	<?= $Nav->genMenu(); ?>

	<div id="menu_close" class="close_button">
		<!-- <img src="/images/icons/cross.png" alt="close"> -->
	</div>
</section>

<section id="body_main">

	<div id="nav_main">
		<div id="menu_butt">
			<div style="margin-bottom: .7em;">Меню</div>
			<div class="menu_icon">
				<div class="menu_icon-item" style="transform: translate3d(0px, 0px, 0px);"></div>
				<div class="menu_icon-item"></div>
				<div class="menu_icon-item"></div>
			</div>
		</div>

		<?= $Nav->genNavMain();?>
</div>

	<section id="logo">
		<?= LOGO?>
	</section>


	<div class="toSidebar"><svg xmlns="http://www.w3.org/2000/svg" width="500" height="40" viewBox="0 0 650 40" preserveAspectRatio="xMidYMid meet" style="width: 100%; height: 100%;">
	<g transform="matrix(0,-1,1,0,360,20)" opacity="1"><g opacity="1" transform="matrix(1,0,0,1,0,0)"><path fill="rgb(255,255,255)" fill-opacity="1" d=" M7.594,18.25 C7.594,18.25 -0.109,27.844 -0.109,27.844 C-0.109,27.844 -7.875,18.25 -7.875,18.25 C-7.875,18.25 -9.094,19.531 -9.094,19.531 C-9.094,19.531 -0.188,30.531 -0.188,30.531 C-0.188,30.531 8.906,19.531 8.906,19.531 C8.906,19.531 7.594,18.25 7.594,18.25"></path></g></g><g transform="matrix(0,-1,1,0,329,17.5)" opacity="1"><g ><path fill-opacity="0" stroke-miterlimit="4" stroke="rgb(255,255,255)" stroke-opacity="1" stroke-width="2" d=" M-2.5,-68.652 C-2.5,-26.235 -2.5,17.735 -2.5,60.152"></path></g></g>
	</svg></div>


	<aside id="sidebar">
		<div id="bg_pattern"></div>

		<div class="content">
			<? #var_dump($MainContent) ?>
			<!-- $CONTENT$ -->
		</div>
	</aside>
</section>


<footer>
</footer>

</body>
</html>