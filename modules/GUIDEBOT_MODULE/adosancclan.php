<?php
$adosancclan_txt = "<header>::::: Adonis Sanctuary Garden Key Quest CLAN :::::<end>\n\n";
$adosancclan_txt .= "
Gilthar Sanctuary Garden Key Quest

<highlight>Guide suited for:<end> <font color='#FF9933'>All Classes</font>
<highlight>Faction:<end> <font color='#FF9933'>Clan</font>
<highlight>Level Range:<end> <font color='#FF9933'>150-200</font>

Talk to <font color='#FF3333'>Diviner Gil Yeol-Ulma</font> in the garden, he is located in the garden at <font color='#FF3333'>478 x 270</font>.

Show him your garden key and he will ask you to find a blueprint of a Demon, the Pattern you need to find is for the <font color='#FF3333'>Infernal Demon.

There are two mobs which drop these patterns,
<font color='#FF3333'>Visionist Lum-Bhotaar Dal (1475 850)</font> and <font color='#HEXCODE'>Follower Man-Hes Dal (1310 810)</font>
both in the <font color='#FF3333'>Adonis City</font> area.

Once you have a all 4 pieces (aban-bothar-chi-dom) of the pattern, combine them to make a complete pattern (go not use novictum on it, this part requires that you only complete the patern) then head back and show it the Diviner.
Once he has seen this you will now be asked to retrieve information from a Diviner.

After the quest is updated, you can then finish the process and make the pattern into a Novictalized Notum Crystal.

Take the crystal to a 160+ incarnator and activate the crystal (there is a lvl 170 incarnator northeast and a lvl 190 incarnator northwest from Adonis City South).

When the demon spawns he cannot be attacked for 6 minutes (this is because the omni version requires for players to talk to him), once this time runs out kill him and loot the blueprint of the Diviner Gil Kald-Thar pattern from it's corpse.

Make this pattern into a Novictalized Notum Crystal (same process as with the previous complete pattern). You already know where you can spawn the Diviner. Once he appears you have 6 minutes to talk to him, during that time he cannot be attacked.

After some talking you will get <font color='#CCFF99'>'Record of Lost Diviner'</font>, bring it back to <font color='#FF3333'>Diviner Gil Yeol-Ulma</font> and you will be rewarded with your sanctuary key!


--------------------------------------------------------------------------------
Last updated on 10.08.2006 by Trgeorge
Information originally provided by tcollings to the SL Library Forums. Additional information provided by Windguaerd
Guide Courtesy of AO Universe
"
;
$adosancclan_txt = bot::makeLink("Adonis: Sanctuary Garden Key Quest CLAN", $adosancclan_txt);

bot::send($adosancclan_txt, $sendto);

?>