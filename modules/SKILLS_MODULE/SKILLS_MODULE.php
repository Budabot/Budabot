<?php
	$MODULE_NAME = "SKILLS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Skills module

	$this->command("", "$MODULE_NAME/aggdef.php", "aggdef", ALL, "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	$this->command("", "$MODULE_NAME/as.php", "as", ALL, "AS: Calculates Aimed Shot.");
	$this->command("", "$MODULE_NAME/nanoinit.php", "nanoinit", ALL, "Nanoinit: Calculates Nano Init.");
	$this->command("", "$MODULE_NAME/fa.php", "fa", ALL, "FA: Calculates Full Auto recharge.");
	$this->command("", "$MODULE_NAME/burst.php", "burst", ALL, "Burst: Calculates Burst.");
	$this->command("", "$MODULE_NAME/fling.php", "fling", ALL, "Fling: Calculates Fling.");
	$this->command("", "$MODULE_NAME/mafist.php", "mafist", ALL, "MA Fist: Calculates your fist speed.");
	$this->command("", "$MODULE_NAME/dimach.php", "dimach", ALL, "Dimach: Calculates dimach facts.");
	$this->command("", "$MODULE_NAME/brawl.php", "brawl", ALL, "Brawl: Calculates brawl facts.");
	$this->command("", "$MODULE_NAME/fast.php", "fast", ALL, "Fast: Calculates Fast Attack recharge.");
	$this->command("", "$MODULE_NAME/fast.php", "fastattack", ALL, "alias for: fast");

	//Helpiles
	$this->help("skills", "$MODULE_NAME/skills.txt", ALL, "Explains the various Skill commands");

?>