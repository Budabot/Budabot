<?php
	require_once 'utils.php';

	$MODULE_NAME = "SKILLS_MODULE";

	//Skills module
	bot::command("", "$MODULE_NAME/aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	bot::command("", "$MODULE_NAME/as.php", "as", "all", "AS: Calculates Aimed Shot.");
	bot::command("", "$MODULE_NAME/nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");
	bot::command("", "$MODULE_NAME/fa.php", "fa", "all", "FA: Calculates Full Auto recharge.");
	bot::command("", "$MODULE_NAME/burst.php", "burst", "all", "Burst: Calculates Burst.");
	bot::command("", "$MODULE_NAME/fling.php", "fling", "all", "Fling: Calculates Fling.");
	bot::command("", "$MODULE_NAME/mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");
	bot::command("", "$MODULE_NAME/dimach.php", "dimach", "all", "Dimach: Calculates dimach facts.");
	bot::command("", "$MODULE_NAME/brawl.php", "brawl", "all", "Brawl: Calculates brawl facts.");
	bot::command("", "$MODULE_NAME/fast.php", "fast", "all", "Fast: Calculates Fast Attack recharge.");
	bot::command("", "$MODULE_NAME/fast.php", "fastattack", "all", "alias for: fast");
	
	//Inits
	bot::command("", "$MODULE_NAME/inits.php", "inits", "all", "shows how much inits you need for 1/1");

	//Helpiles
	bot::help("skills", "$MODULE_NAME/skills.txt", "all", "Explains the various Skill commands", "Skills");
?>