<?php
	$MODULE_NAME = "WHOIS_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'name_history');

	Command::register($MODULE_NAME, "", "whois.php", "whois", "all", "Display a character's info");
	Command::register($MODULE_NAME, "", "whois.php", "whoisall", "all", "Display a character's info for all dimensions", 'whois');
	
	Event::register($MODULE_NAME, "1min", "save_to_db.php", "none", "Save cache of names and charids to database");
	Event::register($MODULE_NAME, "allpackets", "record_names.php", "none", "Records names and charids for saving later");

	// Help files
    Help::register($MODULE_NAME, "namehistory", "namehistory.txt", "all", "How to see previous names of a player");
	Help::register($MODULE_NAME, "whois", "whois.txt", "all", "Show character info and name history");
?>
