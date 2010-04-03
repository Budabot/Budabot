<?
$MODULE_NAME = "RAIDMODULE";

	/* Commands used only for flatroll of items */
	//Set requirements for the loot roll
	bot::command("priv", "$MODULE_NAME/setminlvl.php", "setminlvl", "leader", "Sets a minlvl for a slot");
	bot::command("priv", "$MODULE_NAME/preservewinners.php", "pwinners", "leader", "Let users only win one item on a raid");		

	//Loot list and adding/removing of players	
	bot::command("priv", "$MODULE_NAME/loot.php", "loot", "leader", "Adds an item to the loot list");
	bot::command("priv", "$MODULE_NAME/add.php", "add", "all", "Let a player join/rem from a roll");
	bot::command("priv", "$MODULE_NAME/loot.php", "clear", "leader", "Clears the loot list");
	bot::command("priv", "$MODULE_NAME/list.php", "list", "leader", "Shows the loot list");
	bot::command("priv", "$MODULE_NAME/rollloot.php", "flatroll", "leader", "Rolls the loot list");
	bot::regGroup("basic_loot", $MODULE_NAME, "Handles a basic flatrolled loot system", "loot", "add", "clear", "list", "flatroll", "setminlvl", "pwinners");
	
	//Settings
	bot::addsetting("add_on_loot", "Adding to loot show on", "edit", "1", "tells;privatechat;privatechat and tells", '1;2;3', "mod");
	bot::addsetting("preserve_winners", "none", "hide", "0", "0;1");
	
	/* Commands used for the raids */
	//Start/End of a raid
	bot::command("priv", "$MODULE_NAME/raidstart.php", "raidstart", "leader", "Starts a new raid");
	bot::command("priv", "$MODULE_NAME/raidend.php", "raidend", "leader", "Let a current raid end");
	bot::command("priv", "$MODULE_NAME/raidupdate.php", "raidupdate", "leader", "Updates the raid");	
	
	//Raidlist handling
	bot::command("priv", "$MODULE_NAME/raidlist.php", "raidlist", "leader", "Show raidlist");
	bot::command("priv", "$MODULE_NAME/raidadd.php", "raidadd", "leader", "Add someone to the raidlist");
	bot::command("priv", "$MODULE_NAME/raidkick.php", "raidkick", "leader", "Kick a player from raidlist");
	bot::command("msg", "$MODULE_NAME/raidlist.php", "raidlist", "all", "Show raidlist");
	bot::command("msg", "$MODULE_NAME/raidadd.php", "raidadd", "leader", "Add someone to the raidlist");
	bot::command("msg", "$MODULE_NAME/raidkick.php", "raidkick", "leader", "Kick a player from raidlist");
	bot::command("priv", "$MODULE_NAME/raidcheck.php", "raidcheck", "leader", "Do a raidcheck");
	bot::regevent("joinPriv", "$MODULE_NAME/raidlist_timers.php");	
	bot::regevent("leavePriv", "$MODULE_NAME/raidlist_timers.php");
	bot::regevent("1min", "$MODULE_NAME/raidlist_timers_check.php");
	
	//Mypoints
	bot::regevent("joinPriv", "$MODULE_NAME/starter_pts.php");
	bot::command("msg", "$MODULE_NAME/mypoints.php", "mypoints", "all", "Let a player check his point status");
	bot::command("msg", "$MODULE_NAME/mypoints.php", "mypoint", "all", "Let a player check his point status");
	
	//Setpoints/Showpoints
	bot::command("msg", "$MODULE_NAME/showpts.php", "showpoints", "admin", "Show the points of a player");
	bot::command("msg", "$MODULE_NAME/setpts.php", "setpoints", "admin", "Set the points for a player");
	
	//Raidloot
	bot::command("msg", "$MODULE_NAME/raidloot.php", "raidloot", "leader", "Shows loot list of the sector");
	bot::command("priv", "$MODULE_NAME/raidloot.php", "raidloot", "leader", "Shows loot list of the sector");
	
	
	//bid/unbid
	bot::command("msg", "$MODULE_NAME/bid.php", "bid", "all", "Lets a player bid on an item");
	bot::command("msg", "$MODULE_NAME/unbid.php", "unbid", "all", "Lets a player remove his bid from an item");
	
	//Raidroll
	bot::command("priv", "$MODULE_NAME/raidroll.php", "raidroll", "leader", "Rolls the loot from the raid");

	//Rules
	bot::command("priv", "$MODULE_NAME/rules.php", "rules", "all", "Show Bot rules");
	bot::command("msg", "$MODULE_NAME/rules.php", "rules", "all", "Show Bot rules");
	
	//Raidpoints
	bot::command("priv", "$MODULE_NAME/raidpoints.php", "raidpoints", "leader", "Give raidpoints");
	
	//Raid configuration
	bot::command("msg", "$MODULE_NAME/raidcfg.php", "raidconfig", "mod", "Configure Raidsetup");
	
	//Settings
	bot::addsetting("starter_pts", "no", "hide", 0);
	
	/* Commands used for both methods */
	//Adding/Removing from loot
	bot::command("priv", "$MODULE_NAME/add.php", "add", "all", "Let a player adding to a slot");	
	bot::command("msg", "$MODULE_NAME/add.php", "add", "all", "Let a player adding to a slot");
	bot::command("priv", "$MODULE_NAME/rem.php", "rem", "all", "Let a player removing from a slot");
	bot::command("msg", "$MODULE_NAME/rem.php", "rem", "all", "Let a player removing from a slot");
	
	//Raidhistory
	bot::command("priv", "$MODULE_NAME/raidhistory.php", "raidhistory", "all", "Show Raidhistory");
	bot::command("msg", "$MODULE_NAME/raidhistory.php", "raidhistory", "all", "Show Raidhistory");

	//Spawntime
	bot::command("msg", "$MODULE_NAME/spawntime.php", "spawntime", "all", "Shows the spawntime of the Mobs");
	bot::command("priv", "$MODULE_NAME/spawntime.php", "spawntime", "all", "Shows the spawntime of the Mobs");
	
	//Setup	
	bot::event("setup", "$MODULE_NAME/setup.php");
	
		//Helpfiles
	bot::help("add_rem", "$MODULE_NAME/add_rem.txt", "all", "Adding/Removing to/from a lootitem", "Raid");
	bot::help("bid", "$MODULE_NAME/bid.txt", "all", "Bidding on an lootitem", "Raid");
	bot::help("flatroll", "$MODULE_NAME/flatroll.txt", "leader", "Flatroll an Item", "Raid");
	bot::help("flat_minlvl", "$MODULE_NAME/minlvl.txt", "leader", "Setting the minlvl of an Item", "Raid");
	bot::help("mypoints", "$MODULE_NAME/mypoints.txt", "all", "Show your amount of points", "Raid");	
	bot::help("pwinners", "$MODULE_NAME/pwinners.txt", "leader", "Enable preservewinners mode", "Raid");	
	bot::help("raidhistory", "$MODULE_NAME/raidhistory.txt", "all", "Shows the loothistory of a raid", "Raid");			
	bot::help("raidlist", "$MODULE_NAME/raidlist.txt", "leader", "Raidlist handling", "Raid");
	bot::help("raidpoints", "$MODULE_NAME/raidpoints.txt", "leader", "Give raidpoints", "Raid");
	bot::help("rules", "$MODULE_NAME/rules.txt", "all", "Show the rules of this Bot", "Raid");
	bot::help("raidstart_end", "$MODULE_NAME/raidstart_raidend.txt", "leader", "Start/End of an Raid", "Raid");
	bot::help("raidloot", "$MODULE_NAME/raidloot.txt", "leader", "Show the Loot of the Raid", "Raid");
	bot::help("raidconfig", "$MODULE_NAME/raidconfig.txt", "mod", "Configure the raidmodule", "Raid");

?>