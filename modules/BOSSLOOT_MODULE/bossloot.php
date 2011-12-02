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

if (preg_match ("/^bossloot (.+)$/i", $message, $arr)) {
	$search = strtolower($arr[1]);

	$links = array("Help" => "/tell <myname> help boss");
	$blob = Text::make_header("Mobs that drop $search", $links);
	
	$loot = $db->query("SELECT DISTINCT b2.bossid, b2.bossname, w.answer FROM boss_lootdb b1 JOIN boss_namedb b2 ON b2.bossid = b1.bossid LEFT JOIN whereis w ON w.name = b2.bossname WHERE b1.itemname LIKE '%".str_replace("'", "''", $search)."%'");
	$count = count($loot);

	if ($count != 0) {
		//Find loot item and associated boss and his location
		forEach ($loot as $row) {
			$blob .= '<pagebreak>' . Text::make_chatcmd($row->bossname, "/tell <myname> boss $row->bossname") . "\n";
			$oldbossid = $bossname;
			$blob .= "<green>Can be found {$row->answer}<end>\nDrops: ";

			// get loot
			$data = $db->query("SELECT * FROM boss_lootdb b JOIN aodb a ON b.itemid = a.lowid WHERE b.bossid = {$row->bossid} AND b.itemname LIKE '%".str_replace("'", "''", $search)."%'");
			forEach ($data as $row2) {
				$blob .= Text::make_item($row2->lowid, $row2->highid, $row2->highql, $row2->itemname) . ', ';
			}
			$blob .= "\n\n";
		}
		$output = Text::make_blob("Bossloot ($count result(s))", $blob);
	} else {
		$output .= "There were no matches for your search.";
	}
	$chatBot->send($output, $sendto);
} else {
	$syntax_error = true;
}

?>
