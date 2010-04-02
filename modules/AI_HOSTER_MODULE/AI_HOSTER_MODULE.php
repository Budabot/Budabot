<?
$MODULE_NAME = "AI_HOSTER_MODULE";

	bot::command("priv", "$MODULE_NAME/ai_hoster.php", "crhoster", "leader", "Start/End a city raidhoster roll");
	bot::command("priv", "$MODULE_NAME/Add.php", "crhadd", "all", "Adding to the city raidhoster roll");
	bot::command("priv", "$MODULE_NAME/Rem.php", "crhrem", "all", "Removing from the city raidhoster roll");
	
	bot::help("aihoster", "$MODULE_NAME/help.txt", "all", "Rolling for a city raidhoster roll", "Raidbot");
?>