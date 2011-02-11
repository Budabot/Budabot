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

    $links = array("Help;chatcmd:///tell <myname> help boss");

$output = '';
if (preg_match ("/^bossloot (.+)$/i", $message, $arr)) {

	$search = $arr[1];
	$search = ucwords(strtolower($search));

	$boss = Text::make_header("Mobs that drop $search", $links);
	
	$db->query("SELECT * FROM boss_lootdb WHERE itemname LIKE '%".str_replace("'", "''", $search)."%'");
	$loot_found = $db->numrows();

	if ($loot_found != 0) {
		//Find loot item and associated boss and his location
		$db->query("SELECT * FROM boss_lootdb, boss_namedb, whereis WHERE boss_lootdb.itemname LIKE '%".str_replace("'", "''", $search)."%' AND boss_namedb.bossid = boss_lootdb.bossid AND whereis.name = boss_namedb.bossname");
		$data = $db->fobject("all");
		forEach ($data as $row) {
			$bossname = $row->bossname;
			$bossid = $row->bossid;
			$where = $row->answer;
			//output Bossname once
			while ($oldbossname != $bossname) {
				$boss .= "\n\n<a href='chatcmd:///tell <myname> !boss $bossname'>$bossname</a>\n";
				$oldbossname = $bossname;
				$boss .= "<green>Can be found $where<end>\nDrops:";
				//output bossloot as many times as necessary
				$db->query("SELECT * FROM boss_lootdb, aodb WHERE boss_lootdb.itemname LIKE '%".str_replace("'", "''", $search)."%' AND boss_lootdb.bossid = $bossid AND boss_lootdb.itemid = aodb.lowid");
				$data = $db->fobject("all");
				forEach ($data as $row) {
					$lowid = $row->lowid;
					$highid = $row->highid;
					$ql = $row->highql;
					$loot_name = $row->itemname;
					$boss .= "<a href='itemref://$lowid/$highid/$ql.'>$loot_name</a> ";
				}
			}
		}
		$output = Text::make_link("BossLoot", $boss);
	} else {
		$output .= "<yellow>There were no matches for your search.<end>";
	}
} else {
	$output .= "<yellow>You must add a search criteria after the command.<end>";
}

bot::send($output, $sendto);

?>