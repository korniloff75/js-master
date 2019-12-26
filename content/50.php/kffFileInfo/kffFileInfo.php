<pre>
<?php

$path = new SplFileInfo(__FILE__);
$kffPath = new kffFileInfo(__FILE__);

echo "<h5>SplFileInfo</h5>";

echo $path->getPathname() . " - getPathname()\n";
echo $path->getRealPath() . " - getRealPath()\n";
echo "\n";

echo '<hr>';
echo "<h5>kffFileInfo</h5>";

echo $kffPath::ROOT . " - \$kffPath::ROOT\n";
echo $kffPath->getPathname() . " - getPathname()\n";
echo $kffPath->getRealPath() . " - getRealPath()\n";
echo $kffPath->fromRoot() . " - fromRoot()\n";


?>
</pre>