<?
	$MODULE_NAME = "ITEMS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Load items db
	bot::loadSQLFile($MODULE_NAME, "aodb");
	
    //Items Search
	bot::command("", "$MODULE_NAME/items.php", "items", "all", "Searches for an item in the Database");

	//Settings
    bot::addsetting('maxitems', 'Number of Items shown on the list', 'edit', '40', '30;40;50;60', "0", "mod", "$MODULE_NAME/aodb_maxitems_help.txt");

	//Helpfiles
    bot::help("items", "$MODULE_NAME/items.txt", "guild", "How to search for an item.", "Itemssearch Module"); 
?>