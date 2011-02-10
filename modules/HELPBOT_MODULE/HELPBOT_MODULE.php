<?php
	require_once 'db_utils.php';
	require_once 'trickle_functions.php';

	$MODULE_NAME = "HELPBOT_MODULE";

	DB::loadSQLFile($MODULE_NAME, "roll_kos");
	DB::loadSQLFile($MODULE_NAME, "dyna");
	DB::loadSQLFile($MODULE_NAME, "research");
	DB::loadSQLFile($MODULE_NAME, "trickle");
	DB::loadSQLFile($MODULE_NAME, "playfields");

	Command::register($MODULE_NAME, "", "kos.php", "kos", "guild", "Shows the Kill On Sight List");
	Command::register($MODULE_NAME, "", "time.php", "time", "all", "Shows the time in the different timezones");
	Command::register($MODULE_NAME, "", "whois.php", "whois", "all", "Char Infos (only current dim)");
	Command::register($MODULE_NAME, "", "whois.php", "whoisall", "all", "Char Infos (all dim)");
	Command::register($MODULE_NAME, "", "whois.php", "whoisorg", "all", "Org Infos");
	Command::register($MODULE_NAME, "", "biomat_identify.php", "bio", "all", "Biomaterial Identify");
	Command::register($MODULE_NAME, "", "calc.php", "calc", "all", "Calculator");
	Command::register($MODULE_NAME, "", "oe.php", "oe", "all", "OE");
	Command::register($MODULE_NAME, "", "player_history.php", "history", "all", "Show a history of a player");
	Command::register($MODULE_NAME, "", "smileys.php", "smileys", "all", "The meaning of different Smileys");
	Command::register($MODULE_NAME, "", "inspect.php", "inspect", "all", "Inspects Christmas/Eart Gifts and Peren. Containers");
	Command::register($MODULE_NAME, "", "aigen.php", "aigen", "all", "Info about Alien City Generals(which VBs they drop)");
	Command::register($MODULE_NAME, "", "aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");
	Command::register($MODULE_NAME, "", "mobloot.php", "mobloot", "all", "loot QL Infos ");
	Command::register($MODULE_NAME, "", "random.php", "random", "all", "Random order");
	Command::register($MODULE_NAME, "", "cluster.php", "cluster", "all", "cluster location");
	Command::register($MODULE_NAME, "", "buffitem.php", "buffitem", "all", "buffitem look up");
	Command::register($MODULE_NAME, "", "whatbuffs.php", "whatbuffs", "all", "find items that buff");
	Command::register($MODULE_NAME, "", "dyna.php", "dyna", "all", "Search for RK Dynaboss");
	Command::register($MODULE_NAME, "", "research.php", "research", "all", "Info on Research");
	Command::register($MODULE_NAME, "", "trickle.php", "trickle", "all", "Shows how much skills you will gain by increasing an ability");
	Command::register($MODULE_NAME, "", "playfields.php", "playfields", "all", "Shows all the playfields including IDs and short names");
	Command::register($MODULE_NAME, "", "waypoint.php", "waypoint", "all", "Creats a waypoint link");

	// Flip or Roll command
	Command::register($MODULE_NAME, "", "roll.php", "flip", "all", "Flip a coin");
	Command::register($MODULE_NAME, "", "roll.php", "roll", "all", "Roll a random number");
	Command::register($MODULE_NAME, "", "roll.php", "verify", "all", "Verifies a flip/roll");

	// Max XP calculator
	Command::register($MODULE_NAME, "", "cap_xp.php", "capsk", "all", "Max SK Calculator");
	Command::register($MODULE_NAME, "", "cap_xp.php", "capxp", "all", "Max XP Calculator");

	// Help files
	Help::register($MODULE_NAME, "whois", "whois.txt", "all", "Show char stats at current and all dimensions");
    Help::register($MODULE_NAME, "biomat", "biomat.txt", "all", "Identify an Biomaterial");
    Help::register($MODULE_NAME, "calc", "calculator.txt", "all", "Calculator");
    Help::register($MODULE_NAME, "oe", "oe.txt", "all", "Calculating the OE ranges");
    Help::register($MODULE_NAME, "roll", "roll.txt", "all", "How to use the flip and roll command");
    Help::register($MODULE_NAME, "history", "history.txt", "all", "History of a player");
    Help::register($MODULE_NAME, "time", "time.txt", "all", "Timezones");
    Help::register($MODULE_NAME, "kos", "kos.txt", "all", "Kill On Sight List");
    Help::register($MODULE_NAME, "inspect", "inspect.txt", "all", "How to use inspect");
	Help::register($MODULE_NAME, "smileys", "inspect.txt", "all", "How to use smileys");
    Help::register($MODULE_NAME, "aiarmor", "aiarmor.txt", "all", "Alien armor Tradeskillprocess");
	Help::register($MODULE_NAME, "aigen", "aigen.txt", "all", "Alien City Generals Info");
	Help::register($MODULE_NAME, "buffitem", "buffitem.txt", "all", "How to use buffitem");
	Help::register($MODULE_NAME, "cluster", "cluster.txt", "all", "How to use cluster");
	Help::register($MODULE_NAME, "mobloot", "mobloot.txt", "all", "How to use mobloot");
	Help::register($MODULE_NAME, "whatbuffs", "whatbuffs.txt", "all", "How to use whatbuffs");
	Help::register($MODULE_NAME, "dyna", "dyna.txt", "all", "Search for RK Dynaboss");
	Help::register($MODULE_NAME, "research", "research.txt", "all", "Info on Research");
	Help::register($MODULE_NAME, "capxp", "capxp.txt", "all", "Set your reasearch bar for max xp/sk");
	Help::register($MODULE_NAME, "trickle", "trickle.txt", "all", "How to use trickle");
?>
