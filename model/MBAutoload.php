<?php
require_once(__DIR__.'/'.'MBBasicModel.php');

$handler = opendir(__DIR__);
while (($filename = readdir($handler)) !== false) {
    if ($filename != "." && $filename != ".." && stristr($filename,'.php')) {
    	require_once(__DIR__.'/'.$filename);
    }
}
closedir($handler);

?>