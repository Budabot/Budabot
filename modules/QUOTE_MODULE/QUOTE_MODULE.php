<?php
	$MODULE_NAME = "QUOTE_MODULE";

	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");
	$this->event("24hrs", "$MODULE_NAME/quotestats.php", "none", "Update Quote Stats");

	//Commands
	$this->command("", "$MODULE_NAME/quotestats.php", "quoteupdate", MODERATOR, "Update Quote Stats");
	$this->command("", "$MODULE_NAME/quote.php", "quote", ALL, "Add/Remove/View Quotes");

	//Help files
	$this->help("quote", "$MODULE_NAME/quote.txt", ALL, "Add/Remove/View Quotes");

	$this->addsetting("quote_add_min", "Minimum org level needed to add quote.", "edit", "-1", "Anyone;At least in Private chat;0;1;2;3;4;5;6", "-2;-1;0;1;2;3;4;5;6", MODERATOR);
	$this->addsetting("quote_stat_count", "Number of users shown in stats.", "edit", "10", "number", "0", MODERATOR);
?>