<?php
	$MODULE_NAME = "BANK_MODULE";

	// Bank browse
	bot::command("", "$MODULE_NAME/bankbrowse.php", "bank", "all", "Browse the Org Bank.");
	
	// Backpack browse
	bot::command("", "$MODULE_NAME/backpackbrowse.php", "pack", "all", "Browse an Org Bank backpack.");
	
	// Bank lookup
	bot::command("", "$MODULE_NAME/banklookup.php", "id", "all", "Look up an item.");
	
	// Bank search
	bot::command("", "$MODULE_NAME/banksearch.php", "find", "all", "Search the Org Bank for an item you need.");
	
	// Help
	bot::help("bank", "$MODULE_NAME/bank.txt", "all", "How to search for an item.", "Bank Search Module"); 


	// Thanks to Xyphos (RK1) for helping me bugfix
?>