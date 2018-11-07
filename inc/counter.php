<?php
if(!isset($_SESSION['ip']) or $_SESSION['ip'] == NULL)
$_SESSION['ip'] = $ip;
if(!isset($_SESSION['ua']) or $_SESSION['ua'] == NULL)
$_SESSION['ua'] = $ua;
if(!isset($_SESSION['phone']) or $_SESSION['phone'] == NULL)
$_SESSION['phone'] = $phone;
if(!isset($_SESSION['date']) or $_SESSION['date'] == NULL)
$_SESSION['date'] = gmdate('d-m-Y', time()+3600*$set['gmt']);

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `online` WHERE `ip` = "'.$_SESSION['ip'].'" AND `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'" AND `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'" LIMIT 1'), 0) == 1){
$guest = mysql_fetch_array(mysql_query('SELECT `visit` FROM `online` WHERE `ip` = "'.$_SESSION['ip'].'" AND `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'" AND `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'" LIMIT 1'));
if(isset($_SESSION['name']) && $_SESSION['name'] != NULL)
mysql_query('UPDATE `online` SET `time` = "'.time().'", `name` = "'.mysql_real_escape_string($_SESSION['name']).'", `url` = "'.mysql_real_escape_string($_SERVER['REQUEST_URI']).'", `visit` = "'.($guest['visit']+1).'" WHERE `ip` = "'.$_SESSION['ip'].'" AND `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'" AND `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'" LIMIT 1');
else mysql_query('UPDATE `online` SET `time` = "'.time().'", `url` = "'.mysql_real_escape_string($_SERVER['REQUEST_URI']).'", `visit` = "'.($guest['visit']+1).'" WHERE `ip` = "'.$_SESSION['ip'].'" AND `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'" AND `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'" LIMIT 1');
}else{
mysql_query('INSERT INTO `online` SET `time` = "'.time().'", `ip` = "'.$_SESSION['ip'].'", `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'", `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'", `url` = "'.mysql_real_escape_string($_SERVER['REQUEST_URI']).'"');
}
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` = "'.$_SESSION['date'].'" AND `ip` = "'.$_SESSION['ip'].'" AND `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'" AND `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'" LIMIT 1'), 0) == 0){
mysql_query('INSERT INTO `counter` SET `date` = "'.$_SESSION['date'].'", `ip` =  "'.$_SESSION['ip'].'", `ua` = "'.mysql_real_escape_string($_SESSION['ua']).'", `phone` = "'.mysql_real_escape_string($_SESSION['phone']).'", `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"');
}
function online(){
return mysql_result(mysql_query('SELECT COUNT(*) FROM `online` WHERE `time` > "'.(time()-120).'"'), 0);
}
function day(){
global $set;
$date = gmdate('d-m-Y', time()+3600*$set['gmt']);
return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` = "'.$date.'" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
}
function week(){
global $set;
$week = gmdate('d', time()+3600*$set['gmt'])-7;
if($week == 1 || $week == 2 || $week == 3 || $week == 4 || $week == 5 || $week == 6 || $week == 7 || $week == 8 || $week == 9)
return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` = "0'.$week.'-'.$month.'-'.$year.'" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
else return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` = "'.$week.'-'.$month.'-'.$year.'" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
}
function month(){
global $set;
$month = gmdate('m-Y', time()+3600*$set['gmt']);
return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` LIKE "%'.$month.'%" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
}
function year(){
global $set;
$year = gmdate('Y', time()+3600*$set['gmt']);
return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `date` LIKE "%'.$year.'%" AND `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
}
function counter(){
return mysql_result(mysql_query('SELECT COUNT(*) FROM `counter` WHERE `page` = "'.mysql_real_escape_string($_SERVER['PHP_SELF']).'"'), 0);
}
mysql_query('DELETE FROM `online` WHERE `time` < "'.(time()-86400).'"');
?>
