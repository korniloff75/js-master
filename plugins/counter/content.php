<?php
if(!\LOCALHOST){
?>
<div id="LIcounter" class="right"></div>

<script src="/<?=\Site::getPathFromRoot(__DIR__)?>/liveInternet.js" async></script>
<?php
}
else echo "<div class='right'>{{COUNTER}}</div>";