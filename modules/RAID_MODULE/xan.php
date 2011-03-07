<?php

global $loot;
$msg = '';

if (!function_exists('get_xan_loot')) {
	function get_xan_loot($raid, $category) {
		$db = DB::get_instance();
		
		$list = "<header>::::: $raid $category Loot :::::<end>\n\n\n";
		$sql = "SELECT * FROM xan_loot WHERE raid = '$raid' AND category = '$category'";
		$db->query($sql);
		$data = $db->fObject('all');
		
		forEach ($data as $row) {
			$list .= Text::make_item($row->lowid, $row->highid, $row->ql, "<img src=rdb://{$row->imageid}>");  // image
			$list .= "\nItem: <highlight>{$row->name}<end>\n"; // name
			$list .= Text::make_link("Add to Loot List", "/tell <myname> xanloot $row->id", "chatcmd");  // add link
			$list .= "\n\n";
		}
		$list .= "\n\nXan Loot By Morgo (RK2)";
		return Text::make_link("$raid $category Loot", $list);
	}
}

if (preg_match("/^xan$/i", $message)){
	$list = "<header>::::: Legacy of the Xan Loot :::::<end>\n\n";
	
	$list .= Text::make_link("Vortexx", "/tell <myname> <symbol>vortexx", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";
	
	$list .= Text::make_link("Mitaar Hero", "/tell <myname> <symbol>mitaar", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n\n";
	
	$list .= Text::make_link("12 Man", "/tell <myname> <symbol>12m", "chatcmd") . "\n";
	$list .= "<tab>General\n";
	$list .= "<tab>Symbiants (Beta)\n";
	$list .= "<tab>Spirits (Beta)\n";
	$list .= "<tab>Profession Gems\n";

	$list .= "\n\nXan Loot By Morgo (RK2)";

	$msg = Text::make_link("Xan Loot", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^xanloot ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	
	$sql = "SELECT * FROM xan_loot WHERE id = $id";
	$db->query($sql);
	$item = $db->fObject();
	
	if ($item === null) {
		$msg = "Could not find item with id <highlight>$id<end> to add.";
		$chatBot->send($msg, $sendto);
	}
	
	$dontadd = 0;
	forEach ($loot as $key => $value) {
		if ($value["name"] == $item->name){
			$loot[$key]["multiloot"] = $value["multiloot"]+1;
			$total = $value["multiloot"]+1;
			$dontadd = 1;
			$slot = $key;
		}
	}

	if ($dontadd == 0) {
		if (is_array($loot)) {
			$nextloot = count($loot) + 1;
		} else {
			$nextloot = 1;
		}
		$loot[$nextloot]["name"] = $item->name;
		$loot[$nextloot]["linky"] = "<a href='itemref://{$item->lowid}/{$item->highid}/{$item->ql}'>{$item->name}</a>";
		$loot[$nextloot]["icon"] = $item->imageid;
		$loot[$nextloot]["multiloot"] = 1;
		$msg = "<highlight>".$item->name."<end> will be rolled in Slot <highlight>#".$nextloot;
	} else {
		$msg = "<highlight>".$item->name."<end> will be rolled in Slot <highlight>#".$slot."<end> as multiloot. Total: <yellow>".$total."<end>";
	}
	$msg .= "\nTo add use !add ".$nextloot.", or !add 0 to remove yourself";
	$chatBot->send($msg, 'priv');
} else if (preg_match("/^vortexx$/i", $message)){
	$chatBot->send(get_xan_loot('Vortexx', 'General'), $sendto);
	$chatBot->send(get_xan_loot('Vortexx', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('Vortexx', 'Spirits'), $sendto);
} else if (preg_match("/^mitaar$/i", $message)){
	$chatBot->send(get_xan_loot('Mitaar', 'General'), $sendto);
	$chatBot->send(get_xan_loot('Mitaar', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('Mitaar', 'Spirits'), $sendto);
} else if (preg_match("/^12m$/i", $message)){
	$chatBot->send(get_xan_loot('12 Man', 'General'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Symbiants'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Spirits'), $sendto);
	$chatBot->send(get_xan_loot('12 Man', 'Profession Gems'), $sendto);
} else {
	$syntax_error = true;
}

?>