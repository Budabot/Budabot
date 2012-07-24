<?php

/**
 * @Instance
 *
 * Author: Mackten (RK1)
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
	public function dojaCommand($message, $channel, $sender, $sendto, $args) {
		$msg =  "<highlight>Below you can read about where to loot DOJA Chips. This information will show you the level requirements and the mobs that will drop the DOJA Chips for each level range. There are two types of Doja Chips, normal and special. You can turn one normal and one special chip in each day.\n";
		$msg .= "\n\n";	
		$msg .= "<center><img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER>\n";
		$msg .= "<orange>Normal DOJA Chips<end>\n";
		$msg .= "<img src=tdb://id:GFX_GUI_FRIENDLIST_SPLITTER></center>\n\n";
		$msg .= "<a href='itemref://284954/284954/1'>Nascense</a>\n";
		$msg .= "<a href='itemref://284954/284954/1'><img src=rdb://293716></a>\n";
		$msg .= "Level Range: <white>1-60<end>\n";
		$msg .= "Looted from: <white>Crippler of Growth, Hiathlin, Malah-Ana, Predator Striker and Spinetooth Hatchling<end>\n\n";	
		$msg .= "<a href='itemref://284955/284955/1'>Elysium</a>\n";
		$msg .= "<a href='itemref://284955/284955/1'><img src=rdb://293715></a>\n";
		$msg .= "Level Range: <white>61-100<end>\n";
		$msg .= "Looted from: <white>Bony Spinetooth, Callous Mortiig, Heartland Predator and Shunpike Dryad<end>\n\n";
		$msg .= "<a href='itemref://284956/284956/1'>Scheol</a>\n";
		$msg .= "<a href='itemref://284956/284956/1'><img src=rdb://293714></a>\n";
		$msg .= "Level Range: <white>101-130<end>\n";
		$msg .= "Looted from: <white>Arcorash, Esoteric Hiathlin, Glurash and Rock Rafter<end>\n\n";
		$msg .= "<a href='itemref://284957/284957/1'>Adonis</a>\n";
		$msg .= "<a href='itemref://284957/284957/1'><img src=rdb://293713></a>\n";
		$msg .= "Level Range: <white>131-160<end>\n";
		$msg .= "Looted from: <white>Creepos, Somphos and Stinger<end>\n\n";
		$msg .= "<a href='itemref://284958/284958/1'>Penumbra</a>\n";
		$msg .= "<a href='itemref://284958/284958/1'><img src=rdb://293712></a>\n";
		$msg .= "Level Range: <white>161-204<end>\n";
		$msg .= "Looted from: <white>Icy Predator, Vortexoid, Demon of Water and Frosty Rafter<end>\n\n";
		$msg .= "<a href='itemref://284959/284959/1'>Inferno</a>\n";
		$msg .= "<a href='itemref://284959/284959/1'><img src=rdb://293711></a>\n";
		$msg .= "Level Range: <white>205-220<end>\n";
		$msg .= "Looted from: <white>Demon of Shadow, Fiery Chimera and Somphos Logee<end>\n\n\n";
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
}
