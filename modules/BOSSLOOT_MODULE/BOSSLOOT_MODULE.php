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
	
	bot::command("", "$MODULE_NAME/boss.php", "boss", "all", "Bossloot Tables");
	bot::command("", "$MODULE_NAME/bossloot.php", "bossloot", "all", "Bossloot Search");
	
	bot::help($MODULE_NAME, "Boss", "boss.txt", "all", "Bossloot Tables", "Boss");
	
?>
