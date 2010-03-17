<?php
   /*
   Bossloot Module Ver 1.1
   Written By Jaqueme
   For Budabot
   Database Adapted From One Originally 
   Compiled by Malosar For BeBot
   Boss Drop Table Database Module
   Written 5/11/07
   Last Modified 5/14/07
   */
	$MODULE_NAME = "BOSSLOOT_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "boss_namedb");
	bot::loadSQLFile($MODULE_NAME, "boss_lootdb");
	
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
