<?php
	$MODULE_NAME = "LEPROCS_MODULE";

	DB::loadSQLFile($MODULE_NAME, "leprocs");

	Command::register($MODULE_NAME, "", "leprocs.php", "leprocs", "all", "Shows the LE Procs for a particular profession");
	CommandAlias::register($MODULE_NAME, "leprocs", "leproc");

	Help::register($MODULE_NAME, "leprocs", "leprocs.txt", "all", "How to use leprocs");
?>