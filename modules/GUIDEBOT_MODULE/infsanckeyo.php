<?php
$infsanckeyo_txt = "<header>::::: Inferno Sanctuary Key Quest - Omni :::::<end>\n\n"; 
$infsanckeyo_txt = "Inferno Sanctuary Key Quest - Omni
 
 </font><font color='#ffff00'>1) </font><font color='#00ff00'>You should get sanctuary key quest when you finish garden key quest. If you don't, talk to <font color='#ff9900'>Garboil Hes Mord</font> in Garden. He'll give you the quest.
 
 </font><font color='#ffff00'>2) </font><font color='#00ff00'>The sanctuary key quest is some assassination like Vanya Sanctuary Key quest.
 Target 1: </font><font color='#ff9900'>Kuir-Afat, The Custodian</font><font color='#00ff00'>
 Target 2: </font><font color='#ff9900'>Acolyte Gal Bela</font><font color='#00ff00'>
 Target 3: </font><font color='#ff9900'>Sipius Gal Jha-Hume</font><font color='#00ff00'>
 Target 4: </font><font color='#ff9900'>Kuir-Afat, The Custodian</font><font color='#00ff00'>
 Target 5: </font><font color='#ff9900'>Suir-Afat, The Custodian</font><font color='#00ff00'>
 Target 6: </font><font color='#ff9900'>Suir-Shelet, The Custodian</font><font color='#00ff00'>
 Target 7: </font><font color='#ff9900'>Venerable Ecclesiast Gal Bala</font><font color='#00ff00'>
 Target 8: </font><font color='#ff9900'>Empath Sir Gawain</font><font color='#00ff00'>
 Target 9: </font><font color='#ff9900'>Redeemed Lord Galahad</font><font color='#00ff00'>
 
 </font><font color='#ffff00'>3) </font><font color='#00ff00'>Target 1 - <font color='#ff9900'>Name: Kuir-Afat, The Custodian 
Location: Inferno Frontier, 2504 x 2569 
Level: 205 </font> Redeemed village at Inferno Frontier.
 
 </font><font color='#ffff00'>4) </font><font color='#00ff00'>Target 2 - <font color='#ff9900'>Acolyte Gal Bela</font>
Location: Between the Portal and the Yutto Camp near portal to Penumbra
Level: 200 
Note: This guy can really wander! 

 </font><font color='#ffff00'>5) </font><font color='#00ff00'>Target 3 - <font color='#ff9900'>Sipius Gal Jha-Hume</font>
 Location: Inferno Frontier, 2462 x 2628 Level: 200 Note: There are 2 mobs of this name. The second is a little further north at 2466 x 2644
 
 </font><font color='#ffff00'>6) </font><font color='#00ff00'>Target 4 - <font color='#ff9900'>Name: Suir-Katan, The Custodian 
Location: Inferno Frontier, 2501 x 2526 Level: 210 </font>
 
 </font><font color='#ffff00'>7) </font><font color='#00ff00'>Target 5-6 - <font color='#ff9900'>Suir-Afat, The Custodian</font>, <font color='#ff9900'>Suir-Shelet, The Custodian</font>
 At Sorrow. It might be hard to pull Suir-Afat, The Custodian 1168 x 1440 since he's at the middle of the village. Suir-Shelet, The Custodian is at south and easier to pull.1218 x 1384 
 
 </font><font color='#ffff00'>8) </font><font color='#00ff00'>Target 7-9 - <font color='#ff9900'>Venerable Ecclesiast Gal Bala</font>, <font color='#ff9900'>Empath Sir Gawain</font>, <font color='#ff9900'>Redeemed Lord Galahad</font>
Redeemed temple in Inferno.</font><font color='#00ff00'> When you kill </font><font color='#00ff00'><font color='#ff9900'>Redeemed Lord Galahad</font></font><font color='#00ff00'> and get loot, You get <a href='itemref://229063/229063/1'>Lord Mordeth Sanctuary Key</a> as reward.
 
</font><font color='#ff0000'>NOTE: Step 9 has <a href='itemref://239759/239760/220'>Box of Nanocrystals</a> as item reward. If you already have one, use it or transfer to someone befor finishing step 9, or you'll miss it.</font><a href='itemref://229063/229063/1'>Lord Mordeth Sanctuary Key</a> is your final quest reward</a></font>
 ";

$infsanckeyo_txt = bot::makeLink("Inferno Sanctuary Garden Key - Omni", $infsanckeyo_txt); 
if($type == "msg") 
bot::send($infsanckeyo_txt, $sender); 
elseif($type == "all") 
bot::send($infsanckeyo_txt); 
else 
bot::send($infsanckeyo_txt, "guild"); 
?>