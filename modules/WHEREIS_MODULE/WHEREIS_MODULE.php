<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "whereis");
	
	bot::command("", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	
	bot::help($MODULE_NAME, "whereis", "whereis.txt", "all", "Whereis Database");
	
?>