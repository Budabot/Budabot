<?php
	$MODULE_NAME = "HELPBOT_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;

	//KOS-List Part
	bot::command("", "$MODULE_NAME/kos_list.php", "kos", ALL, "Shows the Kill On Sight List");

	//Time Part
	bot::command("", "$MODULE_NAME/time.php", "time", ALL, "Shows the time in the different timezones");

    //Whois Part
	bot::command("", "$MODULE_NAME/whois.php", "whois", ALL, "Char Infos (only current dim)");

    //Whoisall Part
	bot::command("", "$MODULE_NAME/whois.php", "whoisall", ALL, "Char Infos (all dim)");

	//Whoisorg Part
	bot::command("", "$MODULE_NAME/whois.php", "whoisorg", ALL, "Org Infos");

	//Biomaterial identification
	bot::command("", "$MODULE_NAME/biomat_identify.php", "bio", ALL, "Biomaterial Identify");
	
	//Calculator
	bot::command("", "$MODULE_NAME/calc.php", "calc", ALL, "Calculator");

	//OE Calculator
	bot::command("", "$MODULE_NAME/oe.php", "oe", ALL, "OE");

	//Flip or Roll command
	bot::command("", "$MODULE_NAME/roll.php", "flip", ALL, "Flip a coin");
	bot::command("", "$MODULE_NAME/roll.php", "roll", ALL, "Roll a random number");

	//Player History
	bot::command("", "$MODULE_NAME/player_history.php", "history", ALL, "Show a history of a player");
	
	//Smileys
	bot::command("", "$MODULE_NAME/smileys.php", "smileys", ALL, "The meaning of different Smileys");
	
	//Inspect
	bot::command("", "$MODULE_NAME/inspect.php", "inspect", ALL, "Inspects Christmas/Eart Gifts and Peren. Containers");
	
	//Alien City Generals
	bot::command("", "$MODULE_NAME/aigen.php", "aigen", ALL, "Info about Alien City Generals(which VBs they drop)");
	
	//Alien Armor
	bot::command("", "$MODULE_NAME/aiarmor.php", "aiarmor", ALL, "Tradeskillprocess for Alien Armor");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Help files
	bot::help("whois", "$MODULE_NAME/whois.txt", ALL, "Show char stats at current and all dimensions");
    bot::help("biomat", "$MODULE_NAME/biomat.txt", ALL, "Identify an Biomaterial");
    bot::help("calculator", "$MODULE_NAME/calculator.txt", ALL, "Calculator");
    bot::help("oe", "$MODULE_NAME/oe.txt", ALL, "Calculating the OE ranges");
    bot::help("fliproll", "$MODULE_NAME/fliproll.txt", ALL, "How to use the flip and roll command");
    bot::help("history", "$MODULE_NAME/history.txt", ALL, "History of a player");
    bot::help("time", "$MODULE_NAME/time.txt", ALL, "Timezones");
    bot::help("kos_list", "$MODULE_NAME/kos_list.txt", ALL, "Kill On Sight List");
    bot::help("smiley_title_inspect", "$MODULE_NAME/smiley_title_inspect.txt", ALL, "Help for Smiley,Title Level and Inspect");
    bot::help("alien_armor", "$MODULE_NAME/alien_armor.txt", ALL, "Alien armor Tradeskillprocess");
	bot::help("alien_generals", "$MODULE_NAME/alien_generals.txt", ALL, "Alien City Generals Info");
?>
