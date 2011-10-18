<?php

if (preg_match("/^namehistory (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
	
	$sql = "SELECT * FROM name_history WHERE charid = (SELECT charid FROM name_history WHERE name = '{$name}' AND dimension = <dim>) AND dimension = <dim> ORDER BY dt DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	$count = count($data);

	$blob = "<header> :::::: Name History for $name ($count) :::::: <end>\n\n";
	if ($count > 0) {
		forEach ($data as $row) {
			$blob .= "<green>{$row->name}<end> " . gmdate("M j, Y, G:i", $row->dt) . "\n";
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
