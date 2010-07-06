<?php
	$MODULE_NAME = "LEVEL_MODULE";
	$PLUGIN_VERSION = 0.1;
	$FOLDER = $dir;
	
	$this->loadSQLFile($MODULE_NAME, 'levels');

    //Level Infos
	$this->command("", "$MODULE_NAME/level.php", "pvp", ALL, "Show level ranges");
	$this->command("", "$MODULE_NAME/level.php", "level", ALL, "Show level ranges");
	$this->command("", "$MODULE_NAME/level.php", "lvl", ALL, "Show level ranges");
	$this->regGroup("lvlrng", $MODULE_NAME, "Show level ranges", "lvl", "level");

	//Missions
	$this->command("", "$MODULE_NAME/missions.php", "mission", ALL);
	$this->command("", "$MODULE_NAME/missions.php", "missions", ALL);
	
	//XP/SK/AXP Calculator
	$this->command("", "$MODULE_NAME/xp_sk_calc.php", "sk", ALL, "SK Calculator");
	
	$this->command("", "$MODULE_NAME/xp_sk_calc.php", "xp", ALL, "XP Calculator");

	$this->command("", "$MODULE_NAME/axp.php", "axp", ALL, "AXP Calculator");
	$this->regGroup("EXP", $MODULE_NAME, "Calculate needed XP/SK/AXP", "sk", "xp", "axp");

	//Title Levels
	$this->command("", "$MODULE_NAME/title.php", "title", ALL, "Show the Titlelevels and how much IP/Level");

	//Help files
    $this->help("level", "$MODULE_NAME/level.txt", ALL, "Levelinfos");
    $this->help("title_level", "$MODULE_NAME/title.txt", ALL, "Infos about TitleLevels");
    $this->help("missions", "$MODULE_NAME/missions.txt", ALL, "Who can roll a specific QL of a mission");
	$this->help("experience", "$MODULE_NAME/experience.txt", ALL, "XP/SK/AXP Infos");
?>
