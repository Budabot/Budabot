<?php
	require_once 'Alts.class.php';

	$MODULE_NAME = "ALTS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "alts");
	
	// Alternative Characters
	bot::command("", "$MODULE_NAME/alts.php", "alts", "all", "Alt Char handling");
	bot::command("", "$MODULE_NAME/altsadmin.php", "altsadmin", "mod", "Alt Char handling (admin)");
	
	//Helpfile
	bot::help("alts", "$MODULE_NAME/alts.txt", "guild", "How to set alts");
	bot::help("altsadmin", "$MODULE_NAME/altsadmin.txt", "guild", "How to set alts (admins)");
?>