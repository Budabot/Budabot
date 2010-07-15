<?php
$adosanckey_txt = "<header>::::: Adonis: Dalja Sanctuary Garden Key Quest :::::<end>\n\n";
$adosanckey_txt .= "
Show your Dalja Garden Key to Visionist Bhotaar-Hes Dal in Garden. He tells you to hunt ghosts and find blueprint for a demon.

Hunt spirits/spirit hunters and complete the blueprint for 'Infernal Demon'. You can hunt ranked unredeemed for patterns too, if you don't mind losing some unredeemed faction.

Show the complete blueprint for Infernal Demon to Visionist (do not use novictum on it, this part requires that you only complete the pattern). You get quest update and Visionist will tell you to summon the demon and get some information from him.

After the quest is updated, you can then finish the process and make the pattern into a Novictalized Notum Crystal. Check our Pocket Boss Guide to learn how to complete the pattern.

Take the crystal to a 160+ incarnator and activate the crystal (there is a lvl 170 incarnator northeast and a lvl 190 incarnator northwest from Adonis City South).

Once he appears you have 6 minutes to talk to him, during that time he cannot be attacked. Talk with him and he will give you a Complete Blueprint Pattern of 'Diviner Gil Kalad-Thar'.

Make this pattern into a Novictalized Notum Crystal (same process as with the previous complete pattern). You already know where you can spawn the Diviner. When he spawns he cannot be attacked for 6 minutes (this is because the clan version requires for players to talk to him), once this time runs out kill him. Loot 'Record of Lost Diviner' from his corpse

Your get a mission update. Bring the item the Diviner dropped to Visionist Bhotaar-Hes Dal and he will reward you with the Sanctuary Key!
 ";

$adosanckey_txt = bot::makeLink("Adonis: Dalja Sanctuary Garden Key Quest", $adosanckey_txt);

bot::send($adosanckey_txt, $sendto);
 
?>