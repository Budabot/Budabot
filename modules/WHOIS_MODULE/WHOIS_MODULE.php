<?php
	$db->loadSQLFile($MODULE_NAME, 'name_history');

	$command->register($MODULE_NAME, "", "whois.php", "whois", "all", "Show character info and name history", "whois.txt");
	$command->register($MODULE_NAME, "", "whois.php", "whoisall", "all", "Show character info and name history for all dimensions", "whois.txt");
	$command->register($MODULE_NAME, "", "history.php", "history", "all", "Show history of a player", "history.txt");
	$command->register($MODULE_NAME, "", "namehistory.php", "namehistory", "all", "Show name history of a character", "namehistory.txt");
	$command->register($MODULE_NAME, "", "lookup.php", "lookup", "all", "Find the uid for a character", "lookup.txt");
	
	$event->register($MODULE_NAME, "1min", "save_to_db.php", "Save cache of names and charids to database");
	$event->register($MODULE_NAME, "allpackets", "record_names.php", "Records names and charids for saving later");
?>
