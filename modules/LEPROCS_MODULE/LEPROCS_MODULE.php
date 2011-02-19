<?php
	$MODULE_NAME = "LEPROCS_MODULE";

	DB::loadSQLFile($MODULE_NAME, "leprocs");

    // nano Search
	Command::register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Searches for a nano and tells you were to get it.");
	CommandAlias::register($MODULE_NAME, "leprocs", "leproc");
	
	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
?>