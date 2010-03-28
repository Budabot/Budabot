<?php
	$MODULE_NAME = "WHEREIS_MODULE";
	
	bot::loadSQLFile($MODULE_NAME, "whereis");
	
	bot::command("msg", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	bot::command("priv", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	bot::command("guild", "$MODULE_NAME/whereis.php", "whereis", "all", "Whereis Database");
	
	bot::command("msg", "$MODULE_NAME/whereistell.php","bosstell","all","Request for DB Update");
	bot::command("guild", "$MODULE_NAME/whereistell.php","bosstell","all","Request for DB Update");
	
	bot::help("whereis", "$MODULE_NAME/whereis.txt", "all", "Whereis Database", "Whereis");
	
?>