<?php
$root = NULL;
$root2 = 0;
while(!is_file($root.'inc/root.php') && $root2 < 20){
$root .= '../';
$root2++;
}
define('root', $root);
?>
