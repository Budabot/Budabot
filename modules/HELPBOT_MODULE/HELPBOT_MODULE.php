<?php
$MODULE_NAME = "HELPBOT_MODULE";
$PLUGIN_VERSION = 0.1;
$FOLDER = $dir;

	//KOS-List Part
	bot::command("guild", "$MODULE_NAME/Kos_list.php", "kos", "all", "Shows the Kill On Sight List");
	bot::command("msg", "$MODULE_NAME/Kos_list", "kos", "guild", "Shows the Kill On Sight List");
	bot::command("priv", "$MODULE_NAME/Kos_list", "kos", "all", "Shows the Kill On Sight List");

	//Time Part
	bot::command("guild", "$MODULE_NAME/Time.php", "time", "all", "Shows the time in the different timezones");
	bot::command("msg", "$MODULE_NAME/Time.php", "time", "all", "Shows the time in the different timezones");
	bot::command("priv", "$MODULE_NAME/Time.php", "time", "all", "Shows the time in the different timezones");

    //Whois Part
	bot::command("guild", "$MODULE_NAME/Whois.php", "whois", "all", "Char Infos (only current dim)");
	bot::command("msg", "$MODULE_NAME/Whois.php", "whois", "all", "Char Infos (only current dim)");
	bot::command("priv", "$MODULE_NAME/Whois.php", "whois", "all", "Char Infos (only current dim)");

    //Whoisall Part
	bot::command("guild", "$MODULE_NAME/Whois.php", "whoisall", "all", "Char Infos (all dim)");
	bot::command("msg", "$MODULE_NAME/Whois.php", "whoisall", "all", "Char Infos(all dim)");
	bot::command("priv", "$MODULE_NAME/Whois.php", "whoisall", "all", "Char Infos(all dim)");

	//Whoisorg Part
	bot::command("guild", "$MODULE_NAME/Whois.php", "whoisorg", "all", "Org Infos");
	bot::command("msg", "$MODULE_NAME/Whois.php", "whoisorg", "all", "Org Infos");
	bot::command("priv", "$MODULE_NAME/Whois.php", "whoisorg", "all", "Org Infos");

    //PVP Ranges
	bot::command("guild", "$MODULE_NAME/PvP_ranges.php", "pvp", "all", "Show PVP ranges");
	bot::command("msg", "$MODULE_NAME/PvP_ranges.php", "pvp", "guild", "Show PVP ranges");
	bot::command("priv", "$MODULE_NAME/PvP_ranges.php", "pvp", "all", "Show PVP ranges");

    //Level Infos
	bot::command("guild", "$MODULE_NAME/Levels.php", "level", "all", "Show level ranges");
	bot::command("msg", "$MODULE_NAME/Levels.php", "level", "guild", "Show level ranges");
	bot::command("priv", "$MODULE_NAME/Levels.php", "level", "all", "Show level ranges");
	bot::command("guild", "$MODULE_NAME/Levels.php", "lvl", "all", "Show level ranges");
	bot::command("msg", "$MODULE_NAME/Levels.php", "lvl", "guild", "Show level ranges");
	bot::command("priv", "$MODULE_NAME/Levels.php", "lvl", "all", "Show level ranges");
	bot::regGroup("lvlrng", $MODULE_NAME, "Show level ranges", "lvl", "level");

	//Missions
	bot::command("guild", "$MODULE_NAME/Missions.php", "mission", "all");
	bot::command("msg", "$MODULE_NAME/Missions.php", "mission", "guild");
	bot::command("priv", "$MODULE_NAME/Missions.php", "mission", "all");
	
	//Biomaterial identification
	bot::command("guild", "$MODULE_NAME/Biomat_identify.php", "bio", "all", "Biomaterial Identify");
	bot::command("msg", "$MODULE_NAME/Biomat_identify.php", "bio", "guild", "Biomaterial Identify");
	bot::command("priv", "$MODULE_NAME/Biomat_identify.php", "bio", "all", "Biomaterial Identify");
	
	//Calculator
	bot::command("guild", "$MODULE_NAME/Calc.php", "calc", "all", "Calculator");
	bot::command("msg", "$MODULE_NAME/Calc.php", "calc", "guild", "Calculator");
	bot::command("priv", "$MODULE_NAME/Calc.php", "calc", "all", "Calculator");

	//OE Calculator
	bot::command("guild", "$MODULE_NAME/Oe.php", "oe", "all", "OE");
	bot::command("msg", "$MODULE_NAME/Oe.php", "oe", "guild", "OE");
	bot::command("priv", "$MODULE_NAME/Oe.php", "oe", "all", "OE");

	//XP/SK/AXP Calculator
	bot::command("guild", "$MODULE_NAME/XP_SK_Calc.php", "sk", "all", "SK Calculator");
	bot::command("msg", "$MODULE_NAME/XP_SK_Calc.php", "sk", "guild", "SK Calculator");
	bot::command("priv", "$MODULE_NAME/XP_SK_Calc.php", "sk", "all", "SK Calculator");
	
	bot::command("guild", "$MODULE_NAME/XP_SK_Calc.php", "xp", "all", "XP Calculator");
	bot::command("msg", "$MODULE_NAME/XP_SK_Calc.php", "xp", "guild", "XP Calculator");
	bot::command("priv", "$MODULE_NAME/XP_SK_Calc.php", "xp", "all", "XP Calculator");

	bot::command("guild", "$MODULE_NAME/AXP.php", "axp", "all", "AXP Calculator");
	bot::command("msg", "$MODULE_NAME/AXP.php", "axp", "guild", "AXP Calculator");
	bot::command("priv", "$MODULE_NAME/AXP.php", "axp", "all", "AXP Calculator"); 
	bot::regGroup("EXP", $MODULE_NAME, "Calculate needed XP/SK/AXP", "sk", "xp", "axp");

	//Flip or Roll command
	bot::command("msg", "$MODULE_NAME/Roll.php", "flip", "all", "Flip a coin"); 
	bot::command("msg", "$MODULE_NAME/Roll.php", "roll", "all", "Rolls a number between given ranges");
	bot::command("priv", "$MODULE_NAME/Roll.php", "flip", "all", "Flip a coin"); 
	bot::command("priv", "$MODULE_NAME/Roll.php", "roll", "all", "Rolls a number between given ranges");
	bot::command("guild", "$MODULE_NAME/Roll.php", "flip", "all", "Flip a coin"); 
	bot::command("guild", "$MODULE_NAME/Roll.php", "roll", "all", "Rolls a number between given ranges");
	bot::command("msg", "$MODULE_NAME/Roll.php", "verify", "all", "Verifys a flip or roll");
	
	//Player History
	bot::command("msg", "$MODULE_NAME/Player_History.php", "history", "guild", "Show a history of a player");	
	bot::command("priv", "$MODULE_NAME/Player_History.php", "history", "guild", "Show a history of a player");	
	bot::command("guild", "$MODULE_NAME/Player_History.php", "history", "all", "Show a history of a player");	
	
	//Whereis
	bot::command("msg", "$MODULE_NAME/whereis.php", "whereis", "guild", "Show where places/uniques are on RK");	
	bot::command("priv", "$MODULE_NAME/whereis.php", "whereis", "all", "Show where places/uniques are on RK");	
	bot::command("guild", "$MODULE_NAME/whereis.php", "whereis", "all", "Show where places/uniques are on RK");	
	
	//Title Levels
	bot::command("msg", "$MODULE_NAME/title.php", "title", "guild", "Show the Titlelevels and how much IP/Level");	
	bot::command("priv", "$MODULE_NAME/title.php", "title", "all", "Show the Titlelevels and how much IP/Level");	
	bot::command("guild", "$MODULE_NAME/title.php", "title", "all", "Show the Titlelevels and how much IP/Level");	

	//Smileys
	bot::command("msg", "$MODULE_NAME/smileys.php", "smileys", "guild", "The meaning of different Smileys");	
	bot::command("priv", "$MODULE_NAME/Smileys.php", "smileys", "all", "The meaning of different Smileys");	
	bot::command("guild", "$MODULE_NAME/Smileys.php", "smileys", "all", "The meaning of different Smileys");	
	
	//Inspect
	bot::command("msg", "$MODULE_NAME/inspect.php", "inspect", "guild", "Inspects Christmas/Eart Gifts and Peren. Containers");	
	bot::command("priv", "$MODULE_NAME/inspect.php", "inspect", "all", "Inspects Christmas/Eart Gifts and Peren. Containers");	
	bot::command("guild", "$MODULE_NAME/inspect.php", "inspect", "all", "Inspects Christmas/Eart Gifts and Peren. Containers");
	
	//Alien City Generals
	bot::command("msg", "$MODULE_NAME/aigen.php", "aigen", "guild", "Info about Alien City Generals(which VB´s they drop)");	
	bot::command("priv", "$MODULE_NAME/aigen.php", "aigen", "all", "Info about Alien City Generals(which VB´s they drop)");	
	bot::command("guild", "$MODULE_NAME/aigen.php", "aigen", "all", "Info about Alien City Generals(which VB´s they drop)");	
	
	//Alien Armor
	bot::command("msg", "$MODULE_NAME/aiarmor.php", "aiarmor", "guild", "Tradeskillprocess for Alien Armor");	
	bot::command("priv", "$MODULE_NAME/aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");	
	bot::command("guild", "$MODULE_NAME/aiarmor.php", "aiarmor", "all", "Tradeskillprocess for Alien Armor");	

	//Setup
	bot::event("setup", "$MODULE_NAME/Setup.php");

	//Helpfiles
	bot::help("whois", "$MODULE_NAME/whois.txt", "guild", "Show char stats at current and all dimensions", "Helpbot");
	bot::help("pvpranges", "$MODULE_NAME/pvpranges.txt", "guild", "Pvp ranges", "Helpbot");
    bot::help("level", "$MODULE_NAME/level.txt", "guild", "Levelinfos", "Helpbot");
    bot::help("biomat", "$MODULE_NAME/biomat.txt", "guild", "Identify an Biomaterial", "Helpbot");
    bot::help("calculator", "$MODULE_NAME/calculator.txt", "guild", "Calculator", "Helpbot");
    bot::help("oe", "$MODULE_NAME/oe.txt", "guild", "Calculating the OE ranges", "Helpbot");
	bot::help("experience", "$MODULE_NAME/experience.txt", "guild", "XP/SK/AXP Infos", "Helpbot");
    bot::help("fliproll", "$MODULE_NAME/fliproll.txt", "guild", "How to use the flip and roll command", "Helpbot");
    bot::help("history", "$MODULE_NAME/history.txt", "guild", "History of a player", "Helpbot");
    bot::help("time", "$MODULE_NAME/time.txt", "guild", "Timezones", "Helpbot");
    bot::help("kos_list", "$MODULE_NAME/Kos_list.txt", "guild", "Kill On Sight List", "Helpbot");
    bot::help("smiley_title_inspect", "$MODULE_NAME/smiley_title_inspect.txt", "guild", "Help for Smiley,Title Level and Inspect", "Helpbot");
    bot::help("alien_armor", "$MODULE_NAME/alien_armor.txt", "guild", "Alien armor Tradeskillprocess", "Helpbot");
	bot::help("alien_generals", "$MODULE_NAME/alien_generals.txt", "guild", "Alien City Generals Info", "Helpbot");    
?>
