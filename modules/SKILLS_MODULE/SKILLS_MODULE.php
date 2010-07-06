<?php
	$MODULE_NAME = "SKILLS_MODULE";
	$PLUGIN_VERSION = 1.0;

	//Skills module

	bot::command("", "$MODULE_NAME/aggdef.php", "aggdef", ALL, "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	bot::command("", "$MODULE_NAME/as.php", "as", ALL, "AS: Calculates Aimed Shot.");
	bot::command("", "$MODULE_NAME/nanoinit.php", "nanoinit", ALL, "Nanoinit: Calculates Nano Init.");
	bot::command("", "$MODULE_NAME/fa.php", "fa", ALL, "FA: Calculates Full Auto recharge.");
	bot::command("", "$MODULE_NAME/burst.php", "burst", ALL, "Burst: Calculates Burst.");
	bot::command("", "$MODULE_NAME/fling.php", "fling", ALL, "Fling: Calculates Fling.");
	bot::command("", "$MODULE_NAME/mafist.php", "mafist", ALL, "MA Fist: Calculates your fist speed.");
	bot::command("", "$MODULE_NAME/dimach.php", "dimach", ALL, "Dimach: Calculates dimach facts.");
	bot::command("", "$MODULE_NAME/brawl.php", "brawl", ALL, "Brawl: Calculates brawl facts.");
	bot::command("", "$MODULE_NAME/fast.php", "fast", ALL, "Fast: Calculates Fast Attack recharge.");
	bot::command("", "$MODULE_NAME/fast.php", "fastattack", ALL, "alias for: fast");

	//Helpiles
	bot::help("skills", "$MODULE_NAME/skills.txt", ALL, "Explains the various Skill commands");

?>