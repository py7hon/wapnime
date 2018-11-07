<?php
$all = mysql_result(mysql_query('SELECT COUNT(*) FROM `shout`'), 0);
$new = mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `time` > "'.(time()-86400).'"'), 0);
if($new == 0)
$new = NULL;
else $new = '/+<font class="off">'.$new.'</font>';
echo $all.$new;
?>
