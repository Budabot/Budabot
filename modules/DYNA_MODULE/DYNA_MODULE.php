<?php
	$MODULE_NAME = "DYNA_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "dyna");
	
	bot::command("", "$MODULE_NAME/dyna.php", "dyna", "all", "Search for RK Dynaboss");
	
	bot::help("dyna", "$MODULE_NAME/dyna.txt", "all", "Search for RK Dynaboss", "dyna");
	
?>