<?php
	require_once 'info_functions.php';

	$MODULE_NAME = "INFO_MODULE";

	bot::command("", "$MODULE_NAME/info.php", "info", "all", "Shows basic info");
	
	// aliases
	bot::command("", "$MODULE_NAME/info.php", "breed", "all", "Alias for !info breed");
	bot::command("", "$MODULE_NAME/info.php", "healdelta", "all", "Alias for !info healdelta");
	bot::command("", "$MODULE_NAME/info.php", "lag", "all", "Alias for !info lag");
	bot::command("", "$MODULE_NAME/info.php", "nanodelta", "all", "Alias for !info nanodelta");
	bot::command("", "$MODULE_NAME/info.php", "stats", "all", "Alias for !info stats");
	bot::command("", "$MODULE_NAME/info.php", "buffs", "all", "Alias for !info buffs");
?>