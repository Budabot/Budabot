<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "whereis");
	
	bot::command("", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	
	Help::register($MODULE_NAME, "whereis", "whereis.txt", "all", "Whereis Database");
	
?>