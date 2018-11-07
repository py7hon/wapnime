<?php
$root = opendir(root.'fnc'); 
while($files = readdir($root)){
if(eregi('\.php$', $files))
include root.'fnc/'.$files;
}
?>
