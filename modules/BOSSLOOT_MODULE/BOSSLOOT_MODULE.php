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

    $db->loadSQLFile($MODULE_NAME, "boss_namedb");
	$db->loadSQLFile($MODULE_NAME, "boss_lootdb");
	
	$command->register($MODULE_NAME, "", "boss.php", "boss", "all", "Show bosses and their loot");
	$command->register($MODULE_NAME, "", "bossloot.php", "bossloot", "all", "Find which boss drops certain loot", 'boss');
	
	$help->register($MODULE_NAME, "boss", "boss.txt", "all", "How to search for bosses and bossloots");
?>
