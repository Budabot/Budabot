<?php
	require_once 'Alts.class.php';

	$MODULE_NAME = "ALTS_MODULE";
	
	DB::loadSQLFile($MODULE_NAME, "alts");
	
	// Alternative Characters
	Command::register($MODULE_NAME, "", "alts.php", "alts", "guild", "Alt Char handling");
	Subcommand::register($MODULE_NAME, "", "alts_main.php", "alts main (.+)", "guild", "alts", "Add yourself as an alt to a main", 'alts');
	
	Command::register($MODULE_NAME, "", "altsadmin.php", "altsadmin", "mod", "Alt Char handling (admin)");
	
	//Helpfile
	Help::register($MODULE_NAME, "alts", "alts.txt", "guild", "How to set alts");
	Help::register($MODULE_NAME, "altsadmin", "altsadmin.txt", "mod", "How to set alts (admins)");
?>