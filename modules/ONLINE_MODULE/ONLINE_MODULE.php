<?
	$MODULE_NAME = "ONLINE_MODULE";

	//Private
	bot::command("", "$MODULE_NAME/online.php", "online", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "sm", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/chatlist.php", "chatlist", "all", "Shows who is the PrivChan");
	bot::command("", "$MODULE_NAME/count.php", "count", "all", "Shows who is the PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "adv", "all", "Shows Adventurer큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "agent", "all", "Shows Agent큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "crat", "all", "Shows Bureaucrat큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "doc", "all", "Shows Doctor큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "enf", "all", "Shows Enforcer큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "eng", "all", "Shows Engineer큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "fix", "all", "Shows Fixer큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "keep", "all", "Shows Keeper큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "ma", "all", "Shows Martial-Artist큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "mp", "all", "Shows Meta-Physicist큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "nt", "all", "Shows Nano-Technician큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "sol", "all", "Shows Soldier큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "shade", "all", "Shows Shade큦 in PrivChan");
    bot::command("", "$MODULE_NAME/count.php", "trader", "all", "Shows Trader큦 in PrivChan");

	//Group	
	bot::regGroup("online", $MODULE_NAME, "Show who is online(guild or privatechat)", "adv", "agent", "crat", "doc", "enf", "eng", "fix", "keep", "ma", "mp", "nt", "sol", "shade", "trader", "sm", "chatlist", "online", "count");

	//Settings
	bot::addsetting("online_tell", "Mode for Online Cmd in tells", "edit", "0", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting("count_tell", "Mode for Count Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0");
	bot::addsetting("chatlist_tell", "Mode for Chatlist Cmd in tells", "edit", "1", "Shows online privatechat members;Shows online guild members", "1;0");	
	
	//Setup
	bot::event("setup", "$MODULE_NAME/setup.php");
	
	//Helpfiles
	bot::help("chatlist", "$MODULE_NAME/chatlist.txt", "all", "Showing who is in the private group", "Raidbot");
	bot::help("online", "$MODULE_NAME/online.txt", "guild", "Show who is on from the guild", "Basic Guild Commands");	
?>