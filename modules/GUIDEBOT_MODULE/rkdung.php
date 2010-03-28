<? 
$rkdung_txt = "<header>::::: Guide To Rubi-Ka Static Dungeons  :::::<end>\n\n"; 
$rkdung_txt = "<font color = red> Guide To Rubi-Ka Static Dungeons </font> 

<a href='chatcmd:///tell <myname> <symbol>stepsm'><font color = yellow>Steps of Madness</font> - A dungeon introduced on Halloween, for teams lvl 30-50</a>
<a href='chatcmd:///tell <myname> <symbol>totw'><font color = yellow>Temple of the Three Winds</font> For level 30-60</a>
<a href='chatcmd:///tell <myname> <symbol>biomare'><font color = yellow>Biomare (aka Foremans)</font> For Levels 60-100</a>
<a href='chatcmd:///tell <myname> <symbol>cryptinfo'><font color = yellow>Crypt Of Home aka COH</font> For Levels 80+</a>
<a href='chatcmd:///tell <myname> <symbol>InnerS'><font color = yellow>Inner Sanctum</font>  - A Continuation of TOTW for Levels 125+</a>
<a href='chatcmd:///tell <myname> <symbol>smugden'><font color = yellow>Smuggler's Den</font>  - for Levels 125+</a>
<a href='chatcmd:///tell <myname> <symbol>hollow'><font color = yellow>Hollow Island</font> - For Level 190+ teams</a>";

$rkdung_txt = bot::makeLink("Guide To Rubi-Ka Static Dungeons", $rkdung_txt); 
if($type == "msg") 
bot::send($rkdung_txt, $sender); 
elseif($type == "all") 
bot::send($rkdung_txt); 
else 
bot::send($rkdung_txt, "guild"); 
?>