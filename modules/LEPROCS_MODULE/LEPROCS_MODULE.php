<?php
	$MODULE_NAME = "LEPROCS_MODULE";

	//Search for Database Updates
	DB::loadSQLFile($MODULE_NAME, "leprocs");

    //nano Search
	Command::register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Searches for a nano and tells you were to get it.");
	Command::register($MODULE_NAME, "", "leprocs.php", "leproc", "all", "Searches for a nano and tells you were to get it.");
	
	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
?>