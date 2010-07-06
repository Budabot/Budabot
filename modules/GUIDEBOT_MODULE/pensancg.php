<? 
$pensancg_txt = "<header>::::: Penumbra Sanctuary Garden Key Quest OMNI :::::<end>\n\n"; 
$pensancg_txt = "Penumbra Sanctuary Garden Key Quest OMNI

To start this quest, speak to <font color = yellow>Garboil Roch Van</font>. He is in the Penumbra Garden, in the middle of the maze to the north.

<font color = yellow>Part one: Calibrate stuff!</font>
The Garboil will give you a mission, and a Calibration Tuner. The mission has coords, so it's a smooth ride. Upload the coords to the map, enter the mission and find a large machine. Use your Tuner on the machine, and wait for a mission update. Run out, and repeat the process 7 times. Usually all the missions point to the same location, and the machine is almost always in the same room, so it's a piece of cake.


<font color = yellow>Part two: Kill redeemed leaders!</font>
This one is a bit trickier, you have to kill twelwe redeemed leaders. Some are pretty hard to reach because of the adds (no problem after lvl 203 (202?), adds turn grey around then), so you might want to bring backup.

The leaders:

<font color = yellow>1. Hiisian Steed - Tainted</font>
   <font color = green>Coords: 790x1290 (West)</font>

<font color = yellow>2. Watcher Cama Mara-Thar - Leader</font>
   <font color = green>Coords: 1045x180 (West) - SW redeemed village, lots of adds.</font>

<font color = yellow>3. Chimera Cranii - Marked</font>
   <font color = green>(East) - Northeast of the Penumbra Fortress Statue.</font>

<font color = yellow>4. Devoted Gil Wei-Wei</font>
   <font color = green>(Adonis) - Upstairs in the redeemed temple.</font>

<font color = yellow>5. Devoted Cama Zean-Mara</font>
   <font color = green>(West) The redeemed village in the cave to the north-west (where the corpse-sucker was, only further in). Hard to reach, tons of adds.</font>

<font color = yellow>6. Watcher Cama Gil Shan</font>
   <font color = green>(West) Northwest redeemed temple, behind the stairs in the second room.</font>

<font color = yellow>7. Len Wu</font>
   <font color = green>(West) Southwest redeemed village, tons of them there, pick one </font>

<font color = yellow>8. Sipius Cama Ilad-Lux</font>
   <font color = green>(West) Southwest redeemed village</font>

<font color = yellow>9. Watcher Cama Hume-Ulma</font>
   <font color = green>(West) Northwest redeemed temple, on the top of the stairs.</font>

<font color = yellow>10-12. Venerable Ecclesiast, Empath Alyssa, redeemed Cama.</font>
   (West) Northwest redeemed temple. Kill the Ecclesiast, give the shady guy 6 Cama Insignias. He will spawn the Empath. Kill the Empath, redeemed Cama will spawn. Kill Cama, and you have the key.
";

$pensancg_txt = $this->makeLink("Penumbra: Sanctuary Garden Key Quest OMNI", $pensancg_txt); 
if($type == "msg") 
$this->send($pensancg_txt, $sender); 
elseif($type == "all") 
$this->send($pensancg_txt); 
else 
$this->send($pensancg_txt, "guild"); 
?>