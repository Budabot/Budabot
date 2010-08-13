<?php
$elysancclan_txt = "<header>::::: Elysium Sanctuary Garden Key Quest CLAN :::::<end>\n\n"; 
$elysancclan_txt = "
<font color='#ff9933'><highlight>Enel Sanctuary Garden Key Quest</font></end>

<highlight>Guide suited for:</end> All Classes
<highlight>Faction:</end> Clan
<highlight>Level Range:</end> 50-100

To do the Elysium Sanctuary quest all you have to do is go back to Ecclesiast Enel Gil where you started the garden quest and give him your garden key and he will give you the mission. 

Basically you're going to kill the same mobs you just tagged for the garden key quest in the same order you tagged them. 
The last one will have a briefing on him that you will return to Enel Gil and receive your Sanctuary key. 

1588 721 : Remnons 
Fortuitous Jorr-Fes Shere 
Hypnagogic Wox-Xum Shere 
Follower Chi-Nar Shere 

737 566 : Stormshelter 
Fortuitous Hes-Man Shere 
Hypnagogic Ixu-Bhotaar Shere 
Follower Yutt-Ixi Share 

899 418 : Nero 
Fortuitous Pi-Zul Shere 
Follower Man-Wox Shere 

--------------------------------------------------------------------------------
Last updated on 12.03.2006 by Windkeeper
Courtesy of AO Universe
"
;
$elysancclan_txt = bot::makeLink("Elysium: Sanctuary Garden Key Quest Clan", $elysancclan_txt); 
if($type == "msg") 
bot::send($elysancclan_txt, $sender); 
elseif($type == "all") 
bot::send($elysancclan_txt); 
else 
bot::send($elysancclan_txt, "guild"); 
?>