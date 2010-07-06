<?php
$apf28guide_txt = "<header>::::: Guide to Apf Sector 35 :::::<end>\n\n";
$apf28guide_txt = "
<font color=#4169e1>Objective: PROTECT THE ROOKIE ALIEN HUNTER</font>
In this sector, you follow the NPC the Rookie Alien Hunter. Follow him through a series of canyons loaded with groups of aliens. There are also several ambushes along the way. THE ROOKIE MUST STAY ALIVE UNTIL THE END! If the rookie dies, you must go back and get a new one and follow him through his programmed path. There are several laser fences blocking your way. Do not run into these, as they will instantly kill you. When you come to these, stay behind the rookie and he will disable the fence so you can pass.

<font color=#ff0000>BOSS: Embalmer Cha'Khaz</font>
This boss spawns in the middle of the last section of canyon as long as the rookie is still alive (after this it doesn't matter if the rookie dies). Let a single character (the tank) attack the embalmer to build hate, and NO ONE ELSE. The boss will spawn several adds: Support Sentries, Alien Pods, and regular aliens. Kill these immediately, especially the support sentries as they can cast protection/heal nanos on boss. The pods are a real pain too as they capture someone inside them, rendering that person unable to do anything until the pod is killed. Having everyone assist a caller on the adds is reccomended. Once the tank has sufficient aggro (1-1.5 bars of health), everyone should attack the embalmer. Keep in mind that adds spawn throughout the fight so everyone should re-assist the caller frequently to make sure the adds stay to a minimum. It seems that when the embalmer is almost dead he starts to wipe aggro and attack random people--be prepared for this!

<font color=#ff0000>BOSS LOOT:</font>

<a href='itemref://257959/257959/250'>Inertial Adjustment Processing Unit</a>
 Used to make:
  * <a href='itemref://257119/257119/300'>Hadrulf's Viral Belt Component Platform</a>
  * <a href='itemref://257109/257109/300'>Low Recoil Diamondine Kick Pistol</a>
  * <a href='itemref://257111/257111/300'>Lightened Murder Maul</a>

<a href='itemref://257963/257963/250'>Notum Amplification Coil</a>
 Used to make:
  * <a href='itemref://257124/257124/300'>Twice Augmented Hellspinner Shock Cannon</a>
  * <a href='itemref://257118/257118/250'>ObiTom's Nano Calculator</a>
  * <a href='itemref://257144/257144/300'>Amplified Kyr'Ozch Carbine - Type 5</a>
  * <a href='itemref://257143/257143/300'>Amplified Kyr'Ozch Carbine - Type 12</a>
  * <a href='itemref://257142/257142/300'>Amplified Kyr'Ozch Carbine - Type 13</a>

<a href='itemref://257706/257706/1'>Kyr'Ozch Helmet</a>
  * Give to Omni-Tek Observer for <a href='itemref://257112/257112/1'>Omni-Tek Award - Exemplar</a>
  * Give to Clan Liaison for <a href='itemref://257113/257113/1'>Clan Merits - Paragon</a>

Give the following items to Omni-Tek Observer or Clan Liaison for 10 tokens or 4 million credits.
  * <a href='itemref://257533/257533/1'>Kyr'Ozch Video Processing Unit</a>
  * <a href='itemref://257529/257529/1'>Kyr'Ozch Battlesuit Audio Processor</a>
  * <a href='itemref://257531/257531/1'>Kyr'Ozch Rank Identification</a>
  * <a href='itemref://257530/257530/1'>Kyr'Ozch Battlesuit Water Processing Unit</a>
  * <a href='itemref://257044/257044/1'>Kyr'Ozch Battlesuit Gauntlet</a>

Combine <a href='itemref://257968/257968/1'>Hacker ICE-Breaker Source</a> with a <a href='itemref://161699/161699/1'>Nano Programming Interface</a> (1,000 Nano Programming skill required) to make an <a href='itemref://257110/257110/1'>Intrusion Countermeasure Electronics Upgrade</a> (100% CRU).

Kyr'Ozch Data Cores can be traded to Unicorn Administrator for an Encrypted Kyr'Ozch Key. You need one Data Core from Sectors 13, 28, and 35 to make the key needed to access Sector 42.


Thanks to Turk021 for his APF Guides
http://forums.anarchy-online.com/showthread.php?t=392937


";

$apf28guide_txt = $this->makeLink("Guide to Apf Sector 28", $apf28guide_txt);
if($type == "msg")
$this->send($apf28guide_txt, $sender);
elseif($type == "all")
$this->send($apf28guide_txt);
else
$this->send($apf28guide_txt, "guild");
?>