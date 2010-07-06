<?php
	$MODULE_NAME = "SPIRITS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "spirits");
	
	bot::command("", "$MODULE_NAME/spirits.php", "spirits", ALL, "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritslvl", ALL, "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritsagi", ALL, "Search for Spirits");
	bot::command("", "$MODULE_NAME/spirits.php", "spiritssen", ALL, "Search for Spirits");
	
	bot::help("Spirits", "$MODULE_NAME/spirits.txt", ALL, "Search for Spirits");
	
?>