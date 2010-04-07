<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "whereis");
	
	bot::command("", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	bot::command("", "$MODULE_NAME/whereistell.php","bosstell","all","Request for DB Update");
	
	bot::help("whereis", "$MODULE_NAME/whereis.txt", "all", "Whereis Database", "Whereis");
	
?>