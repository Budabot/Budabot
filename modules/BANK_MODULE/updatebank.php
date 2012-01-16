<?php

if (preg_match("/^updatebank$/i", $message)) {
	$lines = file($setting->get('bank_file_location'));
	
	if ($lines === false) {
		$msg = "Could not open file: '" . $setting->get('bank_file_location') . "'";
		$sendto->reply($msg);
		return;
	}

	//remove the header line
	array_shift($lines);

	$db->begin_transaction();
	$db->exec("DROP TABLE IF EXISTS bank");
	$db->exec("CREATE TABLE bank (name varchar(150), lowid int, highid int, ql int, player VARCHAR(20), container VARCHAR(150), container_id INT, location VARCHAR(150))");

	forEach ($lines as $line) {
		list($name, $ql, $player, $container, $containerId, $location, $lowId, $highId) = str_getcsv($line);
		
		if ($location != 'Bank' && $location != 'Inventory') {
			continue;
		}
		if ($container == '') {
			$container = $location;
		}
		
		$sql = "INSERT INTO bank (name, lowid, highid, ql, player, container, container_id, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$db->exec($sql, $name, $lowId, $highId, $ql, $player, $container, $containerId, $location);
	}
	$db->commit();
	
	$msg = "The bank database has been updated.";
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
