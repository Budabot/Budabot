<?php
$apf35guide_txt = "<header>::::: Guide to Apf Sector 35 :::::<end>\n\n";
$apf35guide_txt = "
<font color=#4169e1>Objective: ASSIST THE UNICORN FIELD ENGINEER</font>
The Unicorn Field Engineer is trying to repiar a downed Unicorn Transport Ship, but needs help defending the area. The Kyr'Ozch have left a large mine field at the beginning of the sector. If you trip a mine, you will be snared. Be wary, there is an ambush after the mine waiting to take advantage of your devided raid force. The mines can be disarmed if you have enough perception and bomb disarmment skills, as well as Bomb Disarmament Tools.

Clear the Kyr'Orch invaders from the sector by following the map below, then return to the ship. Talk to the Engineer, he will deploy three service towers. Defend the towers from waves of aliens until the Field Marshal Cha'Khaz and Field Support Cha'Khaz appear. Do not attack the towers or they will return fire, resulting in instant death for you! If the towers don't survive, the Field Marshal and Field Support will not appear, and the Field Engineer will insult you.

The following map should help you navigate Sector 35.

                  ______C
      Ship      _/|     \
             D_/  /     |
  H   _______/\  /      |
     /         \/       |
    |           \       |
    |            \______|  B
    |         E  |       \
    |            |        \
    |            |        |
    |            |        |
    |            |        |
     \___________|________| A
G                F         \
                            Start

From the start, move north through B to C; at C, turn west, then south-west toward E; after arriving to E, clear the short E-B arm, back to E and proceed south to F.

After F, clear the path to the east to A (start), then turn back west, run back to F and work your way toward G, after which turn north and clear the path all the way toward H and the ship. After this, clear a small remaning group of aliens to the east of the ship, between the ship and D point.
That's it, the sector is clear.

<font color=#ff0000>BOSSES: Field Marshal Cha'Khaz and Field Support Cha'Khaz</font>
Both of these generals come at the SAME TIME. Attack Field Support Cha'Khaz first. While Field Support Cha'Khaz is alive, Field Marshal Cha'Khaz will have a huge reflect shield and you will be hitting him for about 40-80 dmg. Once Field Support Cha'Khaz is deal, kill Field Marshal Cha'Khaz. These bosses spawn no adds iand do not wipe aggro.

<font color=#ff0000>BOSS LOOT:</font>

<a href='itemref://257961/257961/250'>Energy Redistribution Unit</a>
 Used to make:
  * <a href='itemref://257147/257147/300'>Blades of Boltar</a>
  * <a href='itemref://257123/257123/300'>Vektor ND Grand Wyrm</a>
  * <a href='itemref://257116/257116/1'>De'Valos Lava Protection Ring</a>

<a href='itemref://257964/257964/250'>Visible Light Remodulation Device</a>
 Used to make:
  * <a href='itemref://257117/257117/1'>De'Valos Radiation Protection Ring</a>
  * <a href='itemref://253236/253236/300'>Scoped Salabim Shotgun Supremo</a>
  * <a href='itemref://258343/258343/300'>Explosif's Polychromatic Pillows (Sense/Agility)</a>
  * <a href='itemref://258344/258344/300'>Explosif's Polychromatic Pillows (Strength/Stamina)</a>
  * <a href='itemref://258345/258345/300'>Explosif's Polychromatic Pillows (Intelligence/Psychic)</a>

<a href='itemref://257706/257706/1'>Kyr'Ozch Helmet</a>
 Give to Omni-Tek Observer for <a href='itemref://257112/257112/1'>Omni-Tek Award - Exemplar</a>
 Give to Clan Liaison for <a href='itemref://257113/257113/1'>Clan Merits - Paragon</a>

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

Thanks to Valvs for the Sector 35 Layout
http://forums.anarchy-online.com/showthread.php?t=435953
";

$apf35guide_txt = $this->makeLink("Guide to Apf Sector 35", $apf35guide_txt);
if($type == "msg")
$this->send($apf35guide_txt, $sender);
elseif($type == "all")
$this->send($apf35guide_txt);
else
$this->send($apf35guide_txt, "guild");
?>