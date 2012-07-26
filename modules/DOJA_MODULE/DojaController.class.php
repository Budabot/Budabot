<?php

/**
 * @Instance
 *
 * Author: Mackten (RK1)
 * Special thanks to Marebone (RK2)
 * Doja Chip Module
 * Developed for: Budabot(http://budabot.com/)
 */
class DojaController {

	/** @Inject */
	public $text;

	/**
	 * @Command("doja")
	 * @AccessLevel("all")
	 * @Description("Shows info of DOJA chips")
	 * @Matches("/^doja$/i")
	 * @Help("doja.txt")
	 */
	public function dojaCommand($message, $channel, $sender, $sendto, $arr) {
		$msg  = "<highlight>Below you can read about where to loot DOJA Chips. This information will show you the level requirements and the mobs that will drop the DOJA Chips for each level range. There are two types of Doja Chips, normal and special. You can turn one normal and one special chip in each day. Clicking the monster links below will set a waypoint to help you find your way.\n\n\n";
		$msg .= "<center><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
		$msg .= "<orange>Normal DOJA Chips<end>\n";
		$msg .= "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER></center>\n\n";
		$msg .= "<a href='itemref://284954/284954/1'>Nascense</a>\n";
		$msg .= "<a href='itemref://284954/284954/1'><img src=rdb://293716></a>\n";
		$msg .= "Level Range: <white>1-60<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 585.2 1621.9 4310'>Cripplers of Growth</a>, <a href='chatcmd:///waypoint 817.9 1737.8 4310'>Hiathlins</a>, <a href='chatcmd:///waypoint 1763 917.6 4310'>Malah-Anas</a>, <a href='chatcmd:///waypoint 783.3 1930.4 4310'>Predator Strikers</a> and <a href='chatcmd:///waypoint 1564.8 1534.9 4312'>Spinetooth Hatchlings</a><end>\n\n";	
		$msg .= "<a href='itemref://284955/284955/1'>Elysium</a>\n";
		$msg .= "<a href='itemref://284955/284955/1'><img src=rdb://293715></a>\n";
		$msg .= "Level Range: <white>61-100<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 548.5 1708.8 4541'>Bony Spinetooths</a>, <a href='chatcmd:///waypoint 639.2 656.1 4540'>Callous Mortiigs</a>, <a href='chatcmd:///waypoint 952.0 1842.8 4542'>Heartland Predators</a> and <a href='chatcmd:///waypoint 682.1 917.0 4542'>Shunpike Dryads</a><end>\n\n";
		$msg .= "<a href='itemref://284956/284956/1'>Scheol</a>\n";
		$msg .= "<a href='itemref://284956/284956/1'><img src=rdb://293714></a>\n";
		$msg .= "Level Range: <white>101-130<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 1247.2 601.5 4880'>Arcorashs</a>, <a href='chatcmd:///waypoint 1449.0 1257.4 4881'>Esoteric Hiathlins</a>, <a href='chatcmd:///waypoint 1129.3 192.5 4880'>Glurashs</a> and <a href='chatcmd:///waypoint 1192.2 915.9 4880'>Rock Rafters</a><end>\n\n";
		$msg .= "<a href='itemref://284957/284957/1'>Adonis</a>\n";
		$msg .= "<a href='itemref://284957/284957/1'><img src=rdb://293713></a>\n";
		$msg .= "Level Range: <white>131-160<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 2061.5 635.9 4872'>Creepos</a>, <a href='chatcmd:///waypoint 1976.9 631.4 4872'>Somphos</a> and <a href='chatcmd:///waypoint 1919.9 284.1 4873'>Stingers</a><end>\n\n";
		$msg .= "<a href='itemref://284958/284958/1'>Penumbra</a>\n";
		$msg .= "<a href='itemref://284958/284958/1'><img src=rdb://293712></a>\n";
		$msg .= "Level Range: <white>161-204<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 2125.7 873.4 4321'>Icy Predators</a>, <a href='chatcmd:///waypoint 2307.2 1767.8, 4321'>Vortexoids</a>, <a href='chatcmd:///waypoint 1544.5 1750.4 4320'>Demons of Water</a> and <a href='chatcmd:///waypoint 2454.7 1718.3 4321'>Frosty Rafters</a><end>\n\n";
		$msg .= "<a href='itemref://284959/284959/1'>Inferno</a>\n";
		$msg .= "<a href='itemref://284959/284959/1'><img src=rdb://293711></a>\n";
		$msg .= "Level Range: <white>205-220<end>\n";
		$msg .= "Looted from: <white><a href='chatcmd:///waypoint 1671.8 1090.6 4005'>Demons of Shadow</a>, <a href='chatcmd:///waypoint 1380.7 611.8 4005'>Fiery Chimeras</a> and <a href='chatcmd:///waypoint 1680.8 869.9 4005'>Somphos Logees</a><end>\n\n\n";
		$msg .= "<center><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
		$msg .= "<orange>Special DOJA Chips<end>\n";
		$msg .= "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n\n";
		$msg .= "Anything in these zones can drop a chip except maybe adds, special mobs and bosses.\n";
		$msg .= "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER></center>\n\n";	
		$msg .= "<a href='itemref://282144/282144/1'>Dark Ruins(Team)</a>\n";
		$msg .= "<a href='itemref://282144/282144/1'><img src=rdb://293710></a>\n";
		$msg .= "Level Range: <white>Below 201 (instance is level 150-200)<end>\n\n";	
		$msg .= "<a href='itemref://284961/284961/1'>Alappaa</a>\n";
		$msg .= "<a href='itemref://284961/284961/1'><img src=rdb://293802></a>\n";
		$msg .= "Level Range: <white>201-210<end>\n\n";	
		$msg .= "<a href='itemref://284962/284962/1'>Albtraum</a>\n";
		$msg .= "<a href='itemref://284962/284962/1'><img src=rdb://293801></a>\n";
		$msg .= "Level Range: <white>211-219<end>\n\n";	
		$msg .= "<a href='itemref://284960/284960/1'>Pandemonium</a>\n";
		$msg .= "<a href='itemref://284960/284960/1'><img src=rdb://293725></a>\n";
		$msg .= "Level Range: <white>220<end>\n\n";
		
		$msg = $this->text->make_blob("Doja Chips", $msg);
		$sendto->reply($msg);
	}

