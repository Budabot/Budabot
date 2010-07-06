<? 
$martialartists_txt = "<header>::::: Guide to Martial Artists :::::<end>\n\n"; 
$martialartists_txt = "Please see the following website for an excellent, yet somewhat outdated, Martial Artist Guide

'http://users.adelphia.net/~chronita/index.htm'

This guide is quite excellent in giving you information all about the MA class and how to play them
 ";

$martialartists_txt = $this->makeLink("Guide to Martialartists", $martialartists_txt); 
if($type == "msg") 
$this->send($martialartists_txt, $sender); 
elseif($type == "all") 
$this->send($martialartists_txt); 
else 
$this->send($martialartists_txt, "guild"); 
?>