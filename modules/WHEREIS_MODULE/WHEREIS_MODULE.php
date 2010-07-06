<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "whereis");
	
	bot::command("", "$MODULE_NAME/whereis.php", "whereis", ALL, "Whereis Database");
	
	bot::help("whereis", "$MODULE_NAME/whereis.txt", ALL, "Whereis Database");
	
?>