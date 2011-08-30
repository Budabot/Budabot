<?php

if (preg_match("/^bank char$/i", $message)) {
	$blob = "<header> :::::: Bank Characters :::::: <end>\n\n";
	$db->query("SELECT DISTINCT character FROM bank ORDER BY character ASC");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$character_link = Text::make_chatcmd($row->character, "/tell <myname> bank pack {$row->character}");
		$blob .= $character_link . "\n";
	}
	
	$msg = Text::make_blob('Bank Characters', $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^bank pack ([a-z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));

	$blob = "<header> :::::: Backpacks for $name :::::: <end>\n\n";
	$db->query("SELECT DISTINCT container, character FROM bank WHERE character = '$name' ORDER BY container ASC");
	$data = $db->fObject('all');
	if (count($data) > 0) {
		forEach ($data as $row) {
			$container_link = Text::make_chatcmd($row->container, "/tell <myname> bank pack {$row->character} {$row->container}");
			$blob .= "{$container_link}\n";
		}
		
		$msg = Text::make_blob("Backpacks for $name", $blob);
	} else {
		$msg = "Could not find a bank character named $name";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^bank pack ([a-z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$pack = str_replace("'", "''", $arr[2]);
	$limit = Setting::get('max_bank_items');

	$blob = "<header> :::::: Contents of $pack :::::: <end>\n\n";
	$db->query("SELECT * FROM bank WHERE character = '$name' AND container = '{$pack}' ORDER BY name ASC, ql ASC LIMIT {$limit}");
	$data = $db->fObject('all');
	
	if (count($data) > 0) {
		forEach ($data as $row) {
			$item_link = Text::make_item($row->lowid, $row->highid, $row->ql, $row->name);
			$blob .= "{$item_link} ({$row->ql})\n";
		}
		
		$msg = Text::make_blob("Contents of $pack", $blob);
	} else {
		$msg = "Could not find a pack named '{$pack}' on a bank character named '{$name}'";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^bank search (.+)$/i", $message, $arr)) {
	$search = explode(' ', $arr[1]);
	$limit = Setting::get('max_bank_items');

	$where_sql = '';
	forEach ($search as $word) {
		$where_sql .= " AND name LIKE '%{$word}%'";
	}

	$blob = "<header> :::::: Bank Search Results for '{$arr[1]}' :::::: <end>\n\n";
	$db->query("SELECT * FROM bank WHERE 1 = 1 {$where_sql} ORDER BY name ASC, ql ASC LIMIT {$limit}");
	$data = $db->fObject('all');
	
	if (count($data) > 0) {
		forEach ($data as $row) {
			$item_link = Text::make_item($row->lowid, $row->highid, $row->ql, $row->name);
			$blob .= "{$item_link} ({$row->ql}) (<green>{$row->character}<end>, {$row->container})\n";
		}
		
		$msg = Text::make_blob("Bank Search Results for {$arr[1]}", $blob);
	} else {
		$msg = "Could not find any bank items when searching for '{$arr[1]}'";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
