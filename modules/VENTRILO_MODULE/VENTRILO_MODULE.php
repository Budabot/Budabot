<?php
	require_once "vent.inc.php";
	require_once "ventrilostatus.php";

	$command->register($MODULE_NAME, "", "vent.php", "vent", "guild", "Show Ventrilo Server Info");
	
	$setting->add($MODULE_NAME, "ventaddress", "Ventrilo Server Address", "edit", "text", "unknown");
	$setting->add($MODULE_NAME, "ventport", "Ventrilo Server Port", "edit", "number", "unknown");
	$setting->add($MODULE_NAME, "ventpass", "Ventrilo Server Password", "edit", "text", "unknown");
	
	$setting->add($MODULE_NAME, "ventimplementation", "Platform your bot runs on", "edit", "options", "1", "Windows;Linux", "1;2");
	$setting->add($MODULE_NAME, "showventpassword", "Show password with vent info?", "edit", "options", "1", "true;false", "1;0");
	$setting->add($MODULE_NAME, "showextendedinfo", "Show extended vent server info?", "edit", "options", "1", "true;false", "1;0");

	$help->register($MODULE_NAME, "vent", "vent.txt", "guild", "How to see who is in the ventrilo channel");
?>