<?php
	require_once 'db_utils.php';
	require_once 'trickle_functions.php';

	$MODULE_NAME = "HELPBOT_MODULE";
	
	bot::event("setup", "$MODULE_NAME/setup.php");
	
	bot::loadSQLFile($MODULE_NAME, "dyna");
	bot::loadSQLFile($MODULE_NAME, "research");
	bot::loadSQLFile($MODULE_NAME, "trickle");
	bot::loadSQLFile($MODULE_NAME, "playfields");

	bot::command("", "$MODULE_NAME/kos_list.php", "kos", "all", "Shows the Kill On Sight List");
	bot::command("", "$MODULE_NAME/time.php", "time", "all", "Shows the time in the different timezones");
	bot::command("", "$MODULE_NAME/whois.php", "whois", "all", "Char Infos (only current dim)");
	bot::command("", "$MODULE_NAME/whois.php", "whoisall", "all", "Char Infos (all dim)");
	bot::command("", "$MODULE_NAME/whois.php", "whoisorg", "all", "Org Infos");
	bot::command("", "$MODULE_NAME/biomat_identify.php", "bio", "all", "Biomaterial Identify");
	bot::command("", "$MODULE_NAME/calc.php", "calc", "all", "Calculator");
	bot::command("", "$MODULE_NAME/oe.php", "oe", "all", "OE");
	bot::command("", "$MODULE_NAME/player_history.php", "history", "all", "Show a history of a player");
	bot::command("", "$MODULE_NAME/smileys.php", "smileys", "all", "The meaning of different Smileys");
	bot::command("", "$MODULE_NAME/inspect.php", "inspect", "all", "Inspects Christmas/Eart Gifts and Peren. Containers");
	bot::command("", "$MODULE_NAME/aigen.php", "aigen", "all", "Info about Alien City Generals(which VBs they drop)");
	bot::command("", "$MODULE_NAME/aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");
	bot::command("", "$MODULE_NAME/mobloot.php", "mobloot", "all", "loot QL Infos ");
	bot::command("", "$MODULE_NAME/random.php", "random", "all", "Random order");
	bot::command("", "$MODULE_NAME/cluster.php", "cluster", "all", "cluster location");
	bot::command("", "$MODULE_NAME/buffitem.php", "buffitem", "all", "buffitem look up");
	bot::command("", "$MODULE_NAME/whatbuffs.php", "whatbuffs", "all", "find items that buff");
	bot::command("", "$MODULE_NAME/dyna.php", "dyna", "all", "Search for RK Dynaboss");
	bot::command("", "$MODULE_NAME/research.php", "research", "all", "Info on Research");
	bot::command("", "$MODULE_NAME/trickle.php", "trickle", "all", "Shows how much skills you will gain by increasing an ability");
	bot::command("", "$MODULE_NAME/waypoint.php", "waypoint", "all", "Creats a waypoint link");

	// Flip or Roll command
	bot::command("", "$MODULE_NAME/roll.php", "flip", "all", "Flip a coin");
	bot::command("", "$MODULE_NAME/roll.php", "roll", "all", "Roll a random number");
	bot::command("", "$MODULE_NAME/roll.php", "verify", "all", "Verifies a flip/roll");
	
	// Max XP calculator
	bot::command("", "$MODULE_NAME/cap_xp.php", "capsk", "all", "Max SK Calculator");
	bot::command("", "$MODULE_NAME/cap_xp.php", "capxp", "all", "Max XP Calculator");

	// Help files
	bot::help("whois", "$MODULE_NAME/whois.txt", "all", "Show char stats at current and all dimensions", "Helpbot");
    bot::help("biomat", "$MODULE_NAME/biomat.txt", "all", "Identify an Biomaterial", "Helpbot");
    bot::help("calc", "$MODULE_NAME/calculator.txt", "all", "Calculator", "Helpbot");
    bot::help("oe", "$MODULE_NAME/oe.txt", "all", "Calculating the OE ranges", "Helpbot");
    bot::help("fliproll", "$MODULE_NAME/fliproll.txt", "all", "How to use the flip and roll command", "Helpbot");
    bot::help("history", "$MODULE_NAME/history.txt", "all", "History of a player", "Helpbot");
    bot::help("time", "$MODULE_NAME/time.txt", "all", "Timezones", "Helpbot");
    bot::help("kos_list", "$MODULE_NAME/kos_list.txt", "all", "Kill On Sight List", "Helpbot");
    bot::help("smiley_title_inspect", "$MODULE_NAME/smiley_title_inspect.txt", "all", "Help for Smiley,Title Level and Inspect", "Helpbot");
    bot::help("alien_armor", "$MODULE_NAME/alien_armor.txt", "all", "Alien armor Tradeskillprocess", "Helpbot");
	bot::help("alien_generals", "$MODULE_NAME/alien_generals.txt", "all", "Alien City Generals Info", "Helpbot");
	bot::help("buffitem", "$MODULE_NAME/buffitem.txt", "all", "How to use buffitem");
	bot::help("cluster", "$MODULE_NAME/cluster.txt", "all", "How to use cluster");
	bot::help("mobloot", "$MODULE_NAME/mobloot.txt", "all", "How to use mobloot");
	bot::help("whatbuffs", "$MODULE_NAME/whatbuffs.txt", "all", "How to use whatbuffs");
	bot::help("dyna", "$MODULE_NAME/dyna.txt", "all", "Search for RK Dynaboss");
	bot::help("research", "$MODULE_NAME/research.txt", "all", "Info on Research");
	bot::help("capxp", "$MODULE_NAME/capxp.txt", "all", "Set your reasearch bar for max xp/sk");
	bot::help("trickle", "$MODULE_NAME/trickle.txt", "all", "How to use trickle");
?>
