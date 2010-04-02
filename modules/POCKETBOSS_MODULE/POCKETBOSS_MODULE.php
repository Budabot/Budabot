<?
$MODULE_NAME = "POCKETBOSS_MODULE";
$PLUGIN_VERSION = 0.1;

	//Setup
	bot::event("setup", "$MODULE_NAME/Setup.php");

	//Pocketboss module
	bot::command("msg", "$MODULE_NAME/PocketBoss.php", "pb", "guild", "Shows what symbs a PB drops");
	bot::command("priv", "$MODULE_NAME/PocketBoss.php", "pb", "guild", "Shows what symbs a PB drops");
	bot::command("guild", "$MODULE_NAME/PocketBoss.php", "pb", "all", "Shows what symbs a PB drops");
	bot::command("msg", "$MODULE_NAME/PocketBoss.php", "symb", "guild", "Shows what PB drops a symb");
	bot::command("priv", "$MODULE_NAME/PocketBoss.php", "symb", "guild", "Shows what PB drops a symb");
	bot::command("guild", "$MODULE_NAME/PocketBoss.php", "symb", "all", "Shows what PB drops a symb");
	bot::regGroup("PB_SYMB", $MODULE_NAME, "PocketBoss List and Symb search", "symb", "pb");

	//Helpiles
    bot::help("pocketboss", "$MODULE_NAME/pocketboss.txt", "guild", "See what drops which Pocketboss", "Helpbot");
?>