<?
$MODULE_NAME = "ITEMS_MODULE";
$PLUGIN_VERSION = 0.1;
	
	//Search for Database Updates
	bot::event("24hrs", "$MODULE_NAME/UpdateDB.php", "none", "Create/Update the items Database");

    //Items Search
	bot::command("guild", "$MODULE_NAME/Items.php", "items", "all", "Searches for an item in the Database");
	bot::command("msg", "$MODULE_NAME/Items.php", "items", "all", "Searches for an item in the Database");
	bot::command("priv", "$MODULE_NAME/Items.php", "items", "all", "Searches for an item in the Database");

	//Settings
    bot::addsetting('maxitems', 'Number of Items shown on the list', 'edit', '40', '30;40;50;60', "0", "mod", "$MODULE_NAME/aodb_maxitems_help.txt");
	bot::addsetting('aodb_version', 'Current Itemsdatabase Version', 'noedit', '0', "none", "0", "mod", "$MODULE_NAME/aodb_version_help.txt");    

	//Helpfiles
    bot::help("items", "$MODULE_NAME/items.txt", "guild", "How to search for an item.", "Itemssearch Module"); 
?>