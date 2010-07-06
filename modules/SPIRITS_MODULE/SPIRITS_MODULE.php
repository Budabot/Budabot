<?php
	$MODULE_NAME = "SPIRITS_MODULE";
	
	$this->loadSQLFile($MODULE_NAME, "spirits");
	
	$this->command("", "$MODULE_NAME/spirits.php", "spirits", ALL, "Search for Spirits");
	$this->command("", "$MODULE_NAME/spirits.php", "spiritslvl", ALL, "Search for Spirits");
	$this->command("", "$MODULE_NAME/spirits.php", "spiritsagi", ALL, "Search for Spirits");
	$this->command("", "$MODULE_NAME/spirits.php", "spiritssen", ALL, "Search for Spirits");
	
	$this->help("Spirits", "$MODULE_NAME/spirits.txt", ALL, "Search for Spirits");
	
?>