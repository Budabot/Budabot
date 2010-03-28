<?php
	$MODULE_NAME = "RESEARCH_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "research");
	
	bot::command("msg","$MODULE_NAME/research.php","research","all","Info on Research");
	bot::command("priv","$MODULE_NAME/research.php","research","all","Info on Research");
	bot::command("guild","$MODULE_NAME/research.php","research","all","Info on Research");
	
	bot::help("Research", "$MODULE_NAME/research.txt","all","Info on Research","Research");

?>