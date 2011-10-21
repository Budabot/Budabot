<?php
	require_once 'implant_functions.php';

	// Setup
	DB::loadSQLFile($MODULE_NAME, "implant_requirements");
	DB::loadSQLFile($MODULE_NAME, "premade_implant");

	// Private
	Command::register($MODULE_NAME, "", "impql.php", "impql", "all", "Shows stats for implant at given ql", 'implant');
	Command::register($MODULE_NAME, "", "impreq.php", "impreq", "all", "Shows the highest ql implant that can be worn given treatment and ability", 'implant');
	Command::register($MODULE_NAME, "", "premade.php", "premade", "all", "Searches for implants out of the premade implants booths");
	Command::register($MODULE_NAME, "", "cluster.php", "cluster", "all", "cluster location");

	// Help
	Help::register($MODULE_NAME, "implant", "implant.txt", "all", "How to use impql/impreq");
	Help::register($MODULE_NAME, "premade", "premade_implant.txt", "guild", "How to search for premade implants");
	Help::register($MODULE_NAME, "cluster", "cluster.txt", "all", "How to use cluster");
?>