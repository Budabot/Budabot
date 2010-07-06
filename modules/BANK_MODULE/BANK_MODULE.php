<?php
	$MODULE_NAME = "BANK_MODULE";
	$PLUGIN_VERSION = 0.1;

	// Bank browse
	bot::command("", "$MODULE_NAME/bankbrowse.php", "bank", ALL, "Browse the Org Bank.");
	
	// Backpack browse
	bot::command("", "$MODULE_NAME/backpackbrowse.php", "pack", ALL, "Browse an Org Bank backpack.");
	
	// Bank lookup
	bot::command("", "$MODULE_NAME/banklookup.php", "id", ALL, "Look up an item.");
	
	// Bank search
	bot::command("", "$MODULE_NAME/banksearch.php", "find", ALL, "Search the Org Bank for an item you need.");
	
	// Help
	bot::help("bank", "$MODULE_NAME/bank.txt", ALL, "How to search for an item.");


	// Thanks to Xyphos (RK1) for helping me bugfix
?>