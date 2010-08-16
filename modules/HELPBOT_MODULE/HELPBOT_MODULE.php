<?php
	$MODULE_NAME = "HELPBOT_MODULE";

	//KOS-List Part
	bot::command("", "$MODULE_NAME/kos_list.php", "kos", "all", "Shows the Kill On Sight List");

	//Time Part
	bot::command("", "$MODULE_NAME/time.php", "time", "all", "Shows the time in the different timezones");

    //Whois Part
	bot::command("", "$MODULE_NAME/whois.php", "whois", "all", "Char Infos (only current dim)");

    //Whoisall Part
	bot::command("", "$MODULE_NAME/whois.php", "whoisall", "all", "Char Infos (all dim)");

	//Whoisorg Part
	bot::command("", "$MODULE_NAME/whois.php", "whoisorg", "all", "Org Infos");

	//Biomaterial identification
	bot::command("", "$MODULE_NAME/biomat_identify.php", "bio", "all", "Biomaterial Identify");
	
	//Calculator
	bot::command("", "$MODULE_NAME/calc.php", "calc", "all", "Calculator");

	//OE Calculator
	bot::command("", "$MODULE_NAME/oe.php", "oe", "all", "OE");

	//Flip or Roll command
	bot::command("", "$MODULE_NAME/roll.php", "flip", "all", "Flip a coin");
	bot::command("", "$MODULE_NAME/roll.php", "roll", "all", "Roll a random number");
	bot::command("", "$MODULE_NAME/roll.php", "verify", "all", "Verifies a flip/roll");

	//Player History
	bot::command("", "$MODULE_NAME/player_history.php", "history", "all", "Show a history of a player");
	
	//Smileys
	bot::command("", "$MODULE_NAME/smileys.php", "smileys", "guild", "The meaning of different Smileys");
	
	//Inspect
	bot::command("", "$MODULE_NAME/inspect.php", "inspect", "guild", "Inspects Christmas/Eart Gifts and Peren. Containers");
	
	//Alien City Generals
	bot::command("", "$MODULE_NAME/aigen.php", "aigen", "guild", "Info about Alien City Generals(which VBs they drop)");
	
	//Alien Armor
	bot::command("", "$MODULE_NAME/aiarmor.php", "aiarmor", "guild", "Tradeskillprocess for Alien Armor");

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Helpfiles
	bot::help("whois", "$MODULE_NAME/whois.txt", "guild", "Show char stats at current and all dimensions", "Helpbot");
    bot::help("biomat", "$MODULE_NAME/biomat.txt", "guild", "Identify an Biomaterial", "Helpbot");
    bot::help("calculator", "$MODULE_NAME/calculator.txt", "guild", "Calculator", "Helpbot");
    bot::help("oe", "$MODULE_NAME/oe.txt", "guild", "Calculating the OE ranges", "Helpbot");
    bot::help("fliproll", "$MODULE_NAME/fliproll.txt", "guild", "How to use the flip and roll command", "Helpbot");
    bot::help("history", "$MODULE_NAME/history.txt", "guild", "History of a player", "Helpbot");
    bot::help("time", "$MODULE_NAME/time.txt", "guild", "Timezones", "Helpbot");
    bot::help("kos_list", "$MODULE_NAME/kos_list.txt", "guild", "Kill On Sight List", "Helpbot");
    bot::help("smiley_title_inspect", "$MODULE_NAME/smiley_title_inspect.txt", "guild", "Help for Smiley,Title Level and Inspect", "Helpbot");
    bot::help("alien_armor", "$MODULE_NAME/alien_armor.txt", "guild", "Alien armor Tradeskillprocess", "Helpbot");
	bot::help("alien_generals", "$MODULE_NAME/alien_generals.txt", "guild", "Alien City Generals Info", "Helpbot");
?>
