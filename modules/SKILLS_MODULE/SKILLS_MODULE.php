<?
$MODULE_NAME = "SKILLS_MODULE";
$PLUGIN_VERSION = 0.1;

	//Skills module

	bot::command("msg", "$MODULE_NAME/aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	bot::command("priv", "$MODULE_NAME/aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");
	bot::command("guild", "$MODULE_NAME/aggdef.php", "aggdef", "all", "Agg/Def: Calculates weapon inits for your Agg/Def bar.");

	bot::command("msg", "$MODULE_NAME/as.php", "as", "all", "AS: Calculates Aimed Shot.");
	bot::command("priv", "$MODULE_NAME/as.php", "as", "all", "AS: Calculates Aimed Shot.");
	bot::command("guild", "$MODULE_NAME/as.php", "as", "all", "AS: Calculates Aimed Shot.");

	bot::command("msg", "$MODULE_NAME/nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");
	bot::command("priv", "$MODULE_NAME/nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");
	bot::command("guild", "$MODULE_NAME/nanoinit.php", "nanoinit", "all", "Nanoinit: Calculates Nano Init.");

	bot::command("msg", "$MODULE_NAME/fa.php", "fa", "all", "FA: Calculates Full Auto recharge.");
	bot::command("priv", "$MODULE_NAME/fa.php", "fa", "all", "FA: Calculates Full Auto recharge.");
	bot::command("guild", "$MODULE_NAME/fa.php", "fa", "all", "FA: Calculates Full Auto recharge.");

	bot::command("msg", "$MODULE_NAME/burst.php", "burst", "all", "Burst: Calculates Burst.");
	bot::command("priv", "$MODULE_NAME/burst.php", "burst", "all", "Burst: Calculates Burst.");
	bot::command("guild", "$MODULE_NAME/burst.php", "burst", "all", "Burst: Calculates Burst.");

	bot::command("msg", "$MODULE_NAME/fling.php", "fling", "all", "Fling: Calculates Fling.");
	bot::command("priv", "$MODULE_NAME/fling.php", "fling", "all", "Fling: Calculates Fling.");
	bot::command("guild", "$MODULE_NAME/fling.php", "fling", "all", "Fling: Calculates Fling.");

	bot::command("msg", "$MODULE_NAME/mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");
	bot::command("priv", "$MODULE_NAME/mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");
	bot::command("guild", "$MODULE_NAME/mafist.php", "mafist", "all", "MA Fist: Calculates your fist speed.");

	//Helpiles
	bot::help("skills", "$MODULE_NAME/skills.txt", "all", "Explains the various Skill commands", "Skills");

?>