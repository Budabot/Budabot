<?php
	$MODULE_NAME = "DYNA_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "dyna");
	
	bot::command("", "$MODULE_NAME/dyna.php", "dyna", ALL, "Search for RK Dynaboss");
	
	bot::help("dyna", "$MODULE_NAME/dyna.txt", ALL, "Search for RK Dynaboss");
	
?>