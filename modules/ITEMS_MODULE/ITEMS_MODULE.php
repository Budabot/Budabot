<?php
	$MODULE_NAME = "ITEMS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Load items db
	$this->loadSQLFile($MODULE_NAME, "aodb");
	
    //Items Search
	$this->command("", "$MODULE_NAME/items.php", "items", ALL, "Searches for an item in the Database");

	//Settings
    $this->addsetting('maxitems', 'Number of Items shown on the list', 'edit', '40', '30;40;50;60', "0", MODERATOR, "$MODULE_NAME/aodb_maxitems_help.txt");

	//Help files
    $this->help("items", "$MODULE_NAME/items.txt", ALL, "How to search for an item."); 
?>