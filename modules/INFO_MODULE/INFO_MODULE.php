<?php
	require_once 'info_functions.php';

	$MODULE_NAME = "INFO_MODULE";

	bot::command("", "$MODULE_NAME/info.php", "info", "all", "Shows basic info");
	
	// aliases
	bot::command("", "$MODULE_NAME/info.php", "breed", "all", "Alias for !info breed");
	bot::command("", "$MODULE_NAME/info.php", "hd", "all", "Alias for !info hd");
	bot::command("", "$MODULE_NAME/info.php", "lag", "all", "Alias for !info lag");
	bot::command("", "$MODULE_NAME/info.php", "nd", "all", "Alias for !info nd");
	bot::command("", "$MODULE_NAME/info.php", "stats", "all", "Alias for !info stats");

?>