<?php
$meleesmith_txt = "<header>::::: Guide to Melee Smith :::::<end>\n\n"; 
$meleesmith_txt = "Contrary to common belief, the store is open to anyone wanting to drop by, provided they can actually get there. Its easiest for Omni personell no doubt though since the Whompa to Southern Artery Valleys Outpost 10-3 is just closeby. Clans might want to go through Wine in order to get here safely, although it is possible to whompa from the 2HO outpost as well, it depends on how much of a beating from the unsympathetic Omni Guards you think you can handle.

The Tsunayoshi Smith really chose an outback location to provide his services. Located in Southern Artery Valley at the Largest Soul Fragment in the world at 2600 x 2900, the Tsunayoshi Smith has put up his tent and decided to clean out place to make room for himself. His smithing skills has been long awaited and now that he has finally arrived, we set out to meet the master weaponsmith in person and see what he might have in store for us.

<font color= yellow>Meibutsu Daito</font>
The Meibutsu Daito weapons are all based on 1Handed Edged skills, and are one of the finest weapons to be created. Much like the Fire Executioners, these exist in a variety of versions, depending on how many special attacks you are willing to submit your enemies and IP allocation to. Daitos also give a small bonus to Evade ClsC.

<font color= yellow>Meibutsu Katana and Dai-Katana</font>
Another 2Handed Edged weapon. This one comes in three different versions, depending on which kind of special attacks you are looking for. As with the Dai-Katana, is is an Enforcer-only weapon that adds a bit to your Body Dev and depending on which version you get, Riposte, Evade ClsC or Concealment. Dai-Katanas adds to Body dev and Parry.

<font color= yellow>Shining Black Staves</font>
The Black staves are actually designed by a certain Bob Bazemion (now deceased) and by lucky chance found their way to the Tsunayoshi Smith. They sport an impressing array of special attacks and are 2Handed Blunt, Enforcer-only weapons. They add a small bit to Sense, Intelligence and Radiation AC.

<font color= yellow>Sleek Black Staves</font>
As with the Shining Black staves, the sleek ones are also designed by Bob Bazemion. These however, although 2Handed Blunt weapons, are not Enforcer-only. A slight difference in special attacks is what separates them from the Shining versions, and also the Sleek variant adds to Agility and Radiation AC.

<font color= yellow>Schiacciamento Clubs</font>
Like with the case of the Daitos, the Schiacciamento Clubs come in various shapes a brandishing different special attacks. The clubs are based on 1Handed Blunt and very suitable for dualwielding. They also
add a small amount to various base abilities and Melee Init.

To our disappointment we have to report that the Tsunayoshi Smith doesnt sell weapons any higher than QL135 - however - the weaponry available here are quite devastating and perfect at lower levels. And of course, who can resist the feel of the Schiacciamento Club in their hands, like in a bad Teenage Mutant Ninja Leet movie and swing that bat wildly?

The shop is really placed in the middle of nowhere and its use for the people not used to traverse these parts questionable. However, it is a really nice place to pick up weapons for your lowerlevel friends.
 ";

$meleesmith_txt = bot::makeLink("Guide to Melee Smith", $meleesmith_txt); 
if($type == "msg") 
bot::send($meleesmith_txt, $sender); 
elseif($type == "all") 
bot::send($meleesmith_txt); 
else 
bot::send($meleesmith_txt, "guild"); 
?>