<?php
	$db->loadSQLFile($MODULE_NAME, 'name_history');

	Command::register($MODULE_NAME, "", "whois.php", "whois", "all", "Show character info and name history");
	Command::register($MODULE_NAME, "", "whois.php", "whoisall", "all", "show character info and name history for all dimensions", 'whois');
	Command::register($MODULE_NAME, "", "history.php", "history", "all", "Show history of a player");
	Command::register($MODULE_NAME, "", "namehistory.php", "namehistory", "all", "Show name history of a character");
	Command::register($MODULE_NAME, "", "lookup.php", "lookup", "all", "Find the uid for a character");
	
	Event::register($MODULE_NAME, "1min", "save_to_db.php", "Save cache of names and charids to database");
	Event::register($MODULE_NAME, "allpackets", "record_names.php", "Records names and charids for saving later");

	// Help files
	Help::register($MODULE_NAME, "history", "history.txt", "all", "History of a player");
	Help::register($MODULE_NAME, "whois", "whois.txt", "all", "How to show character info and name history");
	Help::register($MODULE_NAME, "namehistory", "namehistory.txt", "all", "How to show name history of a character");
	Help::register($MODULE_NAME, "lookup", "lookup.txt", "all", "How to get the id of a character");
?>
