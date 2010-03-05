<?php 
$MODULE_NAME = "TEAMS_MODULE";

	bot::event("leavePriv", "$MODULE_NAME/left_chat.php", "team");
	bot::event("joinPriv", "$MODULE_NAME/joined_chat.php", "team");
	bot::command("priv", "$MODULE_NAME/team.php", "team", "leader", "Adds/Removes Players to a Team");
	bot::command("priv", "$MODULE_NAME/team.php", "teams", "leader", "Shows Current Team Setup");	
	bot::regGroup("teams", $MODULE_NAME, "Show/Create Teams", "team", "teams");

	//Helpfiles
    bot::help("teams", "$MODULE_NAME/teams.txt", "leader", "Setting up teams", "Raidbot");
?>