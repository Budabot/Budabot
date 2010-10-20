<?php
	$MODULE_NAME = "SPIRITS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "spirits");
	
	bot::command("", "$MODULE_NAME/spirits.php", "spirits", "all", "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritslvl", "all", "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritsagi", "all", "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritssen", "all", "Search for Spirits");
	
	bot::help("Spirits", "$MODULE_NAME/spirits.txt", "all", "Search for Spirits");
	
?>