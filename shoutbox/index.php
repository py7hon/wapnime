<?php

include'../inc/root.php';
include'../inc/session.php';
include'../inc/lang.php';
include'../inc/connect.php';
include'../inc/fnc.php';
include'../inc/ipua.php';
include'../inc/system.php';
include'../inc/counter.php';

switch($_GET['act']){
default:
if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
$locate = $_SERVER['HTTP_REFERER'];
else $locate = './';

if(isset($_POST['name']) && isset($_POST['text'])){
if(!isset($_SESSION['name']) or $_SESSION['name'] != $_POST['name'])
$_SESSION['name'] = $_POST['name'];
if(!isset($_SESSION['url']) or $_SESSION['url'] != $_POST['url'])
$_SESSION['url'] = $_POST['url'];
if(!isset($_SESSION['text']) or $_SESSION['text'] != $_POST['text'])
$_SESSION['text'] = $_POST['text'];
if(!isset($_SESSION['icon']) or $_SESSION['icon'] != $_POST['icon'])
$_SESSION['icon'] = $_POST['icon'];

if(strlen($_POST['name']) < 3){
err($lang['name'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 8){
err($lang['name'].' '.$lang['error_long'].' 8 '.$lang['characters'].'.');
}elseif(!preg_match('#^([A-z0-9])+$#ui', $_POST['name'])){
err($lang['invalid_name']);
}elseif(antispam($_POST['name'])){
err($lang['block_name']);
}elseif(strlen($_POST['url']) > 32){
err($lang['url'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) < 5){
err($lang['msg'].' '.$lang['error_short'].' 5 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > $set['char']){
err($lang['msg'].' '.$lang['error_long'].' '.$set['char'].' '.$lang['characters'].'.');
}elseif(trim($_POST['text'] == 'pesan') || trim($_POST['text'] == 'Pesan') || trim($_POST['text'] == 'test') || trim($_POST['text'] == 'Test') || trim($_POST['text'] == 'message') || trim($_POST['text'] == 'Message')){
err($lang['block_msg']);
}elseif(empty($_POST['icon'])){
err($lang['empty_gender']);
}elseif($_POST['icon'] != 1 && $_POST['icon'] != 2){
err($lang['invalid_gender']);
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `name` = "'.mysql_real_escape_string($_POST['name']).'" AND `text` = "'.mysql_real_escape_string($_POST['text']).'" AND `time` > "'.(time()-300).'" LIMIT 1'), 0) != 0){
err($lang['spam_msg']);
}else{
mysql_query('INSERT INTO `shout` SET `time` = "'.time().'", `name` = "'.mysql_real_escape_string($_POST['name']).'", `url` = "'.mysql_real_escape_string($_POST['url']).'", `text` = "'.mysql_real_escape_string($_POST['text']).'", `icon` = "'.intval($_POST['icon']).'", `ua` = "'.mysql_real_escape_string($ua).'", `phone` = "'.mysql_real_escape_string($phone).'", `ip` = "'.$ip.'"');
header('location: '.$locate);
}
}

$title = 'Shoutbox';
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}elseif(isset($_SESSION['msg'])){
echo $_SESSION['msg'];
unset($_SESSION['msg']);
}
if(isset($user)){
echo'<script type="text/javascript">
function warning(url){
var report = confirm("'.$lang['confirm_delete_msg'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
echo'<form action="./" method="post">'."\n";
echo'<div class="menu">'."\n";
echo'<table class="post">'."\n";
echo'<tr>'."\n";
echo'<td style="width:50%">'."\n";
echo $lang['name'].' :<br />';
if(isset($_SESSION['name']))
echo'<input type="text" name="name" style="width:90%" value="'.$_SESSION['name'].'"/>'."\n";
else echo'<input type="text" name="name" style="width:90%" value="'.strtolower($lang['name']).'" onfocus="if(this.value='.strtolower($lang['name']).') this,value""/>'."\n";
echo'</td>'."\n";
echo'<td style="width:50%">'."\n";
echo $lang['site'].' :<br />';
if(isset($_SESSION['url']))
echo'<input type="text" name="url" style="width:90%" value="'.$_SESSION['url'].'"/>'."\n";
else echo'<input type="text" name="url" style="width:90%" value="http://"/>'."\n";
echo'</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td style="width:50%">'."\n";
echo $lang['msg'].' :<br />';
if(isset($_SESSION['text'])){
echo'<input type="text" name="text" style="width:90%" value="'.$_SESSION['text'].'"/>'."\n";
unset($_SESSION['text']);
}
else echo'<input type="text" name="text" style="width:90%" value="'.strtolower($lang['msg']).'" onfocus="if(this.value='.strtolower($lang['msg']).') this,value""/>'."\n";
echo'</td>'."\n";
echo'<td style="width:50%">'."\n";
echo $lang['gender'].' :<br /><select name="icon" style="width:99%"><option value="">'.$lang['select_icon'].'</option><option value="1" '.($_SESSION['icon'] == 1 ? 'selected="selected"' : null).'>'.$lang['male'].'</option><option value="2" '.($_SESSION['icon'] == 2 ? 'selected="selected"' : null).'>'.$lang['female'].'</option></select></td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="content" colspan="2"><input type="submit" value="Shout"/> <a href="/smiles.php">Smiles</a> <a href="/bbcode.php">BB-Code</a></td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
echo'</form>'."\n";
echo'</div>'."\n";
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `shout`'), 0);
$k_page = k_page($k_post, $set['page']);
$page = page($k_page);
$start = $set['page']*$page-$set['page'];
if($page > 1)
$back = '<a href="?page='.($page-1).'">&laquo;</a>';
else $back = '&laquo;';
if($page < $k_page)
$next = '<a href="?page='.($page+1).'">&raquo;</a>';
else $next = '&raquo;';
if($k_post == 0){
echo'<div class="status">'.$lang['no_msg'].'</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `shout` ORDER BY `id` DESC LIMIT '.$start.', '.$set['page']);
echo'<table class="post">'."\n";
while($post = mysql_fetch_array($result)){
if($set['time']==1)
$time = countdown($post['time']);
else $time = showdate($post['time']).' - '.showtime($post['time']);
if(trim($post['url']) != 'http://')
$name = '<a href="'.$post['url'].'">'.rainbow($post['name']).'</a>'.partner($post['name']);
else $name = rainbow($post['name']).partner($post['name']);
echo'<tr>'."\n";
echo'<td class="icon">'.icon_shout($post['icon']).'</td>'."\n";
echo'<td class="title"><b><span style="text-shadow:black 0.05em 0.05em 0.05em">'.strtolower($name).'</span></b> [<span>'.provider($post['ip']).'</span>]</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="menu" colspan="2">'."\n";
echo'<div style="text-align:right">'.$time.'</div>'."\n";
echo smiles(bbcode(sensor(htmlspecialchars(stripslashes($post['text']))))).'<br />'."\n";
echo'<div style="text-align:right">'.browser($post['ua']).' '.$post['phone'].'</div>'."\n";
if(isset($user))
echo'[<a href="javascript:warning(\'/shoutbox/delete/'.$post['id'].'.xhtml\');">D</a>] &#183; [<a href="/shoutbox/edit/'.$post['id'].'.xhtml">E</a>] &#183; [<a href="/shoutbox/banned/'.$post['id'].'.xhtml">B</a>] &#183; [<a href="/shoutbox/reply/'.$post['id'].'.xhtml">R</a>]'."\n";
echo'</td>'."\n";
echo'</tr>'."\n";
}
echo'<tr>'."\n";
echo'<td class="menu" colspan="2" style="text-align:center">['.$back.']||['.$next.']</td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
}
include'../inc/footer.php';
break;
case'delete':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['msg'].' '.$lang['not_found']);
header('location: /shoutbox/');
}else{
mysql_query('DELETE FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1');
$query = mysql_query('SELECT * FROM `shout` ORDER BY `id`');
$i = 1;
while($data = mysql_fetch_array($query)){
mysql_query('UPDATE `shout` SET `id` = '.$i.' WHERE `id` = '.$data['id']);
$i++;
}
mysql_query('ALTER TABLE `shout` AUTO_INCREMENT = '.$i);
msg($lang['msg'].' '.$lang['report_delete']);
header('location: /shoutbox/');
}
}
break;
case'edit':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['msg'].' '.$lang['not_found']);
header('location: /shoutbox/');
}else{
$edit = mysql_fetch_array(mysql_query('SELECT * FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'));

if(isset($_POST['edit'])){
if(strlen($_POST['name']) < 3){
err($lang['name'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 8){
err($lang['name'].' '.$lang['error_long'].' 8 '.$lang['characters'].'.');
}elseif(!preg_match('#^([A-z0-9])+$#ui', $_POST['name'])){
err($lang['invalid_name']);
}elseif(strlen($_POST['url']) > 32){
err($lang['url'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) < 5){
err($lang['msg'].' '.$lang['error_short'].' 5 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > 500){
err($lang['msg'].' '.$lang['error_long'].' 500 '.$lang['characters'].'.');
}elseif($_POST['icon'] != 1 && $_POST['icon'] != 2){
err($lang['invalid_gender']);
}else{
mysql_query('UPDATE `shout` SET `name` = "'.mysql_real_escape_string($_POST['name']).'", `url` = "'.mysql_real_escape_string($_POST['url']).'", `text` = "'.mysql_real_escape_string($_POST['text']).'", `icon` = "'.intval($_POST['icon']).'" WHERE `id` = '.num($_POST['id']));
msg($lang['msg'].' '.$lang['report_edit']);
header('location: /shoutbox/');
}
}

$title = 'Shoutbox | '.$lang['edit'].' '.$lang['msg'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/shoutbox/edit/'.$edit['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo'<input type="hidden" name="id" class="input" value="'.$edit['id'].'"/>'."\n";
echo $lang['name'].' :<br /><input type="text" name="name" class="input" value="'.$edit['name'].'"/><br />'."\n";
echo $lang['site'].' :<br /><input type="text" name="url" class="input" value="'.$edit['url'].'"/><br />'."\n";
echo $lang['gender'].' :<br /><select name="icon"><option value="1" '.($edit['icon'] == 1 ? 'selected="selected"' : null).'>'.$lang['male'].'</option><option value="2" '.($edit['icon'] == 2 ? 'selected="selected"' : null).'>'.$lang['female'].'</option></select><br />'."\n";
echo $lang['msg'].' :<br /><textarea name="text" class="textarea" cols="auto" rows="auto">'.$edit['text'].'</textarea><br />'."\n";
echo'<input type="submit" name="edit" value="'.$lang['update'].'"/> &#183; <a href="/shoutbox/">'.$lang['cancel'].'</a><br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
include'../inc/footer.php';
}
}
break;
case'reply':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['msg'].' '.$lang['not_found']);
header('location: /shoutbox/');
}else{
$reply = mysql_fetch_array(mysql_query('SELECT * FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'));
if(strlen($reply['text']) > 50)
$msg = substr($reply['text'], 0, 50).'...';
else $msg = $reply['text'];
if(isset($_POST['reply'])){
if(strlen($_POST['text']) < 5){
err($lang['msg'].' '.$lang['error_short'].' 5 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > 500){
err($lang['msg'].' '.$lang['error_long'].' 500 '.$lang['characters'].'.');
}else{
mysql_query('INSERT INTO `shout` SET `time` = "'.time().'", `name` = "Admin", `url` = "http://'.$_SERVER['HTTP_HOST'].'", `text` = "'.mysql_real_escape_string('[b]Quote '.$reply['name'].'...[/b][quote]'.$msg.'[/quote]'.$_POST['text']).'", `ua` = "'.mysql_real_escape_string($ua).'", `phone` = "'.mysql_real_escape_string($phone).'", `ip` = "'.$ip.'"');
msg($lang['msg'].' '.$lang['report_add']);
header('location: /shoutbox/');
}
}

$title = 'Shoutbox |  '.$lang['reply'].' '.$lang['msg'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/shoutbox/reply/'.$reply['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo'<b>'.$reply['name'].' '.$lang['says'].'</b><br />'."\n";
echo'<div class="quote">'.$msg.'</div>'."\n";
echo'<b>'.$lang['respons'].'</b><br /><textarea name="text"></textarea><br />'."\n";
echo'<input type="submit" name="reply" value="'.$lang['reply'].'"/> &#183; <a href="/shoutbox/">'.$lang['cancel'].'</a><br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
include'../inc/footer.php';
}
}
break;
case'banned':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['msg'].' '.$lang['not_found']);
header('location: /shoutbox/');
}else{
$data = mysql_fetch_array(mysql_query('SELECT `id`, `ip` FROM `shout` WHERE `id` = '.num($_GET['id']).' LIMIT 1'));

if(isset($_POST['banned'])){
$time = array('1', '7', '31', '365'); //durasi waktu
if(!in_array($_POST['time'], $time)){
err($lang['invalid_time']);
}elseif(strlen($_POST['text']) < 3){
err($lang['reason'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > 250){
err($lang['reason'].' '.$lang['error_long'].' 250 '.$lang['characters'].'.');
}elseif(empty($_POST['ip'])){
err($lang['empty_ip']);
}else{
mysql_query('INSERT INTO `banned` SET `time` = "'.time().'", `expired` = "'.(time()+1*$_POST['time']*60*60*24).'", `ip` = "'.$_POST['ip'].'", `text` = "'.mysql_real_escape_string($_POST['text']).'"');
msg($lang['report_banned_ip']);
header('location: /shoutbox/');
}
}

$title = 'Shoutbox | Banned IP';
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/shoutbox/banned/'.$data['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo $lang['ip_addr'].':<br /><input type="text" name="ip" value="'.$data['ip'].'"/><br />'."\n";
echo $lang['during'].': <br /><input class="disable" disabled="1" size="2" value="1"/> <select name="time"><option value="1">'.ucwords($lang['day']).'</option><option value="7">'.ucwords($lang['week']).'</option><option value="31">'.ucwords($lang['month']).'</option><option value="365">'.ucwords($lang['year']).'</option></select><br />'."\n";
echo $lang['reason'].':<br /><textarea name="text"></textarea><br />'."\n";
echo'<input type="submit" name="banned" value="'.$lang['execute'].'"/> &#183; <a href="/shoutbox/">'.$lang['cancel'].'</a><br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
echo'<div class="menu">&raquo; <a href="/panel/banned.php">'.$lang['panel_banned'].'</a> ('.mysql_result(mysql_query('SELECT COUNT(*) FROM `banned`'), 0).')</div>'."\n";
include'../inc/footer.php';
}
}
break;
}
?>
