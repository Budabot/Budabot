<?php

if (preg_match("/^lookup (\\d+)$/i", $message, $arr)) {
	$charid = $arr[1];
	$db->query("SELECT * FROM name_history WHERE charid = $charid AND dimension = <dim> ORDER BY dt DESC");
	$data = $db->fObject('all');
	$count = count($data);

	$blob = "<header> :::::: Name History for $charid ($count) :::::: <end>\n\n";
	if ($count > 0) {
		forEach ($data as $row) {
			$link = Text::make_chatcmd($row->name, "/tell <myname> lookup $row->name");
			$blob .= "$link " . date("M j, Y, G:i", $row->dt) . "\n";
		}
		$msg = Text::make_blob("Name History for $charid ($count)", $blob);
	} else {
		$msg = "No history available for character id <highlight>$charid<end>.";
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^lookup (.*)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	
	$db->query("SELECT * FROM name_history WHERE name LIKE '" . str_replace("'", "''", $name) . "' AND dimension = <dim> ORDER BY dt DESC");
	$data = $db->fObject('all');
	$count = count($data);

	$blob = "<header> :::::: Character Ids for $name ($count) :::::: <end>\n\n";
	if ($count > 0) {
		forEach ($data as $row) {
			$link = Text::make_chatcmd($row->charid, "/tell <myname> lookup $row->charid");
			$blob .= "$link " . date("M j, Y, G:i", $row->dt) . "\n";
		}
		$msg = Text::make_blob("Character Ids for $name ($count)", $blob);
	} else {
		$msg = "No history available for character <highlight>$name<end>.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>