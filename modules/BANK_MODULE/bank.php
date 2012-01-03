<?php

if (preg_match("/^bank browse$/i", $message)) {
	$blob = "<header> :::::: Bank Characters :::::: <end>\n\n";
	$data = $db->query("SELECT DISTINCT player FROM bank ORDER BY player ASC");
	forEach ($data as $row) {
		$character_link = Text::make_chatcmd($row->player, "/tell <myname> bank browse {$row->player}");
		$blob .= $character_link . "\n";
	}
	
	$msg = Text::make_blob('Bank Characters', $blob);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^bank browse ([a-z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));

	$blob = "<header> :::::: Backpacks for $name :::::: <end>\n\n";
	$data = $db->query("SELECT DISTINCT container, player FROM bank WHERE player = ? ORDER BY container ASC", $name);
	if (count($data) > 0) {
		forEach ($data as $row) {
			$container_link = Text::make_chatcmd($row->container, "/tell <myname> bank browse {$row->player} {$row->container}");
			$blob .= "{$container_link}\n";
		}
		
		$msg = Text::make_blob("Backpacks for $name", $blob);
	} else {
		$msg = "Could not find a bank character named $name";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^bank browse ([a-z0-9-]+) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$pack = htmlspecialchars_decode($arr[2], ENT_QUOTES);
	$limit = $setting->get('max_bank_items');

	$blob = "<header> :::::: Contents of $pack :::::: <end>\n\n";
	$data = $db->query("SELECT * FROM bank WHERE player = ? AND container = ? ORDER BY name ASC, ql ASC LIMIT {$limit}", $name, $pack);
	
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
	$limit = $setting->get('max_bank_items');

	$where_sql = '';
	forEach ($search as $word) {
		$word = str_replace("'", "''", $word);
		$where_sql .= " AND name LIKE '%{$word}%'";
	}

	$blob = "<header> :::::: Bank Search Results for '{$arr[1]}' :::::: <end>\n\n";
	$data = $db->query("SELECT * FROM bank WHERE 1 = 1 {$where_sql} ORDER BY name ASC, ql ASC LIMIT {$limit}");
	
	if (count($data) > 0) {
		forEach ($data as $row) {
			$item_link = Text::make_item($row->lowid, $row->highid, $row->ql, $row->name);
			$blob .= "{$item_link} ({$row->ql}) (<green>{$row->player}<end>, {$row->container})\n";
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
