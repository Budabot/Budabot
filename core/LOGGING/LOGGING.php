<?php
	require_once 'Logger.class.php';

	$MODULE_NAME = "LOGGING";
	
	bot::addsetting($MODULE_NAME, "error_console", "Enable/disable logging of error messages to the console", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	bot::addsetting($MODULE_NAME, "error_file", "Enable/disable logging of error messages to a file", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	
	bot::addsetting($MODULE_NAME, "info_console", "Enable/disable logging of info messages to the console", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	bot::addsetting($MODULE_NAME, "info_file", "Enable/disable logging of info messages to a file", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	
	bot::addsetting($MODULE_NAME, "chat_console", "Enable/disable logging of chat messages to the console", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	bot::addsetting($MODULE_NAME, "chat_file", "Enable/disable logging of chat messages to a file", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	
	bot::addsetting($MODULE_NAME, "debug_console", "Enable/disable logging of debug messages to the console", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	bot::addsetting($MODULE_NAME, "debug_file", "Enable/disable logging of debug messages to a file", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	
	bot::addsetting($MODULE_NAME, "query_console", "Enable/disable logging of sql queries to the console", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	bot::addsetting($MODULE_NAME, "query_file", "Enable/disable logging of sql queries to a file", "edit", "1", "ON;OFF", "1;0", "$MODULE_NAME/logging.txt");
	
	//Help Files
	bot::help($MODULE_NAME, "logging", "logging.txt", "all", "How the logging system works");
?>