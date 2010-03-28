<? 
$shoelsancclan_txt = "<header>::::: Shoel Sanctuary Garden Key Quest CLAN :::::<end>\n\n"; 
$shoelsancclan_txt = "Shoel Garden Key Quest CLAN

<font color='#ff9933'><highlight>Ocra Sanctuary Garden Key Quest</font></end>

<highlight>Guide suited for:</end> All Classes
<highlight>Faction:</end> Clan
<highlight>Level Range:</end> 50-100

Talk to Sipius Ocra Nuir-Cama in the garden, he asks you to go spawn Min-Ji again and show her the cloth you looted from Completing the Garden Key Quest, to help her remember who she was. 

Spawn Min-Ji again with a new pattern and show her the cloth, she talks about being trapped there by Eric Miller. 

Return to Sipius Ocra Nuir-Cama in the garden, tell him about Eric Miller and offer to help the Empath. He will give you the task of eliminating him. 

Note: Get 6 Insignas of Roch to continue the quest. 

Next head to the Unredeemed Temple, go to the second room, right wing and enter that room. First kill Patriarch Prophet Dom Dal, Acolyte Ocra Duna will spawn and you need to chat and trade him 6 Insignas of Roch. Omega Eric Miller will then appear. Kill him and get the loot the Defiled Pattern of Min-Ji Liu from his corpse. 

Go to Ecclesiast Ocra Lux. Talk to him and give him the defiled pattern. He will send you to locate an ancient device. This can be located just outside Ergo's cave in Scheol (directly south of the cave entrance). Target the device and wait for quest update. 

Go back to Ecclesiast Ocra Lux and tell him you found the device. He will ask you for the defiled pattern, so give that to him. He will then summon Redeemed Ocra so you can talk to him and give him the pattern, and he will bless it. 

Now head to the nearest Incarnator and spawn Weary Empath Min-Ju Liu yet again and give her the blessed pattern. She will despawn and you end up with a mysterious container called Essence of Patience. 

Go back to Ecclesiast Ocra Lux and give him the Essence of Patience. You wil be rewarded with The Key to Ocra's Sanctuary! 

--------------------------------------------------------------------------------
Last updated on 10.08.2006 by Trgeorge
Information originally provided by Friregan to the SL Library Forums. Additional information provided by Windguaerd
Courtesy of AO Universe
"
;
$shoelsancclan_txt = bot::makeLink("Shoel: Sanctuary Garden Key Quest CLAN", $shoelsancclan_txt); 
if($type == "msg") 
bot::send($shoelsancclan_txt, $sender); 
elseif($type == "all") 
bot::send($shoelsancclan_txt); 
else 
bot::send($shoelsancclan_txt, "guild"); 
?>