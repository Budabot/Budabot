<?php

if(ereg ("^bossloot (.+)$", $message, $arr)) {

	$search = $arr[1];
	$search = ucwords(strtolower($search));
	$boss = "<header>  ::::: Search Result For $search  :::::<end>\n\n";
	
	$db->query("SELECT * FROM boss_lootdb WHERE itemname LIKE \"%$search%\"");
	$loot_found = $db->numrows();

	if ($loot_found != 0) {
	$boss ="<header>  :::::  Mobs that drop $search  :::::<end>";
	
	$db->query("SELECT * FROM boss_lootdb, boss_namedb, whereis WHERE boss_lootdb.itemname LIKE \"%$search%\" AND boss_namedb.bossid = boss_lootdb.bossid AND whereis.name = boss_namedb.bossname");
	$data = $db->fobject("all");
		foreach ($data as $row) {
		$bossname = $row->bossname;
		$bossid = $row->bossid;
		$where = $row->answer;
			WHILE ($oldbossname != $bossname) {
			$boss .= "\n\n<a href='chatcmd:///tell <myname> !boss $bossname'>$bossname</a>\n";
			$oldbossname = $bossname;
			$boss .= "<green>Can be found $where<end>\nDrops:";
			$db->query("SELECT * FROM boss_lootdb, aodb WHERE boss_lootdb.itemname LIKE \"%$search%\" AND boss_lootdb.bossid =	$bossid AND boss_lootdb.itemid = aodb.lowid");
			$data = $db->fobject("all");
				foreach ($data as $row) {
				$lowid = $row->lowid;
				$highid = $row->highid;
				$ql = $row->highql;
				$loot_name = $row->itemname;
				$boss .= "<a href='itemref://$lowid/$highid/$ql.'>$loot_name</a> ";
				}
			}
		}
	}
	else { 
	$boss .= "<red>There were no matches for your Query";
	}
}
else {
	$boss = "<header>  :::::  Boss Loot Item Search Result  :::::<end>\n\n\n";
	$boss .="<red>You must add a search criteria after the command.";
}	
$boss = bot::makelink("BossLoot", $boss);

if($type == "msg")
bot::send($boss, $sender);
elseif($type == "all")
bot::send($boss);
else
bot::send($boss, "guild");
?>





