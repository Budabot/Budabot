<?php

if (preg_match("/^namehistory (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($name);
	if (!$uid) {
		$msg = "<highlight>$name<end> does not exist.";
		$sendto->reply($msg);
		return;
	}
	
	$sql = "SELECT * FROM name_history WHERE charid = ? AND dimension = <dim> ORDER BY dt DESC";
	$data = $db->query($sql, $uid);
	$count = count($data);

	$blob = '';
	if ($count > 0) {
		forEach ($data as $row) {
			$blob .= "<green>{$row->name}<end> " . date(Util::DATETIME, $row->dt) . "\n";
		}
		$msg = Text::make_blob("Name History for $name ($count)", $blob);
	} else {
		$msg = "No name history available.";
	}
	
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
