<?php

include'../inc/root.php';
include'../inc/session.php';
include'../inc/lang.php';
include'../inc/connect.php';
include'../inc/fnc.php';
include'../inc/ipua.php';
include'../inc/system.php';
include'../inc/counter.php';

switch($_GET['mode']){
default:
if(isset($user)){
if(isset($_POST['add'])){
if(strlen($_POST['name']) < 3){
err($lang['category'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 32){
err($lang['category'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(!preg_match('#^([a-zA-Z 0-9_-]*)$#ui', $_POST['name'])){
err($lang['invalid_folder']);
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list` WHERE `name` = "'.mysql_real_escape_string($_POST['name']).'"'), 0) != 0){
err($lang['category'].' "'.$_POST['name'].'" '.$lang['report_spam']);
}else{
mysql_query('INSERT INTO `blog_list` SET `name` = "'.mysql_real_escape_string($_POST['name']).'", `url` = "'.mysql_real_escape_string(strtolower(str_replace(' ', '-', $_POST['name']))).'"');
msg($lang['category'].' '.$lang['report_create']);
}
}
if(isset($_POST['edit'])){
if(strlen($_POST['name']) < 3){
err($lang['category'].' '.$lang['error_short'].' 3 '.$lang['characters'].'.');
}elseif(strlen($_POST['name']) > 32){
err($lang['category'].' '.$lang['error_long'].' 32 '.$lang['characters'].'.');
}elseif(!preg_match('#^([a-zA-Z 0-9_-]*)$#ui', $_POST['name'])){
err($lang['invalid_folder']);
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list` WHERE `name` = "'.mysql_real_escape_string($_POST['name']).'"'), 0) != 0){
err($lang['category'].' "'.$_POST['name'].'" '.$lang['report_spam']);
}else{
mysql_query('UPDATE `blog_list` SET `name` = "'.mysql_real_escape_string($_POST['name']).'", `url` = "'.mysql_real_escape_string(strtolower(str_replace(' ', '-', $_POST['name']))).'" WHERE `id` = '.num($_POST['id']));
msg($lang['category'].' '.$lang['report_edit']);
}
}
if(isset($_GET['delete']) && $_GET['delete'] != NULL){
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list` WHERE `id` = '.num($_GET['delete']).' LIMIT 1'), 0) == 0){
err($lang['no_data']);
}else{
mysql_query('DELETE FROM `blog_list` WHERE `id` = '.num($_GET['delete']).' LIMIT 1');
mysql_query('DELETE FROM `blog` WHERE `id_blog` = '.num($_GET['delete']));
msg($lang['category'].' '.$lang['report_delete']);
}
}
}

$title = 'Post';
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
var report = confirm("'.$lang['confirm_delete_category'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog`'), 0);
$k_page = k_page($k_post, $set['page']);
$page = page($k_page);
$start = $set['page']*$page-$set['page'];
if($k_post == 0){
echo'<div class="status">'.$lang['no_article'].'</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `blog` ORDER BY `id` DESC LIMIT '.$start.', '.$set['page']);
while($post = mysql_fetch_array($result)){
$new = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `time` > '.(time()-86400).' AND `id` = '.$post['id']), 0);
if($new == 0)
$new = NULL;
else $new = ' [<font class="off">New!</font>]';
$data = mysql_fetch_array(mysql_query('SELECT * FROM `blog_list` WHERE `id` = '.$post['id_blog'].' LIMIT 1'));
echo'<div class="title"><h1>'.$post['id'].') <a href="/post/'.$post['url'].'.xhtml" title="'.$post['title'].'">'.$post['title'].'</a>'.$new.'</h1></div>'."\n";
echo'<div class="menu">'."\n";
echo $lang['category'].': <a href="/post/category/'.$data['url'].'.xhtml" title="'.$data['name'].'">'.$data['name'].'</a><br />'."\n";
echo'<span>'.showdate($post['time']).' - '.showtime($post['time']).'</span><br />'."\n";
echo'<div class="line"></div>'."\n";
if(strlen($post['text']) > 150)
echo htmlspecialchars(stripslashes(substr($post['text'], 0, 150))).'... <a href="/post/'.$post['url'].'.xhtml" title="'.$post['title'].'">'.$lang['read_more'].'</a>';
else echo htmlspecialchars(stripslashes($post['text']));
echo'<br />'."\n";
echo'['.$lang['view'].': <span>'.$post['view'].'</span>] ['.$lang['comment'].': <a href="/post/comment/'.$post['url'].'.xhtml" title="Comments '.$post['title'].'">'.mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$post['id']), 0).'</a>]<br />'."\n";
echo'</div>'."\n";
}
if($k_page > 1)
str('?', $k_page, $page);
}

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list`'), 0) != 0){
echo'<div class="title"><b>'.$lang['categories'].'</b></div>'."\n";
$result = mysql_query('SELECT * FROM `blog_list` ORDER BY `name` ASC');
echo'<div class="menu">'."\n";
echo'<img src="images/rss.png" alt="&raquo;"/> <a href="rss.xml">RSS-Feed</a><br />'."\n";
while($post = mysql_fetch_array($result)){
$all = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `id_blog` = '.$post['id']), 0);
$new = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `time` > "'.(time()-86400).'" AND `id_blog` = '.$post['id']), 0);
if($new == 0)
$new = NULL;
else $new = '/+<font class="off">'.$new.'</font>';
echo'<img src="images/line.png" alt="&raquo;"/> <a href="/blog/category/'.$post['url'].'.xhtml" title="'.$post['name'].'">'.$post['name'].'</a> ('.$all.$new.')';
if(isset($user)){
if(isset($_GET['edit']) && $_GET['edit'] == $post['id']){
echo'<div id="form">'."\n";
echo'<form action="./" method="post">'."\n";
echo'<input type="hidden" name="id" value="'.$post['id'].'"/>'."\n";
echo $lang['category'].' :<br /><input type="text" name="name" value="'.$post['name'].'"/><br />'."\n";
echo'<input type="submit" name="edit" value="'.$lang['update'].'"/> &#183; <a href="./">'.$lang['cancel'].'</a><br />'."\n";
echo'</form>'."\n";
echo'</div>'."\n";
}else{
echo' [<a href="javascript:warning(\'?delete='.$post['id'].'\');">D</a>] [<a href="?edit='.$post['id'].'#form">E</a>]<br />'."\n";
}
}
else echo'<br />'."\n";
}
echo'</div>'."\n";
}

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" LIMIT 1'), 0) != 0){
$result = mysql_query('SELECT * FROM `comment` WHERE `type` = "blog" ORDER BY `time` DESC LIMIT 5');
echo'<div class="main"><b>'.$lang['comments'].'</b></div>'."\n";
while($post = mysql_fetch_array($result)){
$data = mysql_fetch_array(mysql_query('SELECT * FROM `blog` WHERE `id` = '.$post['id_type'].' LIMIT 1'));
echo'<div class="menu">'."\n";
echo'<img src="images/comment.png" alt="&raquo;"/> <u>'.strtolower($post['name']).'</u><br />'."\n";
echo'<div class="line"></div>'."\n";
if(strlen($post['text']) > 75)
echo htmlspecialchars(stripslashes(substr($post['text'], 0, 75))).'... <a href="/post/'.$data['url'].'.xhtml#'.$post['id'].'">'.$lang['more'].'...</a>'."\n";
else echo htmlspecialchars(stripslashes($post['text'])).' <a href="/post/'.$data['url'].'.xhtml#'.$post['id'].'">'.$lang['more'].'...</a>'."\n";
echo'</div>'."\n";
}
}
if(isset($user)){
echo'<div class="menu">'."\n";
if(isset($_GET['act']) && $_GET['act'] == 'add'){
echo'<div id="form">'."\n";
echo'<form action="?act=add" method="post">'."\n";
echo $lang['category'].' :<br /><input type="text" name="name"/><br />'."\n";
echo'<input type="submit" name="add" value="Submit"/> &#183; <a href="./">'.$lang['cancel'].'</a><br />'."\n";
echo'</form>'."\n";
echo'</div>'."\n";
}
else echo'&raquo; <a href="?act=add#form">'.$lang['create'].' '.$lang['category'].'</a><br />'."\n";
echo'</div>'."\n";
}
include'../inc/footer.php';
break;
case'rss':
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog`'), 0) == 0)
exit;

header('Content-type: text/xml; charset=utf-8');
echo'<?xml version="1.0" encoding="utf-8"?>'."\n";
echo'<rss version="2.0">'."\n";
echo'<channel>'."\n";
echo'<title>RSS Post</title>'."\n";
echo'<link>http://'.$_SERVER['HTTP_HOST'].'/post/</link>'."\n";
echo'<description>'.$set['desc'].'</description>'."\n";
echo'<language>en-id</language>'."\n";
echo'<pubDate>'.gmdate('D, d M Y H:i:s', time()+25200).'</pubDate>'."\n";
echo'<webMaster>'.$set['mail'].'</webMaster>'."\n";

$result = mysql_query('SELECT * FROM `blog` ORDER BY `id` DESC LIMIT '.$set['page']);
while($post = mysql_fetch_array($result)){
echo'<item>'."\n";
echo'<title>'.$post['title'].'</title>'."\n";
echo'<link>http://'.$_SERVER['HTTP_HOST'].'/post/'.$post['url'].'.xhtml</link>'."\n";
echo'<description><![CDATA[';
echo smiles(admcode(bbcode(htmlspecialchars(stripslashes($post['text'])))));
echo']]></description>'."\n";
echo'<pubDate>'.gmdate('D, d M Y H:i:s', $post['time']).'</pubDate>'."\n";
echo'</item>'."\n";
}
echo'</channel>'."\n";
echo'</rss>'."\n";
break;
}
?>
