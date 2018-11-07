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

if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL && $_SERVER['HTTP_REFERER'] != 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
mysql_query('UPDATE `blog` SET `view` = '.($blog['view']+1).' WHERE `id` = '.$blog['id'].' LIMIT 1');

$title = $blog['title'];
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
var report = confirm("'.$lang['confirm_delete_comment'].'");
if(report)
location.href = url;
else return;
}
</script>';
}
$data = mysql_fetch_array(mysql_query('SELECT * FROM `blog_list` WHERE `id` = '.$blog['id_blog'].' LIMIT 1'));
echo'<div class="menu">'."\n";
echo'<table style="width:100%">'."\n";
echo'<tr>'."\n";
echo'<td style="width:30%" valign="top">'.$lang['category'].'</td>'."\n";
echo'<td style="width:70%" valign="top">: <a href="/post/category/'.$data['url'].'.xhtml">'.$data['name'].'</a></td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td style="width:30%" valign="top">'.$lang['times'].'</td>'."\n";
echo'<td style="width:70%" valign="top">: <span>'.showdate($blog['time']).'</span></td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td style="width:30%" valign="top">'.$lang['view'].'</td>'."\n";
echo'<td style="width:70%" valign="top">: <span>'.$blog['view'].' '.$lang['time'].'</span></td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td style="width:30%" valign="top">'.$lang['comment'].'</td>'."\n";
echo'<td style="width:70%" valign="top">: <a href="/post/comment/'.$blog['url'].'.xhtml">'.mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$blog['id']), 0).'</a></td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
echo'</div>'."\n";
echo'<div class="menu">'."\n";
echo'<h1>'.$blog['title'].'</h1><br />'."\n";
echo smiles(bbcode(admcode(htmlspecialchars(stripslashes($blog['text']))))).'<br /><br />'."\n";
echo'<b>'.$lang['share_on'].':</b><br /><a href="http://m.facebook.com/sharer.php?u=http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'"><img src="/post/images/facebook.gif" title="Share on Facebook" alt="Facebook"/></a> <a href="http://mobile.twitter.com/home?status=http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'"><img src="/post/images/twitter.gif" title="Share on Twitter" alt="Twitter"/></a><br />'."\n";
echo'</div>'."\n";
echo'<div class="menu">&raquo; <a href="/post/files/'.$blog['url'].'.xhtml" title="File Attachement '.$blog['title'].'">'.$lang['file'].' '.$lang['attachement'].'</a> ('.mysql_result(mysql_query('SELECT COUNT(*) FROM `blog_file` WHERE `id_blog` = '.$blog['id']), 0).')</div>'."\n";
if(mysql_result(mysql_query('SELECT COUNT(*) FROM `blog` WHERE `id_blog` = '.$blog['id_blog'].' LIMIT 1'), 0) > 1){
$result = mysql_query('SELECT * FROM `blog` WHERE `id_blog` = '.$blog['id_blog'].' AND `title` != "'.mysql_real_escape_string($blog['title']).'" ORDER BY rand() ASC LIMIT 3');
echo'<div class="title"><h2><img src="/post/icons/default.png" alt="&bull;"/> '.$lang['recent_post'].'</h2></div>'."\n";
echo'<div class="menu">'."\n";
while($post = mysql_fetch_array($result)){
echo'<img src="/post/images/line.png" alt="&raquo;"/> <a href="/post/'.$post['url'].'.xhtml" title="'.$post['title'].'">'.$post['title'].'</a><br />'."\n";
}
echo'</div>'."\n";
}
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$blog['id']), 0);
$k_page = k_page($k_post, $set['page']);
$page = page($k_page);
$start = $set['page']*$page-$set['page'];
if($page > 1)
$back = '<a href="/post/'.$blog['url'].'/page-'.($page-1).'.xhtml">&laquo;</a>';
else $back = '&laquo;';
if($page < $k_page)
$next = '<a href="/post/'.$blog['url'].'/page-'.($page+1).'.xhtml">&raquo;</a>';
else $next = '&raquo;';
if($k_post == 0){
echo'<div class="status">'.$lang['no_comment_article'].' <b>'.$blog['title'].'</b>.</div>'."\n";
}else{
$result = mysql_query('SELECT * FROM `comment` WHERE `type` = "blog" AND `id_type` = '.$blog['id'].' ORDER BY `id` DESC LIMIT '.$set['page']);
echo'<table class="post">'."\n";
while($post = mysql_fetch_array($result)){
if($set['time'] == 1)
$time = countdown($post['time']);
else $time = showdate($post['time']).' - '.showtime($post['time']);
if(trim($post['url']) != 'http://')
$name = '<a href="'.$post['url'].'">'.rainbow($post['name']).'</a>'.partner($post['name']);
else $name = rainbow($post['name']).partner($post['name']);
echo'<div id="'.$post['id'].'"></div>'."\n";
echo'<tr>'."\n";
echo'<td class="icon"><img src="/post/icons/comment.png" alt="*"/></td>'."\n";
echo'<td class="title"><b><span style="text-shadow:black 0.05em 0.05em 0.05em">'.strtolower($name).'</span></b> ['.provider($post['ip']).']</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="menu" colspan="2">'."\n";
echo'<div style="text-align:right">'.$time.'</div>'."\n";
echo smiles(bbcode(sensor(htmlspecialchars(stripslashes($post['text']))))).'<br />'."\n";
echo'<div style="text-align:right">'.browser($post['ua']).' '.$post['phone'].'</div>'."\n";
if(isset($user))
echo'[<a href="javascript:warning(\'/post/comment/'.$blog['url'].'/delete/'.$post['id'].'.xhtml\')">D</a>] &#183; [<a href="/post/comment/'.$blog['url'].'/banned/'.$post['id'].'.xhtml">B</a>] &#183; [<a href="/post/comment/'.$blog['url'].'/edit/'.$post['id'].'.xhtml">E</a>] &#183; [<a href="/post/comment/'.$blog['url'].'/reply/'.$post['id'].'.xhtml">R</a>]';
echo'</td>'."\n";
echo'</tr>'."\n";
}
echo'<tr>'."\n";
echo'<td class="menu" colspan="2" style="text-align:center">['.$back.']||['.$next.']</td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
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
echo'<div class="menu"><a href="/post/" title="Post">Post</a> &raquo; <a href="/Post/category/'.$data['url'].'.xhtml" title="'.$data['name'].'">'.$data['name'].'</a></div>'."\n";
include'../inc/footer.php';
}
?>
