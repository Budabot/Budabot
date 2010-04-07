<?
	$MODULE_NAME = "MEMBERS_MODULE";

	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");

	//Show Members
	bot::command("", "$MODULE_NAME/showmembers.php", "members", "all", "Shows all members of this bot");
	bot::command("", "$MODULE_NAME/addmember.php", "addmember", "mod", "Adds a new members");
	bot::command("", "$MODULE_NAME/remmember.php", "remmember", "mod", "Removes a members");

	//Autoinvites members
	bot::event("logOn", "$MODULE_NAME/autoinvitemember.php", "none", "Enable Autoinvite for members");
	
	//Helpfiles
	bot::help("membersmodule", "$MODULE_NAME/members.txt", "all", "How to use closed privgroup Feature", "Membersbot");
?>