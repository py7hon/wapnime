<?php
header('Content-type: text/html; charset=utf-8');
header('Expires: Mon, 26 Jul 2200 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
header('Pragma: must-revalidate');
header('Cache-control: private');

echo'<?xml version="1.0" encoding="utf-8"?>'."\n";
echo'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">'."\n";
echo'<head>'."\n";
echo'<meta name="keywords" content="'.$set['key'].'"/>'."\n";
echo'<meta name="description" content="'.$set['desc'].'"/>'."\n";
echo'<meta name="copyright" content="2018 (c) Iqbal Rifai"/>'."\n";
echo'<meta name="author" content="Iqbal Rifai"/>'."\n";
echo'<meta name="email" content="iqbl [at] waifu [dot] club"/>'."\n";
echo'<meta name="Charset" content="UTF-8"/>'."\n";
echo'<meta name="Distribution" content="Global"/>'."\n";
echo'<meta name="Rating" content="General"/>'."\n";
echo'<meta name="Robots" content="INDEX,FOLLOW"/>'."\n";
echo'<meta name="Revisit-after" content="1 Day"/>'."\n";
echo'<link rel="icon" type="image/x-icon" href="/favicon.ico"/>'."\n";
echo'<link rel="stylesheet" type="text/css" href="/data/themes/'.$set['themes'].'/style.css"/>'."\n";
if(eregi('^/blog/', $_SERVER['REQUEST_URI']))
echo'<link rel="alternate" type="application/rss+xml" title= "RSS-Blog" href="/blog/rss.xml"/>'."\n";
else echo'<link rel="alternate" type="application/rss+xml" title= "RSS-News" href="/news/rss.xml"/>'."\n";
echo'<title>'.$title.'</title>'."\n";
echo'</head>'."\n";
echo'<body>'."\n";
echo'<div class="menu">'."\n";
echo'<table class="post">'."\n";
echo'<tr>'."\n";
echo'<td valign="bottom"><img src="/logo.png" width="160" height="45" title="Wapnime" alt="logo"/></td>'."\n";
echo'<td align="right">';
if($local == 'id')
echo'EN <a href="/?lang=en"><img src="/data/lang/flags/en.gif" alt="*"/></a>';
else echo'ID <a href="/?lang=id"><img src="/data/lang/flags/id.gif" alt="*"/></a>';
echo'</td>'."\n";
echo'</tr>'."\n";
echo'</table>'."\n";
echo'</div>'."\n";
if(isset($user))
$visitor = $user['nick'];
elseif(isset($_SESSION['name']))
$visitor = $_SESSION['name'];
else $visitor = $lang['guest'];
echo'<div class="header" style="padding: 4px 2px 4px 2px">'."\n";
echo'<span><a href="/">Home</a></span>';
echo'<span><a href="/shoutbox/">Shout</a></span>';
echo'<span><a href="/news/">News</a></span>';
echo'</div>'."\n";
echo'<div class="main">';
echo showdate().' - '.showtime().'<br />';
echo say().' '.$visitor;
echo'</div>'."\n";
?>
