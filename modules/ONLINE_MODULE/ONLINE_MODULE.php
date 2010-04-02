<?
$MODULE_NAME = "ONLINE_MODULE";

	//Private
	bot::command("priv", "$MODULE_NAME/Online.php", "online", "all", "Shows who is the PrivChan");
	bot::command("priv", "$MODULE_NAME/Chatlist.php", "sm", "all", "Shows who is the PrivChan");
	bot::command("priv", "$MODULE_NAME/Chatlist.php", "chatlist", "all", "Shows who is the PrivChan");
	bot::command("priv", "$MODULE_NAME/Count.php", "count", "all", "Shows who is the PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "adv", "all", "Shows Adventurer큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "agent", "all", "Shows Agent큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "crat", "all", "Shows Bureaucrat큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "doc", "all", "Shows Doctor큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "enf", "all", "Shows Enforcer큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "eng", "all", "Shows Engineer큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "fix", "all", "Shows Fixer큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "keep", "all", "Shows Keeper큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "ma", "all", "Shows Martial-Artist큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "mp", "all", "Shows Meta-Physicist큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "nt", "all", "Shows Nano-Technician큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "sol", "all", "Shows Soldier큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "shade", "all", "Shows Shade큦 in PrivChan");
    bot::command("priv", "$MODULE_NAME/Count.php", "trader", "all", "Shows Trader큦 in PrivChan");

	//Guild
	bot::command("guild", "$MODULE_NAME/Count.php", "adv", "all", "Shows online Adventurer큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "agent", "all", "Shows online Agent큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "crat", "all", "Shows online Bureaucrat큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "doc", "all", "Shows online Doctor큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "enf", "all", "Shows online Enforcer큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "eng", "all", "Shows online Engineer큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "fix", "all", "Shows online Fixer큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "keep", "all", "Shows online Keeper큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "ma", "all", "Shows online Martial-Artist큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "mp", "all", "Shows online Meta-Physicist큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "nt", "all", "Shows online Nano-Technician큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "sol", "all", "Shows online Soldier큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "shade", "all", "Shows online Shade큦");
    bot::command("guild", "$MODULE_NAME/Count.php", "trader", "all", "Shows online Trader큦");
	bot::command("guild", "$MODULE_NAME/Chatlist.php", "sm", "all", "Chatlist");
	bot::command("guild", "$MODULE_NAME/Chatlist.php", "chatlist", "all", "Chatlist");
    bot::command("guild", "$MODULE_NAME/Online.php", "online", "all", "Online List");
	bot::command("guild", "$MODULE_NAME/Count.php", "count", "all", "Counts online Members");
	
	//Tells
	bot::command("msg", "$MODULE_NAME/Count.php", "adv", "all", "Shows online Adventurer큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "agent", "all", "Shows online Agent큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "crat", "all", "Shows online Bureaucrat큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "doc", "all", "Shows online Doctor큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "enf", "all", "Shows online Enforcer큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "eng", "all", "Shows online Engineer큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "fix", "all", "Shows online Fixer큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "keep", "all", "Shows online Keeper큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "ma", "all", "Shows online Martial-Artist큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "mp", "all", "Shows online Meta-Physicist큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "nt", "all", "Shows online Nano-Technician큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "sol", "all", "Shows online Soldier큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "shade", "all", "Shows online Shade큦");
    bot::command("msg", "$MODULE_NAME/Count.php", "trader", "all", "Shows online Trader큦");
	bot::command("msg", "$MODULE_NAME/Chatlist.php", "sm", "all", "Chatlist");
	bot::command("msg", "$MODULE_NAME/Chatlist.php", "chatlist", "all", "Chatlist");
    bot::command("msg", "$MODULE_NAME/Online.php", "online", "all", "Online List");
	bot::command("msg", "$MODULE_NAME/Count.php", "count", "all", "Counts online Members");

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