<?php

$set = mysql_fetch_array(mysql_query('SELECT * FROM `user` WHERE `id` = "1" LIMIT 1'));
$set['pass'] = '*****'; //set password for system

if(isset($_POST['nick']) && isset($_POST['pass'])){
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `user` WHERE `nick` = "'.mysql_real_escape_string($_POST['nick']).'" AND `pass` = "'.encrypt(mysql_real_escape_string($_POST['pass'])).'" LIMIT 1'), 0) == 1){
$user = mysql_fetch_array(mysql_query('SELECT * FROM `user` WHERE `nick` = "'.mysql_real_escape_string($_POST['nick']).'" AND `pass` = "'.encrypt(mysql_real_escape_string($_POST['pass'])).'" LIMIT 1'));
$_SESSION['id'] = $user['id'];
setcookie('id', $user['id'], time()+60*60*24*7);
setcookie('pass', encrypt(mysql_real_escape_string($_POST['pass'])), time()+60*60*24*7);
}
else err('Username or Password not match.');
}elseif(isset($_SESSION['id']) && mysql_result(mysql_query('SELECT COUNT(*) FROM `user` WHERE `id` = "'.intval($_SESSION['id']).'" LIMIT 1'), 0) == 1){
$user = mysql_fetch_array(mysql_query('SELECT * FROM `user` WHERE `id` = "'.intval($_SESSION['id']).'" LIMIT 1'));
$_SESSION['id'] = $user['id'];
}elseif(isset($_COOKIE['id']) && isset($_COOKIE['pass']) && $_COOKIE['id'] != NULL && $_COOKIE['pass'] != NULL){
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `user` WHERE `id` = "'.intval($_COOKIE['id']).'" AND `pass` = "'.$_COOKIE['pass'].'" LIMIT 1'), 0) == 1){
$user = mysql_fetch_array(mysql_query('SELECT * FROM `user` WHERE `id` = "'.intval($_COOKIE['id']).'" LIMIT 1'));
$_SESSION['id'] = $user['id'];
}else{
setcookie('id');
setcookie('pass');
}
}

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `banned` WHERE `ip` = "'.$_COOKIE['ip'].'" AND `expired` > "'.time().'" LIMIT 1'), 0) != 0){
die($lang['ip_blocked']);
}

if(isset($_SERVER['HTTP_REFERER']) && !eregi(str_replace('.', '\.', $_SERVER['HTTP_HOST']), $_SERVER['HTTP_REFERER']) && eregi('^https?://', $_SERVER['HTTP_REFERER']) && $ref = explode('/', $_SERVER['HTTP_REFERER'])){
$ref = str_replace('www.', '', $ref[2]);
if(isset($ref)){
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `referer` WHERE `url` = "'.mysql_real_escape_string($ref).'"'), 0) == 0)
mysql_query('INSERT INTO `referer` SET `url` = "'.mysql_real_escape_string($ref).'", `count` = "1"');
else mysql_query('UPDATE `referer` SET `count` = `count`+1 WHERE `url` = "'.mysql_real_escape_string($ref).'"');
}
}
?>
