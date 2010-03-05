<?php
	$MODULE_NAME = "BOSSLOOT_MODULE";
	
	bot::event("setup", "$MODULE_NAME/setup.php");
	
	bot::command("msg", "$MODULE_NAME/boss.php", "boss", "all", "Bossloot Tables");
	bot::command("priv", "$MODULE_NAME/boss.php", "boss", "all", "Bossloot Tables");
	bot::command("guild", "$MODULE_NAME/boss.php", "boss", "all", "Bossloot Tables");
	
	bot::command("msg", "$MODULE_NAME/bossloot.php", "bossloot", "all", "Bossloot Search");
	bot::command("priv", "$MODULE_NAME/bossloot.php", "bossloot", "all", "Bossloot Search");
	bot::command("guild", "$MODULE_NAME/bossloot.php", "bossloot", "all", "Bossloot Search");
	
	bot::command("msg", "$MODULE_NAME/bosstell.php","bosstell","all","Request for DB Update");
	bot::command("guild", "$MODULE_NAME/bosstell.php","bosstell","all","Request for DB Update");
	
	bot::help("Boss", "$MODULE_NAME/boss.txt", "all", "Bossloot Tables", "Boss");
	
?>
