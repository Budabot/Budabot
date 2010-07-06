<?php 
	$MODULE_NAME = "CMD_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Tell
	$this->command("", "$MODULE_NAME/cmd.php", "cmd", LEADER, "Creates a highly visible messaage");
	
	//Helpfile
	$this->help("cmd", "$MODULE_NAME/cmd.txt", ALL, "Repeating of a msg 3 times");
?>
