<?php

if (preg_match("/^charid (\\d+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM name_history WHERE charid = $arr[1] AND dimension = <dim> ORDER BY dt DESC");
	$data = $db->fObject('all');
	$count = count($data);

	$blob = "<header> :::::: Name History for $name ($count) :::::: <end>\n\n";
	if ($count > 0) {
		forEach ($data as $row) {
			$blob .= "<green>{$row->name}<end> " . date("M j, Y, G:i", $row->dt) . "\n";
		}
		$msg = Text::make_blob("Name History for $name ($count)", $blob);
	} else {
		$msg = "No name history available.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
