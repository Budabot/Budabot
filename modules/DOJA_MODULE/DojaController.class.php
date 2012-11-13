<?php

/**
 * @Instance
 *
 * Author: Mackten (RK1)
 * Special thanks to Marebone (RK2)
 * Doja Chip Module
 * Developed for: Budabot(http://budabot.com/)
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'doja',
 *		accessLevel = 'all',
 *		description = 'Shows info of DOJA chips and which chip you need at your level',
 *		help        = 'doja.txt'
 *	)
 */
class DojaController {

	/** @Inject */
	public $text;

	/**
	 * This command handler shows info of DOJA chips.
	 *
	 * @HandlesCommand("doja")
	 * @Matches("/^doja$/i")
	 */
	public function dojaCommand($message, $channel, $sender, $sendto, $arr) {
		$msg  = "Below you can read about where to loot DOJA Chips. This information will show you the level requirements and the mobs that will drop the DOJA Chips for each level range. There are two types of Doja Chips, normal and special. You can turn one normal and one special chip in each day. Clicking the monster links below will set a waypoint to help you find your way.\n\n";
		
		$msg .= "<header2>Normal DOJA Chips<end>\n\n";
		$msg .= $this->text->make_item("284954","284954","1","Nascense")."\n";
		$msg .= $this->text->make_item("284954","284954","1",$this->text->make_image("293716"))."\n";
		$msg .= "Level Range: <highlight>1-60<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Cripplers of Growth", "/waypoint 585.2 1621.9 4310").", ".$this->text->make_chatcmd("Hiathlins", "/waypoint 817.9 1737.8 4310").", ".$this->text->make_chatcmd("Malah-Anas", "/waypoint 1763 917.6 4310").", ".$this->text->make_chatcmd("Predator Strikers", "/waypoint 783.3 1930.4 4310").", and ".$this->text->make_chatcmd("Spinetooth Hatchlings", "/waypoint 1564.8 1534.9 4312")."\n\n";	
		$msg .= $this->text->make_item("284955","284955","1","Elysium")."\n";
		$msg .= $this->text->make_item("284955","284955","1",$this->text->make_image("293715"))."\n";
		$msg .= "Level Range: <highlight>61-100<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Bony Spinetooths", "/waypoint 548.5 1708.8 4541").", ".$this->text->make_chatcmd("Callous Mortiigs", "/waypoint 639.2 656.1 4540").", ".$this->text->make_chatcmd("Heartland Predators", "/waypoint 952.0 1842.8 4542")." and ".$this->text->make_chatcmd("Shunpike Dryads", "/waypoint 682.1 917.0 4542")."\n\n";
		$msg .= $this->text->make_item("284956","284956","1","Scheol")."\n";
		$msg .= $this->text->make_item("284956","284956","1",$this->text->make_image("293714"))."\n";
		$msg .= "Level Range: <highlight>101-130<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Arcorashs", "/waypoint 1247.2 601.5 4880").", ".$this->text->make_chatcmd("Esoteric Hiathlins", "/waypoint 1449.0 1257.4 4881").", ".$this->text->make_chatcmd("Glurashs", "/waypoint 1129.3 192.5 4880")." and ".$this->text->make_chatcmd("Rock Rafters", "/waypoint 1192.2 915.9 4880")."\n\n";
		$msg .= $this->text->make_item("284957","284957","1","Adonis")."\n";
		$msg .= $this->text->make_item("284957","284957","1",$this->text->make_image("293713"))."\n";
		$msg .= "Level Range: <highlight>131-160<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Creepos", "/waypoint 2061.5 635.9 4872").", ".$this->text->make_chatcmd("Somphos", "/waypoint 1976.9 631.4 4872")." and ".$this->text->make_chatcmd("Stingers", "/waypoint 1919.9 284.1 4873")."\n\n";
		$msg .= $this->text->make_item("284958","284958","1","Penumbra")."\n";
		$msg .= $this->text->make_item("284958","284958","1",$this->text->make_image("293712"))."\n";
		$msg .= "Level Range: <highlight>161-204<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Icy Predators", "/waypoint 2125.7 873.4 4321").", ".$this->text->make_chatcmd("Vortexoids", "/waypoint 2307.2 1767.8, 4321").", ".$this->text->make_chatcmd("Demons of Water", "/waypoint 1544.5 1750.4 4320")." and ".$this->text->make_chatcmd("Frosty Rafters", "/waypoint 2454.7 1718.3 4321")."\n\n";
		$msg .= $this->text->make_item("284959","284959","1","Inferno")."\n";
		$msg .= $this->text->make_item("284959","284959","1",$this->text->make_image("293711"))."\n";
		$msg .= "Level Range: <highlight>205-220<end>\n";
		$msg .= "Looted from: ".$this->text->make_chatcmd("Demons of Shadow", "/waypoint 1671.8 1090.6 4005").", ".$this->text->make_chatcmd("Fiery Chimeras", "/waypoint 1380.7 611.8 4005")." and ".$this->text->make_chatcmd("Somphos Logees", "/waypoint 1680.8 869.9 4005")."\n\n";
		
		$msg .= "\n<header2>Special DOJA Chips<end>\n";
		$msg .= "Anything in these zones can drop a chip except maybe adds, special mobs and bosses.\n\n";
		$msg .= $this->text->make_item("282144","282144","1","Dark Ruins(Team)")."\n";
		$msg .= $this->text->make_item("282144","282144","1",$this->text->make_image("293710"))."\n";
		$msg .= "Level Range: <highlight>Below 201 (instance is level 150-200)<end>\n\n";	
		$msg .= $this->text->make_item("284961","284961","1","Alappaa")."\n";
		$msg .= $this->text->make_item("284961","284961","1",$this->text->make_image("293802"))."\n";
		$msg .= "Level Range: <highlight>201-210<end>\n\n";	
		$msg .= $this->text->make_item("284962","284962","1","Albtraum")."\n";
		$msg .= $this->text->make_item("284962","284962","1",$this->text->make_image("293801"))."\n";
		$msg .= "Level Range: <highlight>211-219<end>\n\n";	
		$msg .= $this->text->make_item("284960","284960","1","Pandemonium")."\n";
		$msg .= $this->text->make_item("284960","284960","1",$this->text->make_image("293725"))."\n";
		$msg .= "Level Range: <highlight>220<end>\n\n";
		
		$msg = $this->text->make_blob("Doja Chips", $msg);
		$sendto->reply($msg);
	}

	/**
	 * This command handler shows which DOJA chip you need at given level.
	 *
	 * @HandlesCommand("doja")
	 * @Matches("/^doja ([0-9]+)$/i")
	 */
	public function dojaLevelCommand($message, $channel, $sender, $sendto, $arr) {
		if ($arr[1] >=1 && $arr[1] <= 60) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284954","284954","1","DOJA Chip Nascense")." (1-60) from Cripplers of Growth, Hiathlins, Malah-Anas, Predator Strikers and Spinetooth Hatchlings";
		} else if ($arr[1] >=61 && $arr[1] <= 100) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284955","284955","1","DOJA Chip Elysium")." (61-100) from Bony Spinetooths, Callous Mortiigs, Heartland Predators and Shunpike Dryads";
		} else if ($arr[1] >=101 && $arr[1] <= 130) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284956","284956","1","DOJA Chip Scheol")." (101-130) from Arcorashs, Esoteric Hiathlins, Glurashs and Rock Rafters";
		} else if ($arr[1] >=131 && $arr[1] <= 160) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284957","284957","1","DOJA Chip Adonis")." (131-160) from Creepos, Somphos and Stingers";
		} else if ($arr[1] >=161 && $arr[1] <= 204) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284958","284958","1","DOJA Chip Penumbra")." (161-204) from Icy Predators, Vortexoids, Demons of Water and Frosty Rafters";
		} else if ($arr[1] >=205 && $arr[1] <= 220) {
			$msg = "At level ".$arr[1]." you need to loot ".$this->text->make_item("284959","284959","1","DOJA Chip Inferno")." (205-220) from Demons of Shadow, Fiery Chimeras and Somphos Logees";
		} else {
			return false;
		}
		$sendto->reply($msg);
	}
}
