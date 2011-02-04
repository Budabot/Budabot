<?php
	$MODULE_NAME = "LEPROCS_MODULE";

	//Search for Database Updates
	DB::loadSQLFile($MODULE_NAME, "leprocs");

    //nano Search
	bot::command("", "$MODULE_NAME/leprocs.php", "leprocs", "all", "Searches for a nano and tells you were to get it.");
	bot::command("", "$MODULE_NAME/leprocs.php", "leproc", "all", "Searches for a nano and tells you were to get it.");
?>