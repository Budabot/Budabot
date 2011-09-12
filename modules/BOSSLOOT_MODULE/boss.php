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

	$links = array("Help" => "/tell <myname> help boss");	
	$blob = Text::make_header("Results of Search for '$search'", $links);
	
	// Find boss by name or key
	$db->query("SELECT * FROM boss_namedb b LEFT JOIN whereis w ON b.bossname = w.name WHERE bossname LIKE '%".str_replace("'", "''", $search)."%' OR keyname LIKE '%".str_replace("'", "''", $search)."%'");
	$bosses = $db->fobject("all");
	$count = count($bosses);
	
	if ($count > 1) {
		//If multiple matches found output list of bosses
		forEach ($bosses as $row) {
			$blob .= Text::make_chatcmd($row->name, "/tell <myname> boss $row->name") . "\n";
			$blob .= "<green>Can be found {$row->answer}<end>\nDrops: ";
			
			// get loot
			$db->query("SELECT * FROM boss_lootdb b JOIN aodb a ON b.itemid = a.lowid WHERE b.bossid = {$row->bossid}");
			$data = $db->fobject("all");
			forEach ($data as $row2) {
				$blob .= Text::make_item($row2->lowid, $row2->highid, $row2->ql, $row2->itemname) . ', ';
			}
			$blob .= "\n\n";
		}
		$output = Text::make_blob("Boss ($count results)", $blob);
	} else if ($count == 1) {
		//If single match found, output full loot table
		$row = $bosses[0];
		
		$blob .= "<yellow>{$row->bossname}<end>\n\n";
		
		$blob .= "<green>Can be found {$row->answer}<end>\n\n";
		$blob .= "Loot:\n\n";

		$db->query("SELECT * FROM boss_lootdb b JOIN aodb a ON b.itemid = a.lowid WHERE b.bossid = {$row->bossid}");
		$data = $db->fobject("all");
		forEach ($data as $row2) {
			$blob .= "<img src=rdb://{$row2->icon}>\n";
			$blob .= Text::make_item($row2->lowid, $row2->highid, $row2->ql, $row2->itemname) . "\n\n";
		}
		$output = Text::make_blob("Boss (1 result)", $blob);
	} else {
		$output = "There were no matches for your search.";
	}
	$chatBot->send($output, $sendto);
} else {
	$syntax_error = true;
}

?>