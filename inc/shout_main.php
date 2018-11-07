<?php
echo'<form action="shoutbox/" method="post">'."\n";
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
echo $lang['gender'].' :<br />';
echo'<select name="icon" style="width:99%"><option value="">'.$lang['select_icon'].'</option><option value="1" '.($_SESSION['icon'] == 1 ? 'selected="selected"' : null).'>'.$lang['male'].'</option><option value="2" '.($_SESSION['icon'] == 2 ? 'selected="selected"' : null).'>'.$lang['female'].'</option></select>';
echo'</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="content" colspan="2"><input type="submit" value="Shout"/> <a href="/smiles.php">Smiles</a> <a href="/bbcode.php">BB-Code</a></td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
echo'</div>'."\n";
echo'</form>'."\n";
$k_post = mysql_result(mysql_query('SELECT COUNT(*) FROM `shout`'), 0);
$k_page = k_page($k_post, 5);
$page = page($k_page);
$start = 5*$page-5;
if($page > 1)
$back = '<a href="?page='.($page-1).'">&laquo;</a>';
else $back = '&laquo;';
if($page < $k_page)
$next = '<a href="?page='.($page+1).'">&raquo;</a>';
else $next = '&raquo;';
if($k_post == 0){
echo'<div class="status">'.$lang['no_msg'].'</div>'."\n";
}else{
$query = mysql_query('SELECT * FROM `shout` ORDER BY `id` DESC LIMIT '.$start.', 5');
echo'<table class="post">'."\n";
while($shout = mysql_fetch_array($query)){
if($set['time']==1)
$time = countdown($shout['time']);
else $time = showdate($shout['time']).' - '.showtime($shout['time']);
if(trim($shout['url']) != 'http://')
$name = '<a href="'.$shout['url'].'">'.rainbow($shout['name']).'</a>'.partner($shout['name']);
else $name = rainbow($shout['name']).partner($shout['name']);
echo'<tr>'."\n";
echo'<td class="icon">'.icon_shout($shout['icon']).'</td>'."\n";
echo'<td class="title"><b><span style="text-shadow:black 0.05em 0.05em 0.05em">'.strtolower($name).'</span></b> ['.provider($shout['ip']).']</td>'."\n";
echo'</tr>'."\n";
echo'<tr>'."\n";
echo'<td class="menu" colspan="2">'."\n";
echo'<div style="text-align:right">'.$time.'</div>'."\n";
echo smiles(bbcode(sensor(htmlspecialchars(stripslashes($shout['text']))))).'<br />'."\n";
echo'<div style="text-align:right">'.browser($shout['ua']).' '.$shout['phone'].'</div>'."\n";
echo'</td>'."\n";
echo'</tr>'."\n";
}
echo'<tr>'."\n";
echo'<td class="menu" colspan="2" style="text-align:center">['.$back.']||['.$next.']</td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
}
?>