	/**
	 * @Subcommand("doja ([0-9]+)")
	 * @AccessLevel("all")
	 * @Description("Shows which DOJA chip you need at your level")
	 * @Help("doja.txt")
	 */
	public function dojaLevelCommand($message, $channel, $sender, $sendto, $arr) {
		if ($arr[1] >=1 && $arr[1] <= 60) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284954/284954/1'>DOJA Chip Nascense</a> (1-60) from Cripplers of Growth, Hiathlins, Malah-Anas, Predator Strikers and Spinetooth Hatchlings";
		} else if ($arr[1] >=61 && $arr[1] <= 100) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284955/284955/1'>DOJA Chip Elysium</a> (61-100) from Bony Spinetooths, Callous Mortiigs, Heartland Predators and Shunpike Dryads";
		} else if ($arr[1] >=101 && $arr[1] <= 130) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284956/284956/1'>DOJA Chip Scheol</a> (101-130) from Arcorashs, Esoteric Hiathlins, Glurashs and Rock Rafters";
		} else if ($arr[1] >=131 && $arr[1] <= 160) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284957/284957/1'>DOJA Chip Adonis</a> (131-160) from Creepos, Somphos and Stingers";
		} else if ($arr[1] >=161 && $arr[1] <= 204) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284958/284958/1'>DOJA Chip Penumbra</a> (161-204) from Icy Predators, Vortexoids, Demons of Water and Frosty Rafters";
		} else if ($arr[1] >=205 && $arr[1] <= 220) {
			$msg = "At level ".$arr[1]." you need to loot <a href='itemref://284959/284959/1'>DOJA Chip Inferno</a> (205-220) from Demons of Shadow, Fiery Chimeras and Somphos Logees";
		} else {
			return false;
		}
		$sendto->reply($msg);
	}
}
