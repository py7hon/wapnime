<?php

include'../inc/root.php';
include'../inc/session.php';
include'../inc/lang.php';
include'../inc/connect.php';
include'../inc/fnc.php';
include'../inc/ipua.php';
include'../inc/system.php';
include'../inc/counter.php';

if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list` WHERE `url` = "'.mysql_real_escape_string($_GET['url']).'" LIMIT 1'), 0) == 0){
err($lang['category'].' '.$lang['not_found']);
header('location: /post/');
exit;
}else{
$blog = mysql_fetch_array(mysql_query('SELECT * FROM `blog_list` WHERE `url` = "'.mysql_real_escape_string($_GET['url']).'" LIMIT 1'));

switch($_GET['act']){
default:
if(isset($user) && isset($_POST['add'])){
if(strlen($_POST['title']) < 10){
err($lang['article_title'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['title']) > 100){
err($lang['article_title'].' '.$lang['error_long'].' 100 '.$lang['characters'].'.');
}elseif(strlen($_POST['url']) < 10){
err($lang['url'].' '.$lang['article'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['url']) > 100){
err($lang['url'].' '.$lang['article'].' '.$lang['error_short'].' 100 '.$lang['characters'].'.');
}elseif(!preg_match('#^([a-zA-Z 0-9_-]*)$#ui', $_POST['url'])){
err($lang['invalid_url']);
}elseif(strlen($_POST['text']) < 10){
err($lang['article_desc'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `title` = "'.mysql_real_escape_string($_POST['title']).'" AND `text` = "'.mysql_real_escape_string($_POST['text']).'" AND `time` > "'.(time()-86400).'" AND `id_blog` = '.$blog['id'].' LIMIT 1'), 0) != 0){
err($lang['article'].' "'.$_POST['title'].'" '.$lang['report_spam']);
}else{
mysql_query('INSERT INTO `blog` SET `time` = "'.time().'", `title` = "'.mysql_real_escape_string($_POST['title']).'", `url` = "'.mysql_real_escape_string(strtolower(str_replace(' ', '-', $_POST['url']))).'", `text` = "'.mysql_real_escape_string($_POST['text']).'", `id_blog` = '.$blog['id']);
msg($lang['article'].' '.$lang['report_create']);
}
}

$title = $blog['name'];
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
var report = confirm("'.$lang['confirm_delete_article'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `id_blog` = '.$blog['id']), 0);
$k_page = k_page($k_post, $set['page']);
$page = page($k_page);
$start = $set['page']*$page-$set['page'];
if($k_post == 0){
echo'<div class="status">'.$lang['no_article_category'].' <b>'.$blog['name'].'</b>.</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `blog` WHERE `id_blog` = '.$blog['id'].' ORDER BY `id` DESC LIMIT '.$start.', '.$set['page']);
while($post = mysql_fetch_array($result)){
$new = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `time` > '.(time()-86400).' AND `id` = '.$post['id']), 0);
if($new == 0)
$new = null;
else $new = ' [<font class="off">New!</font>]';
echo'<div class="title"><h1>'.$post['id'].'. <a href="/post/'.$post['url'].'.xhtml" title="'.$post['title'].'">'.$post['title'].'</a>'.$new.'</h1></div>'."\n";
echo'<div class="menu">'."\n";
echo'<div style="text-align:right"><span>'.showdate($post['time']).' - '.showtime($post['time']).'</span></div>'."\n";
echo'<div class="line"></div>'."\n";
if(strlen($post['text']) > 150)
echo htmlspecialchars(stripslashes(substr($post['text'], 0, 150))).'... <a href="/post/'.$post['url'].'.xhtml" title="'.$post['title'].'">'.$lang['read_more'].'</a>';
else echo htmlspecialchars(stripslashes($post['text']));
echo'<br />'."\n";
if(isset($user))
echo'[<a href="javascript:warning(\'/post/category/'.$blog['url'].'/delete/'.$post['url'].'.xhtml\');">'.$lang['delete'].'</a>] &#183; [<a href="/post/category/'.$blog['url'].'/edit/'.$post['url'].'.xhtml">'.$lang['edit'].'</a>]<br />'."\n";
else echo'['.$lang['view'].': <span>'.$post['view'].'</span>] ['.$lang['comment'].': <a href="/post/comment/'.$post['url'].'.xhtml" title="Comment '.$post['title'].'">'.mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$post['id']), 0).'</a>]<br />'."\n";
echo'</div>'."\n";
}
if($k_page > 1)
nav('/post/category/'.$blog['url'].'/', $k_page, $page);
}
echo'<div class="menu">'."\n";
echo'&raquo; <span>'.$blog['name'].'</span><br />'."\n";
echo'&laquo; <a href="/post/" title="Post">Post</a><br />'."\n";
if(isset($user))
echo'&raquo; <a href="/post/category/'.$blog['url'].'/add.xhtml#form">'.$lang['create'].' '.$lang['article'].'</a><br />'."\n";
if(isset($_GET['add']) && isset($user)){
echo'<div class="line"></div>'."\n";
echo'<div id="form">'."\n";
echo'<form action="/post/category/'.$blog['url'].'/add.xhtml" method="post">'."\n";
echo $lang['article_title'].' :<br /><input type="text" name="title" class="input"/><br />'."\n";
echo $lang['url'].' '.$lang['article'].' :<br /><input type="text" name="url" class="input"/><br />'."\n";
echo $lang['category'].' :<br /><input class="input" disabled="1" value="'.$blog['name'].'"/><br />'."\n";
echo $lang['article_desc'].' :<br /><textarea name="text" class="textarea" cols="auto" rows="auto"/></textarea><br />'."\n";
echo'<input type="submit" name="add" value="'.$lang['create'].'"/> &#183; <a href="/post/category/'.$blog['link'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
echo'</form>'."\n";
echo'</div>'."\n";
}
echo'</div>'."\n";
include'../inc/footer.php';
break;
case'delete':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'" LIMIT 1'), 0) == 0){
err($lang['article'].' '.$lang['not_found']);
header('location: /post/category/'.$blog['url'].'.xhtml');
}else{
$data = mysql_fetch_array(mysql_query('SELECT `id` FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'" LIMIT 1'));
mysql_query('DELETE FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$data['id']);
$result = mysql_query('SELECT * FROM `blog_file` WHERE `id_blog` = '.$data['id']);
while($files = mysql_fetch_array($result)){
@unlink(root.'post/files/'.$files['id'].'.frf');
}
mysql_query('DELETE FROM `blog_file` WHERE `id_blog` = '.$data['id']);
mysql_query('DELETE FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'" LIMIT 1');
msg($lang['article'].' '.$lang['report_delete']);
header('location: /post/category/'.$blog['url'].'.xhtml');
}
}
break;
case'edit':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'" LIMIT 1'), 0) == 0){
err($lang['article'].' '.$lang['not_found']);
header('location: /post/category/'.$blog['url'].'.xhtml');
}else{
$edit = mysql_fetch_array(mysql_query('SELECT * FROM `blog` WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'" LIMIT 1'));
if(isset($_POST['edit'])){
if(strlen($_POST['title']) < 10){
err($lang['article_title'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['title']) > 100){
err($lang['article_title'].' '.$lang['error_long'].' 100 '.$lang['characters'].'.');
}elseif(strlen($_POST['url']) < 10){
err($lang['url'].' '.$lang['article'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(strlen($_POST['url']) > 100){
err($lang['url'].' '.$lang['article'].' '.$lang['error_long'].' 100 '.$lang['characters'].'.');
}elseif(!preg_match('#^([a-zA-Z 0-9_-]*)$#ui', $_POST['url'])){
err($lang['invalid_url']);
}elseif(strlen($_POST['text']) < 10){
err($lang['article_desc'].' '.$lang['error_short'].' 10 '.$lang['characters'].'.');
}elseif(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_list` WHERE `id` = '.intval($_POST['id']).' LIMIT 1'), 0) == 0){
err($lang['invalid_category']);
}else{
mysql_query('UPDATE `blog` SET `title` = "'.mysql_real_escape_string($_POST['title']).'", `url` = "'.mysql_real_escape_string(strtolower(str_replace(' ', '-', $_POST['url']))).'", `text` = "'.mysql_real_escape_string($_POST['text']).'", `id_blog` = '.intval($_POST['id']).' WHERE `url` = "'.mysql_real_escape_string($_GET['blog']).'"');
msg($lang['article'].' '.$lang['report_edit']);
header('location: /post/'.strtolower(str_replace(' ', '-', $_POST['url'])).'.xhtml');
exit;
}
}

