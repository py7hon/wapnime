<?php
if(isset($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] == '/index.php' && isset($_GET['lang'])){

//Register session and cookie
$_SESSION['lang'] = $_GET['lang'];
setcookie('lang', $_GET['lang'], time()+60*60*24*360);
if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
header('location: '.$_SERVER['HTTP_REFERER']);
else header('location: /?'.SID);
}elseif(isset($_SESSION['lang'])){
$_GET['lang'] = $_SESSION['lang'];
}elseif(isset($_COOKIE['lang'])){
$_GET['lang'] = $_COOKIE['lang'];
}else{
$_GET['lang'] = 'id';
}
switch($_GET['lang']){
default:
$local = 'id';
break;
case'en':
$local = 'en';
break;
case'id':
$local = 'id';
break;
}
include root.'lang/'.$local.'.php';
?>
