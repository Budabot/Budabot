<? 
$quests_txt = "<header>::::: NPC Quest Guides  :::::<end>\n\n"; 
$quests_txt = " NPC Quest Guide

<a href='chatcmd:///tell <myname> sided1>LINK</a> Sided Shoulderpads Pt 1
<a href='chatcmd:///tell <myname> sided2>LINK</a> Sided Shoulderpads Pt 2
<a href='chatcmd:///tell <myname> sided3>LINK</a> Sided Shoulderpads Pt 3
<a href='chatcmd:///tell <myname> atailor>LINK</a> A Tailor's Woe
<a href='chatcmd:///tell <myname> fgridone>LINK</a> Fixer Grid 1
<a href='chatcmd:///tell <myname> fgrid2>LINK</a> Fixer Grid 2 TeamFgrid
<a href='chatcmd:///tell <myname> jacksrings>LINK</a> Jacks Professionals Rings
";
$quests_txt = bot::makeLink("NPC Quest Guides", $quests_txt); 
if($type == "msg") 
bot::send($quests_txt, $sender); 
elseif($type == "all") 
bot::send($quests_txt); 
else 
bot::send($quests_txt, "guild"); 
?>