<?php
	$MODULE_NAME = "DYNA_MODULE";
	
	$this->loadSQLFile($MODULE_NAME, "dyna");
	
	$this->command("", "$MODULE_NAME/dyna.php", "dyna", ALL, "Search for RK Dynaboss");
	
	$this->help("dyna", "$MODULE_NAME/dyna.txt", ALL, "Search for RK Dynaboss");
	
?>