<?php
	$MODULE_NAME = "BANK_MODULE";

	// Bank browse
	Command::register($MODULE_NAME, "", "bankbrowse.php", "bank", "all", "Browse the Org Bank.");
	
	// Backpack browse
	Command::register($MODULE_NAME, "", "backpackbrowse.php", "pack", "all", "Browse an Org Bank backpack.");
	
	// Bank lookup
	Command::register($MODULE_NAME, "", "banklookup.php", "id", "all", "Look up an item.");
	
	// Bank search
	Command::register($MODULE_NAME, "", "banksearch.php", "find", "all", "Search the Org Bank for an item you need.");
	
	// Help
	Help::register($MODULE_NAME, "bank", "bank.txt", "all", "How to search for an item.");

	// Thanks to Xyphos (RK1) for helping me bugfix
?>