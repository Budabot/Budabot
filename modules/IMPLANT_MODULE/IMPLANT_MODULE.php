<?php
	require_once 'implant_functions.php';

	// Setup
	$db->loadSQLFile($MODULE_NAME, "implant_requirements");
	$db->loadSQLFile($MODULE_NAME, "premade_implant");

	// Private
	$command->register($MODULE_NAME, "", "implant.php", "implant", "all", "Shows info about implants given a ql or stats", "implant.txt");
	$command->register($MODULE_NAME, "", "premade.php", "premade", "all", "Searches for implants out of the premade implants booths", "premade_implant.txt");
	$command->register($MODULE_NAME, "", "cluster.php", "cluster", "all", "cluster location", "cluster.txt");
?>