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
if(isset($user) && isset($_POST['upload']) && isset($_FILES['file']) && ereg('\.', $_FILES['file']['name'])){
$file = stripcslashes(htmlspecialchars($_FILES['file']['name']));
$name = eregi_replace('\.[^\.]*$', NULL, $file); //nama file tanpa ekstensi
$type = strtolower(eregi_replace('^.*\.', NULL, $file));
$tmp_name = $_FILES['file']['tmp_name'];
$size = filesize($_FILES['file']['tmp_name']);

mysql_query('INSERT INTO `blog_file` SET `id_blog` = "'.$blog['id'].'", `name` = "'.mysql_real_escape_string($name).'", `size` = "'.$size.'", `type` = "'.mysql_real_escape_string($type).'"');
$id = mysql_insert_id();
$upload = move_uploaded_file($tmp_name, root.'post/files/'.$id.'.frf');
if(@$upload){
@chmod(root.'post/files/'.$id.'.frf', 0777);
msg($lang['report_upload']);
}else{
mysql_query('DELETE FROM `blog_file` WHERE `id` = '.$id.' LIMIT 1');
err($lang['failed_upload']);
}
}

$title = $lang['file'].' '.$lang['attachement'].' | '.$blog['title'];
include'../inc/header.php';
$data = mysql_fetch_array(mysql_query('SELECT * FROM `blog_list` WHERE `id` = '.$blog['id_blog'].' LIMIT 1'));
echo'<div class="menu">'."\n";
echo'<a href="/post/" title="Mobile Blog">Blog</a>';
echo' &raquo; <a href="/post/category/'.$data['url'].'.xhtml" title="'.$data['name'].'">'.$data['name'].'</a>';
echo' &raquo; <a href="/post/'.$blog['url'].'.xhtml" title="'.$blog['title'].'">'.$blog['title'].'</a>';
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
var report = confirm("'.$lang['confirm_delete_file'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
$total = mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_file`'), 0);
if($total == 0){
echo'<div class="status">'.$lang['no_attachement'].'</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `blog_file` WHERE `id_blog` = '.$blog['id'].' ORDER BY `name` ASC');
echo'<table class="post">'."\n";
while($files = mysql_fetch_array($result)){
echo'<tr>'."\n";
echo'<td class="icon">'.icon_file($files['type']).'</td>'."\n";
echo'<td class="title"><a href="/post/files/'.$blog['url'].'/download/'.$files['id'].'.xhtml" title="Download '.$files['name'].'.'.$files['type'].'">'.$files['name'].'.'.$files['type'].'</a>';
if(isset($user))
echo' [<a href="javascript:warning(\'/post/files/'.$blog['url'].'/delete/'.$files['id'].'.xhtml\');">X</a>]';
echo'</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="menu" colspan="2">'.$lang['downloads'].': '.$files['count'].'<br />'.$lang['size'].': '.size_file($files['size']).'</td>'."\n";
echo'</tr>'."\n";
}
echo'</table>'."\n";
}
if(isset($user)){
echo'<div class="menu">'."\n";
if(isset($_GET['upload'])){
echo'<form action="/post/files/'.$blog['url'].'/upload.xhtml" method="post" enctype="multipart/form-data">'."\n";
echo $lang['select'].' '.strtolower($lang['file']).':<br /><input type="file" id="form" name="file" size="16"/><br />'."\n";
echo'<input type="submit" name="upload" value="'.$lang['upload'].'"/> &#183; <a href="/post/files/'.$blog['url'].'.xhtml">'.$lang['cancel'].'</a><br />'."\n";
echo'</form>'."\n";
}
else echo'&raquo; <a href="/post/files/'.$blog['url'].'/upload.xhtml#form">'.$lang['upload'].' '.$lang['file'].'</a><br />'."\n";
echo'</div>'."\n";
}
include'../inc/footer.php';
break;
case'download':
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_file` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 1){
$file = mysql_fetch_array(mysql_query('SELECT * FROM `blog_file` WHERE `id` = '.num($_GET['id']).' LIMIT 1'));
if(is_file(root.'post/files/'.$file['id'].'.frf')){
mysql_query('UPDATE `blog_file` SET `count` = "'.($file['count']+1).'" WHERE `id` = '.num($_GET['id']).' LIMIT 1');
BlogFile(root.'post/files/'.$file['id'].'.frf', $file['name'].'.'.$file['type'], MimeType($file['type']));
exit;
}
}else{
header('refresh: 3; url=/index.php?'.SID);
header('Content-type: text/html', NULL, 404);
echo'<html>'."\n";
echo'<head>'."\n";
echo'<title>'.$lang['title_404'].'</title>'."\n";
echo'<link rel="stylesheet" type="text/css" href="/data/themes/'.$set['themes'].'/style.css"/>'."\n";
echo'</head>'."\n";
echo'<body>'."\n";
echo'<div class="err">'.$lang['404'].'<br /><a href="/?'.SID.'" title="">Home</a><br /></div>'."\n";
echo'</body>'."\n";
echo'</html>'."\n";
exit;
}
break;
case'delete':
if(!isset($user)){
err($lang['not_login']);
header('location: /?'.SID);
exit;
}else{
if(is_file(root.'post/files/'.num($_GET['id']).'.frf') && mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_file` WHERE `id` = '.num($_GET['id']).' LIMIT 1'), 0) == 1){
@unlink(root.'post/files/'.num($_GET['id']).'.frf');
mysql_query('DELETE FROM `blog_file` WHERE `id` = '.num($_GET['id']).' LIMIT 1');
msg($lang['file'].' '.$lang['report_delete']);
header('location: /post/files/'.$blog['url'].'.xhtml');
}else{
err($lang['file'].' '.$lang['not_found']);
header('location: /post/files/'.$blog['url'].'.xhtml');
}
}
break;
}
}
?>
