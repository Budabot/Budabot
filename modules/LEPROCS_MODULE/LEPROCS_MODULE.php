<?php
	$MODULE_NAME = "LEPROCS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Search for Database Updates
	$this->loadSQLFile($MODULE_NAME, "leprocs");

    //nano Search
	$this->command("", "$MODULE_NAME/leprocs.php", "leprocs", ALL, "Searches for a nano and tells you were to get it.");
	$this->command("", "$MODULE_NAME/leprocs.php", "leproc", ALL, "Searches for a nano and tells you were to get it.");

?>