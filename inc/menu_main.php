<?php
$query = mysql_query('SELECT * FROM `menu` ORDER BY `position` ASC');
while($menu = mysql_fetch_array($query)){
if($menu['type'] == 'link'){
echo'<div class="menu">';
if($set['icon'] == 1)
echo'<img src="/data/icons/'.$menu['icon'].'" alt="&raquo;"/> ';
else echo'&raquo; ';
}else{
echo'<div class="title">';
if($set['icon'] == 1)
echo'<img src="/data/icons/'.$menu['icon'].'" alt="&bull;"/> ';
}
if($menu['type'] == 'link')
echo'<a href="'.$menu['url'].'" title="'.$menu['name'].'">';
else echo'<b>';
echo $menu['name'];
if($menu['type'] == 'link')
echo'</a>';
else echo'</b>';
if($menu['count'] != NULL && is_file(root.$menu['count'])){
echo' (';
@include root.$menu['count'];
echo')';
}
if($menu['type']=='link')
echo'</div>'."\n";
else echo'</div>'."\n";
}
?>
