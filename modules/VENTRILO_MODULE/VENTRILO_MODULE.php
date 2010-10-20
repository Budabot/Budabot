<?php
	require_once "vent.inc.php";
	require_once "ventrilostatus.php";

	$MODULE_NAME = "VENTRILO_MODULE"; 

	bot::command("", "$MODULE_NAME/vent.php", "vent", "guild", "Ventrilo Server Info");
	
	bot::addsetting("ventaddress", "Ventrilo Server Address", "edit", "unknown", "text");
	bot::addsetting("ventport", "Ventrilo Server Port", "edit", "unknown", "text");
	bot::addsetting("ventpass", "Ventrilo Server Password", "edit", "unknown", "text");
	
	bot::addsetting("ventimplementation", "Platform your bot runs on", "edit", "1", "Windows;Linux", "1;2");
	bot::addsetting("showventpassword", "Show password with vent info?", "edit", "1", "true;false", "1;0");
	bot::addsetting("showextendedinfo", "Show extended vent server info?", "edit", "1", "true;false", "1;0");

?>