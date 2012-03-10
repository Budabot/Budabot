<?php
   /*
   Bossloot Module Ver 1.1
   Written By Jaqueme
   For Budabot
   Database Adapted From One Originally
   Compiled by Malosar For BeBot
   Boss Drop Table Database Module
   Written 5/11/07
   Last Modified 5/14/07
   */

if (preg_match ("/^boss (.+)$/i", $message, $arr)) {

	$search = strtolower($arr[1]);

	$blob = "Results of Search for '$search'\n\n";
	
	// Find boss by name or key
	$bosses = $db->query("SELECT * FROM boss_namedb b LEFT JOIN whereis w ON b.bossname = w.name WHERE bossname LIKE ? OR keyname LIKE ?", "%{$search}%", "%{$search}%");
	$count = count($bosses);
	
	if ($count > 1) {
		//If multiple matches found output list of bosses
		forEach ($bosses as $row) {
			$blob .= '<pagebreak>' . Text::make_chatcmd($row->name, "/tell <myname> boss $row->name") . "\n";
			$blob .= "<green>Can be found {$row->answer}<end>\nDrops: ";
			
			// get loot
			$data = $db->query("SELECT * FROM boss_lootdb b LEFT JOIN aodb a ON (b.itemid = a.lowid OR b.itemid = a.highid) WHERE b.bossid = ?", $row->bossid);
			forEach ($data as $row2) {
				$blob .= Text::make_item($row2->lowid, $row2->highid, $row2->highql, $row2->itemname) . ', ';
			}
			$blob .= "\n\n";
		}
		$output = Text::make_blob("Boss Search Results ($count)", $blob);
	} else if ($count == 1) {
		//If single match found, output full loot table
		$row = $bosses[0];
		
		$blob .= "<yellow>{$row->bossname}<end>\n\n";
		
		$blob .= "<green>Can be found {$row->answer}<end>\n\n";
		$blob .= "Loot:\n\n";

		$data = $db->query("SELECT * FROM boss_lootdb b LEFT JOIN aodb a ON (b.itemid = a.lowid OR b.itemid = a.highid) WHERE b.bossid = ?", $row->bossid);
		forEach ($data as $row2) {
			$blob .= "<img src=rdb://{$row2->icon}>\n";
			$blob .= Text::make_item($row2->lowid, $row2->highid, $row2->highql, $row2->itemname) . "\n\n";
		}
		$output = Text::make_blob("Boss Search Results (1)", $blob);
	} else {
		$output = "There were no matches for your search.";
	}
	$sendto->reply($output);
} else {
	$syntax_error = true;
}

?>