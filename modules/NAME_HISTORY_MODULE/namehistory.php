<?php

if (preg_match("/^namehistory (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$charid = $chatBot->get_uid($name);
	
	if ($charid == NULL) {
		$chatBot->send("Character $name does exist.", $sendto);
		return;
	}
	
	$sql = "SELECT * FROM name_history WHERE charid = '{$charid}' ORDER BY dt DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	
	if (count($data) > 0) {
		$blob = "<header> :::::: Name History for $name ($charid) :::::: <end>\n\n";
		forEach ($data as $row) {
			$blob .= "<green>{$row->name}<end> " . gmdate("M j, Y, G:i", $row->dt) . "\n";
		}
		$msg = Text::make_blob("Name History for $name", $blob);
	} else {
		$msg = "Could not find any name history for $name";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>