$title = $lang['edit'].' '.$lang['article'].' | '.$edit['title'];
include'../inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}
echo'<form action="/post/category/'.$blog['url'].'/edit/'.$_GET['blog'].'.xhtml" method="post">'."\n";
echo'<div class="menu">'."\n";
echo $lang['article_title'].' :<br /><input type="text" name="title" class="input" value="'.$edit['title'].'"/><br />'."\n";
echo $lang['url'].' '.$lang['article'].' :<br /><input type="text" name="url" class="input" value="'.$edit['url'].'"/><br />'."\n";
echo $lang['category'].' :<br /><select name="id">';
$query = mysql_query('SELECT * FROM `blog_list` ORDER BY `name` ASC');
while($data = mysql_fetch_array($query)){
echo'<option value="'.$data['id'].'" '.($edit['id_blog'] == $data['id'] ? 'selected="selected"' : null).'>'.$data['name'].'</option>';
}
echo'</select><br />'."\n";
echo $lang['article_desc'].' :<br /><textarea name="text" class="textarea" cols="auto" rows="auto">'.$edit['text'].'</textarea><br />'."\n";
echo'<input type="submit" name="edit" value="'.$lang['update'].'"/> &#183; <a href="/post/category/'.$blog['url'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
include'../inc/footer.php';
}
}
break;
}
}
?>
