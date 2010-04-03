<?
$MODULE_NAME = "TOWERS_MODULE";
$PLUGIN_VERSION = 0.1;

	//Tower messages
    bot::event("towers", "$MODULE_NAME/towers_messages.php", "none", "Show Attack Messages"); 
	bot::command("guild", "$MODULE_NAME/towers_result.php", "battle", "all", "Shows the last Tower Attack messages");
  	bot::command("guild", "$MODULE_NAME/towers_result.php", "victory", "all", "Shows the last Tower Battle results");
	bot::command("priv", "$MODULE_NAME/towers_result.php", "battle", "all", "Shows the last Tower Attack messages");
  	bot::command("priv", "$MODULE_NAME/towers_result.php", "victory", "all", "Shows the last Tower Battle results");
	bot::command("msg", "$MODULE_NAME/towers_result.php", "battle", "guid", "Shows the last Tower Attack messages");
  	bot::command("msg", "$MODULE_NAME/towers_result.php", "victory", "guild", "Shows the last Tower Battle results");

	bot::regGroup("Tower_Battle", $MODULE_NAME, "Show Tower Attack Results", "battle", "victory");
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");
	
	//Helpfiles
	bot::help("towers", "$MODULE_NAME/towers.txt", "guild", "Show Tower messages", "Towers");
?>