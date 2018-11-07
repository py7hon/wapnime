<?php

include'../inc/root.php';
include'../inc/session.php';
include'../inc/lang.php';
include'../inc/connect.php';
include'../inc/fnc.php';
include'../inc/ipua.php';
include'../inc/system.php';
include'../inc/counter.php';

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['url']).'" LIMIT 1'), 0) == 0){
err($lang['article'].' '.$lang['not_found']);
header('location: /post/');
exit;
}else{
$blog = mysql_fetch_array(mysql_query('SELECT * FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['url']).'" LIMIT 1'));

switch($_GET['act']){
default:
if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
$locate = $_SERVER['HTTP_REFERER'];
else $locate = $_SERVER['REQUEST_URI'];

if(isset($_POST['name']) && isset($_POST['text'])){
if(!isset($_SESSION['name']) or $_SESSION['name'] != $_POST['name'])
$_SESSION['name'] = $_POST['name'];
if(!isset($_SESSION['mail']) or $_SESSION['mail'] != $_POST['mail'])
$_SESSION['mail'] = $_POST['mail'];
if(!isset($_SESSION['url']) or $_SESSION['url'] != $_POST['url'])
$_SESSION['url'] = $_POST['url'];
if(!isset($_SESSION['text']) or $_SESSION['text'] != $_POST['text'])
$_SESSION['text'] = $_POST['text'];

if(strlen($_POST['name']) < 3){
err($lang['name'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 8){
err($lang['name'].' '.$lang['error_long'].' 8 '.$lang['characters'].'.');
}elseif(!preg_match('#^([A-z0-9])+$#ui', $_POST['name'])){
err($lang['invalid_name']);
}elseif(antispam($_POST['name'])){
err($lang['block_name']);
}elseif(empty($_POST['mail'])){
err($lang['empty_email']);
}elseif(strlen($_POST['mail']) > 32){
err($lang['email'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(!preg_match('#[0-9a-z_]+@[0-9a-z_^\-.]+\.[a-z]{2,4}#i', $_POST['mail'])){
err($lang['invalid_email']);
}elseif(strlen($_POST['url']) > 32){
err($lang['url'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) < 10){
err($lang['comment'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > $set['char']){
err($lang['comment'].' '.$lang['error_long'].' '.$set['char'].' '.$lang['characters'].'.');
}elseif($_POST['code'] != $_SESSION['code']){
err($lang['invalid_captcha']);
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `name` = "'.mysql_real_escape_string($_POST['name']).'" AND `text` = "'.mysql_real_escape_string($_POST['text']).'" AND `time` > "'.(time()-300).'" AND `type` = "blog" AND `id_type` = '.$blog['id'].' LIMIT 1'), 0) != 0){
err($lang['spam_comment']);
}else{
mysql_query('INSERT INTO `comment` SET `time` = "'.time().'", `name` = "'.mysql_real_escape_string($_POST['name']).'", `mail` = "'.mysql_real_escape_string($_POST['mail']).'", `url` = "'.mysql_real_escape_string($_POST['url']).'", `text` = "'.mysql_real_escape_string($_POST['text']).'", `ua` = "'.mysql_real_escape_string($ua).'", `phone` = "'.mysql_real_escape_string($phone).'", `ip` = "'.$ip.'", `type` = "blog", `id_type` = '.$blog['id']);
header('location: '.$locate);
}
}

$title = $lang['comment'].' | '.$blog['title'];
include'../inc/header.php';
$data = mysql_fetch_array(mysql_query('SELECT * FROM `blog_list` WHERE `id` = '.$blog['id_blog'].' LIMIT 1'));
echo'<div class="menu">'."\n";
echo'<a href="/post/" title="Mobile Blog">Blog</a>'."\n";
echo' &raquo; <a href="/post/category/'.$data['url'].'.xhtml" title="'.$data['name'].'">'.$data['name'].'</a>'."\n";
echo' &raquo; <a href="/post/'.$blog['url'].'.xhtml" title="'.$blog['title'].'">'.$blog['title'].'</a>'."\n";
echo'</div>'."\n";
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
var report = confirm("'.$lang['confirm_delete_comment'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$blog['id']), 0);
$k_page = k_page($k_post, $set['page']);
$page = page($k_page);
$start = $set['page']*$page-$set['page'];
if($k_post == 0){
echo'<div class="status">'.$lang['no_comment_article'].' <b>'.$blog['title'].'</b>.</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$blog['id'].' ORDER BY `id` DESC LIMIT '.$start.', '.$set['page']);
echo'<table class="post">'."\n";
while($post = mysql_fetch_array($result)){
if($set['time'] == 1)
$time = countdown($post['time']);
else $time = showdate($post['time']).' - '.showtime($post['time']);
if(trim($post['url']) != 'http://')
$name = '<a href="'.$post['url'].'">'.rainbow($post['name']).'</a>'.partner($post['name']);
else $name = rainbow($post['name']).partner($post['name']);
echo'<tr>'."\n";
echo'<td class="icon"><img src="/icons/comment.png" alt="*"/></td>'."\n";
echo'<td class="title"><b><span style="text-shadow:black 0.05em 0.05em 0.05em">'.strtolower($name).'</span></b> ['.provider($post['ip']).']</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="menu" colspan="2">'."\n";
echo'<div style="text-align:right">'.$time.'</div>'."\n";
echo smiles(bbcode(sensor(htmlspecialchars(stripslashes($post['text']))))).'<br />'."\n";
echo'<div style="text-align:right">'.browser($post['ua']).' '.$post['phone'].'</div>'."\n";
if(isset($user)){
echo'[<a href="javascript:warning(\'/post/comment/'.$blog['url'].'/delete/'.$post['id'].'.xhtml\')">D</a>] &#183; ';
echo'[<a href="/post/comment/'.$blog['url'].'/banned/'.$post['id'].'.xhtml">B</a>] &#183; ';
echo'[<a href="/post/comment/'.$blog['url'].'/edit/'.$post['id'].'.xhtml">E</a>] &#183; ';
echo'[<a href="/post/comment/'.$blog['url'].'/reply/'.$post['id'].'.xhtml">R</a>]';
}
echo'</td>'."\n";
echo'</tr>'."\n";
}
echo'</table>'."\n";
if($k_page > 1)
nav('/post/comment/'.$blog['url'].'/', $k_page, $page);
}
echo'<form action="/post/comment/'.$blog['url'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo $lang['name'].' :<br />';
if(isset($_SESSION['name']))
echo'<input type="text" name="name" class="input" value="'.$_SESSION['name'].'"/>'."\n";
else echo'<input type="text" name="name" class="input"/>'."\n";
echo'<br />'."\n";
echo $lang['email'].' :<br />';
if(isset($_SESSION['mail'])){
echo'<input type="text" name="mail" class="input" value="'.$_SESSION['mail'].'"/>'."\n";
unset($_SESSION['mail']);
}
else echo'<input type="text" name="mail" class="input"/>'."\n";
echo'<br />'."\n";
echo $lang['site'].' :<br />';
if(isset($_SESSION['url']))
echo'<input type="text" name="url" class="input" value="'.$_SESSION['url'].'"/>'."\n";
else echo'<input type="text" name="url" class="input" value="http://"/>'."\n";
echo'<br />'."\n";
echo $lang['comment'].' :<br />';
if(isset($_SESSION['text'])){
echo'<textarea name="text" class="textarea" cols="auto" rows="auto">'.$_SESSION['text'].'</textarea>'."\n";
unset($_SESSION['text']);
}
else echo'<textarea name="text" class="textarea" cols="auto" rows="auto"></textarea>'."\n";
echo'<br />'."\n";
echo'Captcha : <img src="/captcha.php" alt="'.$_SESSION['code'].'"/><br /><input type="text" name="code" class="input"/><br />'."\n";
echo'<input type="submit" value="Posting"/><br />'."\n";
echo'[<a href="/smiles.php" title="Smiles Code">Smiles</a>: <font class="on">On</font>]  [<a href="/bbcode.php" title="BB Code">BB-Code</a>: <font class="on">On</font>]<br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
include'../inc/footer.php';
break;
case'delete':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['comment'].' '.$lang['not_found']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}else{
mysql_query('DELETE FROM `comment` WHERE `id` = '.num($_GET['id']).' LIMIT 1');
msg($lang['comment'].' '.$lang['report_delete']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}
}
break;
case'edit':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['comment'].' '.$lang['not_found']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}else{
$edit = mysql_fetch_array(mysql_query('SELECT * FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'));

if(isset($_POST['edit'])){
if(strlen($_POST['name']) < 3){
err($lang['name'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 8){
err($lang['name'].' '.$lang['error_long'].' 8 '.$lang['characters'].'.');
}elseif(!preg_match('#^([A-z0-9])+$#ui', $_POST['name'])){
err($lang['invalid_name']);
}elseif(empty($_POST['mail'])){
err($lang['empty_email']);
}elseif(strlen($_POST['mail']) > 32){
err($lang['email'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(!preg_match('#[0-9a-z_]+@[0-9a-z_^\-.]+\.[a-z]{2,4}#i', $_POST['mail'])){
err($lang['invalid_email']);
}elseif(strlen($_POST['url']) > 32){
err($lang['url'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) < 10){
err($lang['comment'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > 500){
err($lang['comment'].' '.$lang['error_long'].' 500 '.$lang['characters'].'.');
}else{
mysql_query('UPDATE `comment` SET `name` = "'.mysql_real_escape_string($_POST['name']).'", `mail` = "'.mysql_real_escape_string($_POST['mail']).'", `url` = "'.mysql_real_escape_string($_POST['url']).'", `text` = "'.mysql_real_escape_string($_POST['text']).'" WHERE `id` = '.num($_POST['id']));
msg($lang['comment'].' '.$lang['report_edit']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}
}

$title = $lang['edit'].' '.$lang['comment'].' | '.$blog['title'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/post/comment/'.$blog['url'].'/edit/'.$edit['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo'<input type="hidden" name="id" class="input" value="'.$edit['id'].'"/>'."\n";
echo $lang['name'].' :<br /><input type="text" name="name" class="input" value="'.$edit['name'].'"/><br />'."\n";
echo $lang['email'].' :<br /><input type="text" name="mail" class="input" value="'.$edit['mail'].'"/><br />'."\n";
echo $lang['site'].' :<br /><input type="text" name="url" class="input" value="'.$edit['url'].'"/><br />'."\n";
echo $lang['comment'].' :<br /><textarea name="text" class="textarea" cols="auto" rows="auto">'.$edit['text'].'</textarea><br />'."\n";
echo'<input type="submit" name="edit" value="'.$lang['update'].'"/> &#183; <a href="/post/comment/'.$blog['url'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
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
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['comment'].' '.$lang['not_found']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}else{
$reply = mysql_fetch_array(mysql_query('SELECT * FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'));

if(strlen($reply['text']) > 50)
$msg = substr($reply['text'], 0, 50).'...';
else $msg = $reply['text'];

if(isset($_POST['reply'])){
if(strlen($_POST['text']) < 5){
err($lang['msg'].' '.$lang['error_short'].' 5 '.$lang['characters'].'.');
}elseif(strlen($_POST['text']) > 500){
err($lang['msg'].' '.$lang['error_long'].' 500 '.$lang['characters'].'.');
}else{
mysql_query('INSERT INTO `comment` SET `time` = "'.time().'", `name` = "Admin", `mail` = "'.$set['mail'].'", `url` = "http://'.$_SERVER['HTTP_HOST'].'", `text` = "'.mysql_real_escape_string('[b]Quote '.$reply['name'].'...[/b][quote]'.$msg.'[/quote]'.$_POST['text']).'", `ua` = "'.mysql_real_escape_string($ua).'", `phone` = "'.mysql_real_escape_string($phone).'", `ip` = "'.$ip.'", `type` = "blog", `id_type` = '.$reply['id_type']);
msg($lang['comment'].' '.$lang['report_add']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}
}

$title = $lang['reply'].' '.$lang['comment'].' | '.$blog['title'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/post/comment/'.$blog['url'].'/reply/'.$reply['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo'<b>'.$reply['name'].' '.$lang['says'].'</b><br />'."\n";
echo'<div class="quote">'.$msg.'</div>'."\n";
echo'<b>'.$lang['respons'].'</b><br /><textarea name="text"></textarea><br />'."\n";
echo'<input type="submit" name="reply" value="'.$lang['reply'].'"/> &#183; <a href="/post/comment/'.$blog['url'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
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
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 0){
err($lang['comment'].' '.$lang['not_found']);
header('location: /post/comment/'.$blog['url'].'.xhtml');
}else{
$data = mysql_fetch_array(mysql_query('SELECT `id`, `ip` FROM `comment` WHERE `type` = "blog" AND `id` = '.num($_GET['id']).' LIMIT 1'));

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
header('location: /post/comment/'.$blog['url'].'.xhtml');
}
}

$title = 'Banned IP | '.$blog['title'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/post/comment/'.$blog['url'].'/banned/'.$data['id'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo $lang['ip_addr'].' :<br /><input type="text" name="ip" value="'.$data['ip'].'"/><br />'."\n";
echo $lang['during'].' : <br /><input class="disable" disabled="1" size="2" value="1"/> <select name="time"><option value="1">'.ucwords($lang['day']).'</option><option value="7">'.ucwords($lang['week']).'</option><option value="31">'.ucwords($lang['month']).'</option><option value="365">'.ucwords($lang['year']).'</option></select><br />'."\n";
echo $lang['reason'].' :<br /><textarea name="text"></textarea><br />'."\n";
echo'<input type="submit" name="banned" value="'.$lang['execute'].'"/> &#183; <a href="/post/comment/'.$blog['url'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
echo'<div class="menu">&raquo; <a href="/panel/banned.php">'.$lang['panel_banned'].'</a> ('.mysql_result(mysql_query('SELECT COUNT(*) FROM `banned`'), 0).')</div>'."\n";
include'../inc/footer.php';
}
}
break;
}
}
?>
