<?
$MODULE_NAME = "HD_ND_MODULE";
$PLUGIN_VERSION = 0.1;

	bot::command("priv", "$MODULE_NAME/hd.php", "hd", "all", "hd");
	bot::command("guild", "$MODULE_NAME/hd.php", "hd", "all", "hd");	
	bot::command("priv", "$MODULE_NAME/nd.php", "nd", "all", "nd");
	bot::command("guild", "$MODULE_NAME/nd.php", "nd", "all", "nd");	

?>