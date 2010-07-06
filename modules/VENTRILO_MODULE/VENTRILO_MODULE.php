<?php
	$MODULE_NAME = "VENTRILO_MODULE"; 

	$this->command("", "$MODULE_NAME/vent.php", "vent", GUILDMEMBER, "Ventrilo Server Info");
	
	$this->addsetting("ventaddress", "Ventrilo Server Address", "edit", "unknown", "text");
	$this->addsetting("ventport", "Ventrilo Server Port", "edit", "unknown", "text");
	$this->addsetting("ventpass", "Ventrilo Server Password", "edit", "unknown", "text");
	
	$this->addsetting("ventimplementation", "Platform your bot runs on", "edit", "1", "Windows;Linux", "1;2");
	$this->addsetting("showventpassword", "Show password with vent info?", "edit", "1", "true;false", "1;0");
	$this->addsetting("showextendedinfo", "Show extended vent server info?", "edit", "1", "true;false", "1;0");

?>