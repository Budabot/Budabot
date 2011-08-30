<?php
	$MODULE_NAME = "NAME_HISTORY_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, 'name_history');

	Command::register($MODULE_NAME, "", "namehistory.php", "namehistory", "all", "Show all previous names the bot knows for a player");
	
	Event::register($MODULE_NAME, "1min", "save_to_db.php", "none", "Save cache of names and charids to database");
	Event::register($MODULE_NAME, "allpackets", "record_names.php", "none", "Records names and charids for saving later");

	// Help files
    Help::register($MODULE_NAME, "namehistory", "namehistory.txt", "all", "How to see previous names of a player");
?>
