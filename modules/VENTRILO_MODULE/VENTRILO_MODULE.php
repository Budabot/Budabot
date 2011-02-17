<?php
	require_once "vent.inc.php";
	require_once "ventrilostatus.php";

	$MODULE_NAME = "VENTRILO_MODULE"; 

	Command::register($MODULE_NAME, "", "vent.php", "vent", "guild", "Ventrilo Server Info");
	
	Setting::add($MODULE_NAME, "ventaddress", "Ventrilo Server Address", "edit", "text", "unknown");
	Setting::add($MODULE_NAME, "ventport", "Ventrilo Server Port", "edit", "number", "unknown");
	Setting::add($MODULE_NAME, "ventpass", "Ventrilo Server Password", "edit", "text", "unknown");
	
	Setting::add($MODULE_NAME, "ventimplementation", "Platform your bot runs on", "edit", "options", "1", "Windows;Linux", "1;2");
	Setting::add($MODULE_NAME, "showventpassword", "Show password with vent info?", "edit", "options", "1", "true;false", "1;0");
	Setting::add($MODULE_NAME, "showextendedinfo", "Show extended vent server info?", "edit", "options", "1", "true;false", "1;0");

?>