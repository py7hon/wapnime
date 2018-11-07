<?php
// Wapnime
// PHP Anime Website App
// Versions: 1.1
// Languange: PHP MySQL
// (c) Iqbal Rifai all rights reserved
// Please do not remove this


include'inc/root.php';
include'inc/session.php';
include'inc/lang.php';
include'inc/connect.php';
include'inc/fnc.php';
include'inc/ipua.php';
include'inc/system.php';
include'inc/counter.php';

switch($_GET['err']){
case'404':
$title = $lang['title_404'];
include'inc/header.php';
echo'<div class="err">'.$lang['404'].'</div>'."\n";
break;
case'403':
$title = $lang['title_403'];
include'inc/header.php';
echo'<div class="err">'.$lang['403'].'</div>'."\n";
break;
default:
$title = $set['title'];
include'inc/header.php';
if(isset($_SESSION['err'])){
echo $_SESSION['err'];
unset($_SESSION['err']);
}elseif(isset($_SESSION['msg'])){
echo $_SESSION['msg'];
unset($_SESSION['msg']);
}
include'inc/news_main.php';
include'inc/shout_main.php';
if(isset($user)){
echo'<div class="menu">';
if($set['icon'] == 1)
echo'<img src="icons/setting.png" alt="&raquo;"/> ';
else echo'&raquo; ';
echo'<a href="panel">Panel</a> (<a href="exit.php">X</a>)';
echo'</div>'."\n";
}
}
include'inc/menu_main.php';
include'inc/footer.php';
?>
