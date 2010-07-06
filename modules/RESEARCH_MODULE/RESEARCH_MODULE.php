<?php
	$MODULE_NAME = "RESEARCH_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "research");
	
	bot::command("", "$MODULE_NAME/research.php", "research", ALL, "Info on Research");
	
	bot::help("Research", "$MODULE_NAME/research.txt", ALL, "Info on Research");

?>