<?php
list($msec, $sec)=explode(chr(3), microtime());

echo'<div class="title" style="text-align:center">'."\n";
echo'<a href="/index.php">Home</a>';
echo' | <a href="/shoutbox">Shoutbox</a>';
echo' | <a href="/guestbook">Guestbook</a>';
echo' | <a href="/loads">Download</a>';
echo'</div>'."\n";
echo'<div class="menu" style="text-align:center">'."\n";
echo'Online: <a href="/online.php">'.online().'</a> | Today: '.day().' | Total: '.counter().' Hits<br />'."\n";
echo'Speed : '.round(($sec+$msec)-$conf['headtime'],3).' / second<br />'."\n";
echo'</div>'."\n";
echo'<div class="footer"><h3>Copyright &copy; '.date('Y').'<br />Powered by <a href="http://github.com/py7hon">Iqbal Rifai</a></h3></div>'."\n";
echo'</body>'."\n";
echo'</html>'."\n";
?>
