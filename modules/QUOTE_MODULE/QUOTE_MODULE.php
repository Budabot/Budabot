<?php
	$MODULE_NAME = "QUOTE_MODULE";

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");
	bot::event("24hrs", "$MODULE_NAME/quotestats.php", "none", "Update Quote Stats");

	//Commands
	bot::command("", "$MODULE_NAME/quotestats.php", "quoteupdate", "mod", "Update Quote Stats");
	bot::command("", "$MODULE_NAME/quote.php", "quote", "all", "Add/Remove/View Quotes");

	bot::addsetting($MODULE_NAME, "quote_add_min", "Minimum org level needed to add quote.", "edit", "-1", "Anyone;At least in Private chat;0;1;2;3;4;5;6", "-2;-1;0;1;2;3;4;5;6", "mod");
	bot::addsetting($MODULE_NAME, "quote_stat_count", "Number of users shown in stats.", "edit", "10", "number", "0", "mod");

	//Help files
	bot::help($MODULE_NAME, "quote", "quote.txt", "all", "Add/Remove/View Quotes");
?>