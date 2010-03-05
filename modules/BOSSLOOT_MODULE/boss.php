<?php

if (ereg ("^boss (.+)$", $message, $arr)) {

	$search = $arr[1];
	$search = ucwords(strtolower($search));
	
	$boss = "<header>  ::::: Boss Search  :::::<end>\n\n";
	
	$db->query("SELECT * FROM boss_namedb WHERE bossname LIKE  \"%$search%\" OR keyname = \"$search\"");
	$name_found = $db->numrows();

	If ($name_found > 1) {
	$db->query("SELECT * FROM boss_namedb WHERE bossname LIKE  \"%$search%\" OR keyname = \"$search\"");
	$data = $db->fobject("all");
	$bosses = $data;
		foreach ($bosses as $row) {
		$bossname = $row->bossname;
		$db->query("SELECT * FROM whereis WHERE name = \"$bossname\"");
		$data = $db->fobject("all"); 
			foreach ($data as $row) {
			$bossname = $row->name;
			$boss .= "<a href='chatcmd:///tell <myname> !boss $bossname'>$bossname</a>\n";
			$where = $row->answer;
			$boss .= "<green>Can be found $where<end>\n\n";
			}
		}	
	}
	elseif ($name_found  == 1) {
	$db->query("SELECT * FROM boss_namedb WHERE bossname LIKE  \"%$search%\" OR keyname = \"$search\"");
	$data = $db->fobject("all");
	foreach ($data as $row)
	$name_id = $row->bossid;
	$name = $row->bossname;
	
	$boss .= "<yellow>$name\n\n";
	
	$db->query("SELECT answer FROM whereis WHERE name = \"$name\"");
	$data = $db->fobject("all");
		foreach ($data as $row) {
		$where = $row->answer;
		
		$boss .= "<green>Can be found $where<end>\n\n";
		$boss .= "Loot:\n\n";
		}
	$db->query("SELECT * FROM boss_lootdb, aodb WHERE boss_lootdb.bossid = $name_id AND boss_lootdb.itemid = aodb.lowid");
	$data = $db->fobject("all");
			foreach ($data as $row) { 
			$loid = $row->itemid;
			$hiid = $row->highid;
			$ql = $row->highql;
			$loot_name = $row->itemname;
			$icon = $row->icon;
		
			$boss .= "<img src=rdb://".$icon.">\n";
			$boss .= "<a href='itemref://$loid/$hiid/$ql.'>$loot_name</a>\n\n";
		}
}
	
	else { 
	$boss .= "<red>There were no matches for your Query";
	}
}
else {
	$boss ="<header>  ::::: Search Results For Boss Tables  :::::<end>\n\n\n";
	$boss .="<red>You must enter search criteria after the command.<end>";
	}
	
$boss = bot::makelink("BossLoot", $boss);

if($type == "msg")
bot::send($boss, $sender);
elseif($type == "all")
bot::send($boss);
else
bot::send($boss, "guild");
?>