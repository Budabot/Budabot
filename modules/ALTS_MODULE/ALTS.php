<?php
	require_once 'Alts.class.php';

	$MODULE_NAME = "ALTS";
	
	DB::loadSQLFile($MODULE_NAME, "alts");
	
	Event::register($MODULE_NAME, "setup", "setup.php");
	
	// Alternative Characters
	Command::register($MODULE_NAME, "", "altscmd.php", "alts", "guild", "Alt Char handling");
	Subcommand::register($MODULE_NAME, "", "alts_main.php", "alts main (.+)", "guild", "alts", "Add yourself as an alt to a main", 'alts');
	
	Command::register($MODULE_NAME, "", "altsadmin.php", "altsadmin", "mod", "Alt Char handling (admin)");
	
	// Admin integration
	Command::register($MODULE_NAME, "", "altverify.php", "altvalidate", "guild", "Validate alts for admin privelages");
	Setting::add($MODULE_NAME, "validate_from_validated_alt", "Validate alts from any validated alt", "edit", "options", "0", "true;false", "1;0");
	
	//Helpfile
	Help::register($MODULE_NAME, "alts", "alts.txt", "guild", "How to set alts");
	Help::register($MODULE_NAME, "altsadmin", "altsadmin.txt", "mod", "How to set alts (admins)");
	Help::register($MODULE_NAME, "altvalidate", "altverify.txt", "guild", "How to validate alts");
?>