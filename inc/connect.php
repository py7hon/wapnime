<?php
$config = file_get_contents(root.'app/config.dat');
$mysql = explode("\r\n", $config);
if(!($db = @mysql_connect($mysql[0], $mysql[1], $mysql[2]))){
echo $lang['not_connect'];
exit;
}
if(!@mysql_select_db($mysql[3], $db)){
echo $lang['not_db'];
exit;
}
mysql_query('set charset utf8', $db);
mysql_query('SET names utf8', $db);
mysql_query('set character_set_client="utf8"', $db);
mysql_query('set character_set_connection="utf8"', $db);
mysql_query('set character_set_result="utf8"', $db);
?>
