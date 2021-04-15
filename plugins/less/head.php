<?php
global $Data;

if(\ADMIN){
?>
<link rel="stylesheet/less" type="text/css" href="/css/admin.less">
<script src="/<?=Site::getPathFromRoot(__DIR__)?>/LESS 3-7-1.min.js" defer></script>
<?php
}

echo \H::addFromDir(\DR."/{$Data['template']}", \ADMIN && \USE_BROWS_LESS ? 'less' : 'css');