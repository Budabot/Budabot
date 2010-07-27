<?php 
	$MODULE_NAME = "CMD_MODULE";

	//Tell
	bot::command("", "$MODULE_NAME/cmd.php", "cmd", "rl", "Creates a highly visible messaage");
	
	//Helpfile
	bot::help("cmd", "$MODULE_NAME/cmd.txt", "leader", "Repeating of a msg 3times", "Raidbot");
?>
