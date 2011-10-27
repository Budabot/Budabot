<?php

if (preg_match("/^namehistory (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($name);
	if (!$uid) {
		$msg = "<highlight>$name<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$sql = "SELECT * FROM name_history WHERE charid = $uid AND dimension = <dim> ORDER BY dt DESC";
	$db->query($sql);
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
