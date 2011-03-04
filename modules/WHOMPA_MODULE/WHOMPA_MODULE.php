<?php
	$MODULE_NAME = "WHOMPA_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "whompa_cities");
    
	Command::register($MODULE_NAME, "", "whompa.php", "whompa", "all", "Shows the whompa route from one city to another");
	CommandAlias::register($MODULE_NAME, 'whompa', 'whompas');
	
	Help::register($MODULE_NAME, "whompa", "whompa.txt", "all", "How to find the whompa route from one city to another");
?>