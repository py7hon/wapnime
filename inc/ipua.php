<?php
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif(isset($_SERVER['CLIENT_IP'])){
$ip = $_SERVER['CLIENT_IP'];
}else{
$ip = $_SERVER['REMOTE_ADDR'];
}
if(!isset($_COOKIE['ip']) or $_COOKIE['ip'] == NULL)
setcookie('ip', $ip, time()+60*60*24*365);
$ua = $_SERVER['HTTP_USER_AGENT'];
function browser($ua){
$browser = explode(' ', $ua);
$browser = $browser[0];
return $browser;
}
if(isset($_SERVER['HTTP_X_ANDROID_PHONE_UA'])){
$phone = $_SERVER['HTTP_X_ANDROID_PHONE_UA'];
$phone = strtok($phone, '/');
$phone = strtok($phone, '-');
$phone = str_replace('Sony', 'Sony ', $phone);
$phone = str_replace('Nokia', 'Nokia ', $phone);
$phone = str_replace('Xiaomi', 'Xiaomi ', $phone);
$phone = str_replace('Oppo', 'Oppo ', $phone);
$phone = str_replace('Samsung', 'Samsung ', $phone);
$phone = str_replace('Vivo', 'Vivo ', $phone);
$phone = str_replace('HTC', 'HTC ', $phone);
$phone = str_replace('Hisense', 'Hisense ', $phone);
$phone = str_replace('Huawei', 'Huawei ', $phone);
$phone = str_replace('Asus', 'Asus ', $phone);
}
else $phone = 'Unknown';
?>
