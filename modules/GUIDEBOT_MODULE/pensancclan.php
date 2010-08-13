<?php
$pensancclan_txt = "<header>::::: Penumbra Sanctuary Garden Key Quest CLAN :::::<end>\n\n"; 
$pensancclan_txt = "Penumbra Sanctuary Garden Key Quest CLAN

To start this quest, speak to <font color = yellow>The Forrester</font>. He is in the Penumbra Garden, in the middle of the maze to the north.

<font color = yellow>Part one: Calibrate stuff!</font>
The Forrester will give you a mission.  For the mission, you need a Calibration tuner set to the wrong offset.  The Calibration Tuner itself you have to buy from the Yuttos Tools Vendor in Ado, west of Adonis city.  It can be tricky to get there, so keep trying.  To set it to the wrong offset, use a screwdriver on it.  If you don't have one on you, you can buy one from the Yuttos Metal dealer who is also nearby. The mission has coords, so it's a smooth ride. Upload the coords to the map, enter the mission and find a large machine. Use your Tuner on the machine, and wait for a mission update. Run out, and repeat the process 7 times. Usually all the missions point to the same location, and the machine is almost always in the same room, so it's a piece of cake.


<font color = yellow>Part two: Kill Unredeemed leaders!</font>
This one is a bit trickier, you have to kill ten Unredeemed leaders. Some are pretty hard to reach because of the adds (no problem after lvl 203 (202?), adds turn grey around then), so you might want to bring backup.

The leaders:

<font color = yellow>1. Calan-Kur - Leader</font>
   <font color = green>Coords: ? - Outside Pen Unredeemed Temple</font>

<font color = yellow>2. Rawa-El - Marked</font>
   <font color = green>Outside Pen Unredeemed Temple</font>

<font color = yellow>3. Buran-Kuiri - Leader</font>
   <font color = green>Outside Pen Unredeemed Temple</font>

<font color = yellow>4. Chimera Cranii</font>
   <font color = green>Coords: 2600x884 (East)</font>

<font color = yellow>5. Malah-Ea - Leader</font>
   <font color = green>Coords: 1520x1120 (West)</font>

<font color = yellow>6. Baran-Kuir - Leader</font>
   <font color = green>Outside Pen Unredeemed Temple</font>

<font color = yellow>7. Eron-Cur - Leader</font>
   <font color = green>Outside Pen Unredeemed Temple</font>

<font color = yellow>8. Patriarch Prophet Nar Van</font>
   <font color = green>Inside Pen Unredeemed Temple</font>
Once the Prophet dies, Acolyte Cama Hume spawns to the right of the Dias.  Give him 6 Insignias of Vanya, and he will spawn Carlos

<font color = yellow>9. Omega Carlos Truhillo</font>
   <font color = green>Inside Pen Unredeemed Temple</font>
Once Carlos is dead, kill all the mobs that spawned with him.  Once they are dead, Vanya will spawn.

<font color = yellow>10. Unredeemed Vanya</font> 
   When Vanya dies, you get your key";

$pensancclan_txt = bot::makeLink("Penumbra: Sanctuary Garden Key Quest CLAN", $pensancclan_txt); 
if($type == "msg") 
bot::send($pensancclan_txt, $sender); 
elseif($type == "all") 
bot::send($pensancclan_txt); 
else 
bot::send($pensancclan_txt, "guild"); 
?>