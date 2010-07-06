<?php
	$MODULE_NAME = "HELPBOT_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;

	//KOS-List Part
	$this->command("", "$MODULE_NAME/kos_list.php", "kos", ALL, "Shows the Kill On Sight List");

	//Time Part
	$this->command("", "$MODULE_NAME/time.php", "time", ALL, "Shows the time in the different timezones");

    //Whois Part
	$this->command("", "$MODULE_NAME/whois.php", "whois", ALL, "Char Infos (only current dim)");

    //Whoisall Part
	$this->command("", "$MODULE_NAME/whois.php", "whoisall", ALL, "Char Infos (all dim)");

	//Whoisorg Part
	$this->command("", "$MODULE_NAME/whois.php", "whoisorg", ALL, "Org Infos");

	//Biomaterial identification
	$this->command("", "$MODULE_NAME/biomat_identify.php", "bio", ALL, "Biomaterial Identify");
	
	//Calculator
	$this->command("", "$MODULE_NAME/calc.php", "calc", ALL, "Calculator");

	//OE Calculator
	$this->command("", "$MODULE_NAME/oe.php", "oe", ALL, "OE");

	//Flip or Roll command
	$this->command("", "$MODULE_NAME/roll.php", "flip", ALL, "Flip a coin");
	$this->command("", "$MODULE_NAME/roll.php", "roll", ALL, "Roll a random number");

	//Player History
	$this->command("", "$MODULE_NAME/player_history.php", "history", ALL, "Show a history of a player");
	
	//Smileys
	$this->command("", "$MODULE_NAME/smileys.php", "smileys", ALL, "The meaning of different Smileys");
	
	//Inspect
	$this->command("", "$MODULE_NAME/inspect.php", "inspect", ALL, "Inspects Christmas/Eart Gifts and Peren. Containers");
	
	//Alien City Generals
	$this->command("", "$MODULE_NAME/aigen.php", "aigen", ALL, "Info about Alien City Generals(which VBs they drop)");
	
	//Alien Armor
	$this->command("", "$MODULE_NAME/aiarmor.php", "aiarmor", ALL, "Tradeskillprocess for Alien Armor");

	//Setup
	$this->event("setup", "$MODULE_NAME/setup.php");

	//Help files
	$this->help("whois", "$MODULE_NAME/whois.txt", ALL, "Show char stats at current and all dimensions");
    $this->help("biomat", "$MODULE_NAME/biomat.txt", ALL, "Identify an Biomaterial");
    $this->help("calculator", "$MODULE_NAME/calculator.txt", ALL, "Calculator");
    $this->help("oe", "$MODULE_NAME/oe.txt", ALL, "Calculating the OE ranges");
    $this->help("fliproll", "$MODULE_NAME/fliproll.txt", ALL, "How to use the flip and roll command");
    $this->help("history", "$MODULE_NAME/history.txt", ALL, "History of a player");
    $this->help("time", "$MODULE_NAME/time.txt", ALL, "Timezones");
    $this->help("kos_list", "$MODULE_NAME/kos_list.txt", ALL, "Kill On Sight List");
    $this->help("smiley_title_inspect", "$MODULE_NAME/smiley_title_inspect.txt", ALL, "Help for Smiley,Title Level and Inspect");
    $this->help("alien_armor", "$MODULE_NAME/alien_armor.txt", ALL, "Alien armor Tradeskillprocess");
	$this->help("alien_generals", "$MODULE_NAME/alien_generals.txt", ALL, "Alien City Generals Info");
?>
