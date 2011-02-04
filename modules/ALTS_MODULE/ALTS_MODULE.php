<?php
	require_once 'Alts.class.php';

	$MODULE_NAME = "ALTS_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "alts");
	
	// Alternative Characters
	bot::command("", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");
	bot::command("", "$MODULE_NAME/altsadmin.php", "altsadmin", "mod", "Alt Char handling (admin)");
	
	//Helpfile
	Help::register($MODULE_NAME, "alts", "alts.txt", "guild", "How to set alts");
	Help::register($MODULE_NAME, "altsadmin", "altsadmin.txt", "guild", "How to set alts (admins)");
?>