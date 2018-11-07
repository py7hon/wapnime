<?php
$query = mysql_query('SELECT * FROM `news` WHERE `show` > '.time().' ORDER BY `id` DESC LIMIT 1');
while($news = mysql_fetch_array($query)){
echo'<div class="title"><img src="data/icons/rss.png" alt="&bull;"/> <b><a href="news/read/'.$news['id'].'.xhtml">'.$news['title'].'</a></b></div>'."\n";
echo'<div class="menu">'."\n";
echo'<div style="text-align:right"><span>'.showdate($news['time']).' - '.showtime($news['time']).'</span></div>'."\n";
echo'<div class="line"></div>'."\n";
if(strlen($news['text']) > 100)
echo htmlspecialchars(stripslashes(substr($news['text'], 0, 100))).'... <a href="news/read/'.$news['id'].'.xhtml">'.$lang['read_more'].'</a>';
else echo htmlspecialchars(stripslashes($news['text']));
echo'<br />'."\n";
echo'['.$lang['comments'].': '.mysql_result(mysql_query('SELECT COUNT(*) FROM `comment` WHERE `type` = "news" AND `id_type` = "'.$news['id'].'"'), 0).']<br />'."\n";
echo'</div>'."\n";
}
?>
