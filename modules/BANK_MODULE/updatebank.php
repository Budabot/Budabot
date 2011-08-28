<?php

if (preg_match("/^updatebank$/i", $message)) {
	$lines = file(Setting::get('bank_file_location'));
	
	if ($lines === false) {
		$msg = "Could not open file: '" . Setting::get('bank_file_location') . "'";
		$chatBot->send($msg, $sendto);
		return;
	}

	//remove the header line
	array_shift($lines);

	$db->exec("DROP TABLE IF EXISTS bank");
	$db->exec("CREATE TABLE bank (name varchar(150), lowid int, highid int, ql int, character VARCHAR(20), container VARCHAR(150), location VARCHAR(150))");

	forEach ($lines as $line) {
		list($name, $ql, $character, $container, $location, $lowId, $highId) = str_getcsv($line);
		
		if ($location != 'Bank' && $location != 'Inventory') {
			continue;
		}
		
		$name = str_replace("'", "''", $name);
		$container = str_replace("'", "''", $container);
		$sql = "INSERT INTO bank (name, lowid, highid, ql, character, container, location) VALUES ('{$name}', '{$lowId}', '{$highId}', '{$ql}', '{$character}', '{$container}', '${location}')";
		$db->exec($sql);
	}
	
	$msg = "The bank database has been updated.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
