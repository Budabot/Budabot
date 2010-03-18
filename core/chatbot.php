<?php 
   /*
   ** Author: Sebuda/Derroylo (both RK2)
   ** Description: This class provides the basic functions for the bot.
   ** Version: 0.5.9
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005
   ** Date(last modified): 05.02.2007
   ** 
   ** Copyright (C) 2005, 2006, 2007 Carsten Lohmann and J. Gracik
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */ 

class bot extends AOChat{

/*===============================
** Name: __construct
** Constructor of this class.
*/	function __construct($vars, $settings){
		global $db;
		global $curMod;
		
		$curMod = "Basic Settings";
		$this->settings = $settings;
		$this->vars = $vars;
        $this->vars["name"] = ucfirst(strtolower($this->vars["name"]));
		
		//Set startuptime
		$this->vars["startup"] = time();

		//Create commando/event settings table if not exists
		$db->query("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(5), `type` VARCHAR(10), `file` VARCHAR(255), `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `grp` VARCHAR(25) DEFAULT 'none')");
		$db->query("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `mod` VARCHAR(50), `mode` VARCHAR(10), `setting` VARCHAR(50) Default '0', `options` VARCHAR(50) Default '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `help` VARCHAR(60))");
		$db->query("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `cat` VARCHAR(50), `description` VARCHAR(50), `admin` VARCHAR(10), `verify` INT Default '0')");

		//Prepare commando/event settings table
		$db->query("UPDATE cmdcfg_<myname> SET `verify` = 0");
		$db->query("UPDATE hlpcfg_<myname> SET `verify` = 0");
		$db->query("UPDATE cmdcfg_<myname> SET `status` = 0 WHERE `cmdevent` = 'event' AND `type` = 'setup'");
		$db->query("UPDATE cmdcfg_<myname> SET `grp` = 'none'");		
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `module` = 'none'");
		
		//To reduce Query´s save the current commands/events in an array
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'");
		while($row = $db->fObject())
		  	$this->existing_commands[$row->type][$row->cmd] = true;

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		while($row = $db->fObject())
		  	$this->existing_subcmds[$row->type][$row->cmd] = true;

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event'");
		while($row = $db->fObject()) {
			if(bot::verifyNameConvention($row->file))
			  	$this->existing_events[$row->type][$row->file] = true;
		}

		$db->query("SELECT * FROM hlpcfg_<myname>");
		while($row = $db->fObject())
		  	$this->existing_helps[$row->name] = true;
		  	
		$db->query("SELECT * FROM settings_<myname>");
		while($row = $db->fObject())
		  	$this->existing_settings[$row->name] = true;
		
		// Load the Core Modules
		if($this->settings['debug'] > 0) print("\n:::::::CORE MODULES::::::::\n");
		if($this->settings['debug'] > 0) print("MODULE_NAME:(SYSTEM.php)\n");
				include "./core/SYSTEM/SYSTEM.php";
		if($this->settings['debug'] > 0) print("MODULE_NAME:(ADMIN.php)\n");
				include "./core/ADMIN/ADMIN.php";		
		if($this->settings['debug'] > 0) print("MODULE_NAME:(BAN.php)\n");
				include "./core/BAN/BAN.php";	
		if($this->settings['debug'] > 0) print("MODULE_NAME:(HELP.php)\n");
				include "./core/HELP/HELP.php";	
		if($this->settings['debug'] > 0) print("MODULE_NAME:(CONFIG.php)\n");
				include "./core/CONFIG/CONFIG.php";	
		if($this->settings['debug'] > 0) print("MODULE_NAME:(SETTINGS.php)\n");
				include "./core/SETTINGS/SETTINGS.php";
		if($this->settings['debug'] > 0) print("MODULE_NAME:(ORG_ROSTER.php)\n");
				include "./core/ORG_ROSTER/ORG_ROSTER.php";
		if($this->settings['debug'] > 0) print("MODULE_NAME:(BASIC_CONNECTED_EVENTS.php)\n");
				include "./core/BASIC_CONNECTED_EVENTS/BASIC_CONNECTED_EVENTS.php";
		if($this->settings['debug'] > 0) print("MODULE_NAME:(PRIV_TELL_LIMIT.php)\n");
				include "./core/PRIV_TELL_LIMIT/PRIV_TELL_LIMIT.php";
		$curMod = "";
								
		// Load Plugin Modules
		if($this->settings['debug'] > 0) print("\n:::::::PLUGIN MODULES::::::::\n");	
		//Start Transaction
		$db->beginTransaction();
		//Load modules
		$this->loadModules();
		//Submit the Transactions
		$db->Commit();

		//Load active commands
		if($this->settings['debug'] > 0) print("\nSetting up commands.\n");
		$this->loadCommands();

		//Load active subcommands
		if($this->settings['debug'] > 0) print("\nSetting up subcommands.\n");
		$this->loadSubcommands();

		//Load active events
		if($this->settings['debug'] > 0) print("\nSetting up events.\n");
		$this->loadEvents();

		//kill unused vars
		unset($this->existing_commands);
		unset($this->existing_events);
		unset($this->existing_subcmds);
		unset($this->existing_settings);
		unset($this->existing_helps);
		
		//Delete old entrys in the DB
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
		$db->query("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");
	}
	

/*===============================
** Name: connect
** Connect to AO chat servers.
*/	function connectAO($login, $password){
		//Create Aochat
		AOChat::AOchat("callback");

		// Choose Server
		if($this->vars["dimension"] == 1) {
			$server = "chat.d1.funcom.com";
			$port = 7101;
		} elseif($this->vars["dimension"] == 2) {
			$server = "chat.d2.funcom.com";
			$port = 7102;
		} elseif($this->vars["dimension"] == 3) {
			$server = "chat.d3.funcom.com";
			$port = 7103;
		} elseif($this->vars["dimension"] == 4) {
			$server = "chat.dt.funcom.com";
			$port = 7109;
		} else {
			echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "		    	  No valid Server to connect with!\n";
			echo "		       Available dimensions are 0, 1, 2 and 3!";
			echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		  	sleep(10);
		  	die();
		}
		
		// Begin the login process
		echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "		    	  Connecting to AO Server...\n";
		echo "		    	     ($server)";
		echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		AOChat::connect($server, $port);
		sleep(2);
		if($this->state != "auth") {
			echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "		    	  Connection to AO Servers failed!\n";
			echo "		     Pls check your Internetconnection and Firewall";
			echo "\n\n\n\n\n\n\n\n\n\n\n\n";
			sleep(10);
			die();
		}

		echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "		    	  Authenticate login data...\n";
		echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		AOChat::authenticate($login, $password);
		sleep(2);
		if($this->state != "login") {
			echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "		    	  Authenticating of your data failed!\n";
			echo "		          Pls check your Account Informations!";
			echo "\n\n\n\n\n\n\n\n\n\n\n\n";
			sleep(10);
			die();
		}
		echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "		    	  Logging in {$this->vars["name"]}...\n";
		echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		AOChat::login($this->vars["name"]);
		sleep(2);
		if($this->state != "ok") {
			echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
			echo "		    	Logging in of {$this->vars["name"]} failed!\n";
			echo "		    Pls check if this char exists on the Account!";
			echo "\n\n\n\n\n\n\n\n\n\n\n\n";
			sleep(10);
			die();
		}
		
		echo "\n\n\n\n\n\n\n\n\n\n\n\n\n";
		echo "		    	  All Systems ready....\n";
		echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		sleep(2);
		
		// Set cron timers
		$this->vars["2sec"] 			= time() + $this->settings["CronDelay"];
		$this->vars["1min"] 			= time() + $this->settings["CronDelay"];
		$this->vars["1hour"] 			= time() + $this->settings["CronDelay"];
		$this->vars["24hours"]			= time() + $this->settings["CronDelay"];
		$this->vars["15min"] 			= time() + $this->settings["CronDelay"];
	}
	
/*===============================
** Name: ping
** Get next packet info from AOChat
*/	function ping(){
		return AOChat::wait_for_packet();
	}

/*===============================
** Name: connectedEvents
** Execute Events that needs to be executed right after login
*/	function connectedEvents(){
		// Check files, for all 'connect events'.
		foreach($this->_connect as $filename)
			include $filename;
	}
		
/*===============================
** Name: makeLink
** Make click link reference.
*/	function makeLink($name, $content, $type = "blob", $style = NULL){
		if($type == "blob") { // Normal link.
			if(strlen($content) > $this->settings["max_blob_size"]) {  //Split the windows if they are too big
				$pages = ceil(strlen($content) / $this->settings["max_blob_size"]);
			  	$content = explode("\n", $content);
				$page = 1;
			  	foreach($content as $line) {
					if($page > 1 && $display)
						$result[$page] .= "<header>::::: $name Page $page :::::<end>\n";
					$display = false;		
				    $result[$page] .= $line."\n";
				    if(strlen($result[$page]) >= $this->settings["max_blob_size"]) {
						$result[$page] = "<a ".$style."href=\"text://".$this->settings["default window color"].$result[$page]."\">$name</a> (Page <highlight>$page<end> of <highlight>$pages<end>)";
				    	$page++;
						$display = true;					  
					}
				}
				$result[$pages] = "<a ".$style."href=\"text://".$this->settings["default window color"].$result[$pages]."\">$name</a> (Page <highlight>$pages<end> of <highlight>$pages<end>)";				
				return $result;
			} else
				return "<a ".$style."href=\"text://".$this->settings["default window color"].$content."\">$name</a>";
		} elseif($type == "text") // Majic link.
			return "<a ".$style."href='text://".$content."'>$name</a>";
		elseif($type == "chatcmd") // Chat command.
			return "<a ".$style."href='chatcmd://".$content."'>$name</a>";
		//Adds support for right clicking usernames in chat, providing you with a menu of options (ignore etc.) (see 18.1 AO patchnotes)
		elseif($type == "user") // Adds user link
			return "<a ".$style."href=\"user://".$content."\">$name</a>";
		else							
			return false;			
	}
	
/*===============================
** Name: makeItem
** Make item link reference.
*/	function makeItem($lowID, $hiID,  $ql, $name){
		if($hiID != NULL && $lowID != NULL && $ql != NULL && $name !=NULL){ // make Item
			return "<a href='itemref://" . $lowID . "/" . $hiID . "/" . $ql . "'>" . $name . "</a>";
		}
		else								
			return false;		
	}
	
/*===============================
** Name: send
** Send chat messages back to aochat servers thru aochat.
*/	function send($message, $who = NULL, $disable_relay = false){
		// Color			
		$message = str_replace("<header>", $this->settings['default header color'], $message);
		$message = str_replace("<highlight>", $this->settings['default highlight color'], $message);			
		$message = str_replace("<black>", "<font color='#000000'>", $message);
		$message = str_replace("<white>", "<font color='#FFFFFF'>", $message);			
		$message = str_replace("<yellow>", "<font color='#FFFF00'>", $message);				
		$message = str_replace("<blue>", "<font color='#8CB5FF'>", $message);
		$message = str_replace("<green>", "<font color='#00DE42'>", $message);
		$message = str_replace("<white>", "<font color='#FFFFFF'>", $message);				
		$message = str_replace("<red>", "<font color='#ff0000'>", $message);				
		$message = str_replace("<orange>", "<font color='#FCA712'>", $message);	
		$message = str_replace("<grey>", "<font color='#C3C3C3'>", $message);
		$message = str_replace("<myname>", "{$this->vars["name"]}", $message);
		$message = str_replace("<tab>", "    ", $message);
		$message = str_replace("<end>", "</font>", $message);
		$message = str_replace("<symbol>", $this->settings["symbol"] , $message);
		
		// Send
		if($message == 'addbuddy') // Addbuddy
			AOChat::buddy_add($who);
		elseif($message == 'rembuddy') { // Rembuddy
			AOChat::buddy_remove($who);
			unset($this->buddyList[$who]);
		} elseif($message == 'isbuddy') {
            if(AoChat::buddy_exists($who))
                return true;
            else
                return false;
        } elseif($who == NULL) { // Target is private chat by defult.
			if(is_array($message)) {
			  	foreach($message as $key => $value)
			  		AOChat::send_privgroup($this->vars["name"],$this->settings["default priv color"].$value);
			  	
			  	if(($this->settings["guest_relay"] == 1 || (isset($this->vars[guestchannel_enabled]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2)) && $this->settings["guest_relay_commands"] == 1)
					foreach($message as $key => $value)
			  			AOChat::send_group($this->vars["my guild"], "</font>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default priv color"]}$value</font>");
			} else {
				AOChat::send_privgroup($this->vars["name"],$this->settings["default priv color"].$message);
				if(($this->settings["guest_relay"] == 1 || (isset($this->vars["guestchannel_enabled"]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2)) && $this->settings["guest_relay_commands"] == 1 && $disable_relay === false)
		  			AOChat::send_group($this->vars["my guild"], "</font>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default priv color"]}$message</font>");
			}
		} elseif($who == $this->vars["my guild"] || $who == 'guild') {// Target is guild chat.
    		if(is_array($message)) {
			  	foreach($message as $key => $value)
			  		AOChat::send_group($this->vars["my guild"],$this->settings["default guild color"].$value);

  			  	if(($this->settings["guest_relay"] == 1 || (isset($this->vars[guestchannel_enabled]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2)) && $this->settings["guest_relay_commands"] == 1)
					foreach($message as $key => $value)
			  			AOChat::send_privgroup($this->vars["name"], "</font>{$this->settings["guest_color_channel"]}[{$this->vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default guild color"]}$value</font>");		  
			} else {
				AOChat::send_group($this->vars["my guild"],$this->settings["default guild color"].$message);
				if(($this->settings["guest_relay"] == 1 || (isset($this->vars["guestchannel_enabled"]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2)) && $this->settings["guest_relay_commands"] == 1 && $disable_relay === false)
		  			AOChat::send_privgroup($this->vars["name"], "</font>{$this->settings["guest_color_channel"]}[{$this->vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default guild color"]}$message</font>");		  
			}
		} elseif(AOChat::get_uid($who) != NULL) {// Target is a player.
    		if(is_array($message)) {
			  	foreach($message as $key => $value) {
			  		AOChat::send_tell($who,$this->settings["default tell color"].$value);

					// Echo	
					if($this->settings['echo'] >= 1) newLine("Out. Msg.", $who, $value, $this->settings['echo']);
			  	}
			} else {
				AOChat::send_tell($who,$this->settings["default tell color"].$message);

				// Echo	
				if($this->settings['echo'] >= 1) newLine("Out. Msg.", $who, $message, $this->settings['echo']);

			}
		} else { // Public channels that are not myguild.
	    	if(is_array($message)) {
			  	foreach($message as $key => $value)
			  		AOChat::send_group($who,$this->settings["default guild color"].$value);			  
			} else
				AOChat::send_group($who,$this->settings["default guild color"].$message);
		}
	}
	
	/*===============================
** Name: reply
** Send chat messages back to aochat servers thru aochat.
*/	function reply($type, $sender, $message){
		if($type == "msg")
			bot::send($message, $sender);
		elseif($type == "priv")
			bot::send($message);
		elseif($type == "guild")
			bot::send($message, "guild");
	}

/*===============================
** Name: loadModules
** Load all Modules
*/	function loadModules(){
		global $db;
		global $curMod;
		if($d = dir("./modules")){
			while (false !== ($entry = $d->read())){
				if(!is_dir("$entry")){
					// Look for the plugin's ... setup file				
					if(file_exists("./modules/$entry/$entry.php")){
						$curMod = $entry;
						if($this->settings['debug'] > 0) print("MODULE_NAME:($entry.php)\n");						
						include "./modules/$entry/$entry.php";
					}
					else // else add entry as a single file.
						include "./modules/$entry";
				}
			}
			$d->close();	
		}	
	}

/*===============================
** Name: loadCommands
**  Load the Commands that are set as active
*/	function loadCommands() {
	  	global $db;
		//Delete commands that are not verified
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'cmd'");
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'cmd'");
		$data = $db->fObject("all");
		foreach($data as $row)
			bot::regcommand($row->type, $row->file, $row->cmd, $row->admin);
	}

/*===============================
** Name: loadSubcommands
**  Load the Commands that are set as active
*/	function loadSubcommands() {
	  	global $db;
		//Delete subcommands that are not verified
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'subcmd'");
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		$data = $db->fObject("all");
		foreach($data as $row) {
			$this->subcommands[$row->file][$row->type]["cmd"] = $row->cmd;
			$this->subcommands[$row->file][$row->type]["admin"] = $row->admin;
		}
	}

/*===============================
** Name: loadEvents
**  Load the Events that are set as active
*/	function loadEvents() {
	  	global $db;
		//Delete events that are not verified
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'event'");
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'event' AND `dependson` = 'none'");
		$data = $db->fObject("all");
		foreach($data as $row)
			bot::regevent($row->type, $row->file);
	}

/*===============================
** Name: Command
** 	Register a command
*/	function command($type, $filename, $command, $admin = 'all', $description = 'none'){
		global $db;

		$type = bot::processCommandType($type);	
		$command = strtolower($command);
		$module = explode("/", strtolower($filename));
	  	
		forEach ($type as $typeSingle) {
			if($this->settings['debug'] > 1) print("Adding Command to list:($command) File:($filename)\n");
			if($this->settings['debug'] > 1) print("                 Admin:($admin) Type:($typeSingle)\n");
			if($this->settings['debug'] > 2) sleep(1);
			
			if($this->existing_commands[$typeSingle][$command] == true)
				$db->query("UPDATE cmdcfg_<myname> SET `module` = '$module[0]', `verify` = 1, `file` = '$filename', `description` = '$description' WHERE `cmd` = '$command' AND `type` = '$typeSingle'");
			else
				$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`) VALUES ('$module[0]', '$typeSingle', '$filename', '$command', '$admin', '$description', 1, 'cmd', '".$this->settings["default module status"]."')");
		}
	}

/*===============================
** Name: regcommand
**  Sets an command as active
*/	function regcommand($type, $filename, $command, $admin = 'all') {
		global $db;
		
	  	if($this->settings['debug'] > 1) print("Activate Command:($command) Admin Type:($admin)\n");
		if($this->settings['debug'] > 1) print("            File:($filename) Type:($type)\n");
		if($this->settings['debug'] > 2) sleep(1);

		$module = explode("/", strtolower($filename));
		$module = $module[0];

		//Check if the file exists		
		if(($actual_filename = bot::verifyFilename($filename)) != '') { 
    		$filename = $actual_filename; 
		} else { 
			echo "Error in registering the File $filename for command $command. The file doesn´t exists!\n";
			return;
		}
							
		if($command != NULL) // Change commands to lower case.
			$command = strtolower($command);
		
		$admin = strtolower($admin);
		
		//Check if the admin status exists
		if(!is_numeric($admin)) {
			if($admin == "leader")
				$admin = 1;
			elseif($admin == "raidleader" || $admin == "rl")
				$admin = 2;
			elseif($admin == "mod" || $admin == "moderator")
				$admin = 3;
			elseif($admin == "admin")
				$admin = 4;
			elseif($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
				echo "Error in registrating the command $command for channel $type. Reason Unknown Admintype: $admin. Admintype is set to all now.\n";
				$admin = "all";
			}
		}		
				
		switch ($type){
			case "msg":
				if($this->tellCmds[$command]["filename"] == ""){
					$this->tellCmds[$command]["filename"] = $filename;
					$this->tellCmds[$command]["admin level"] = $admin;
				}		
			break;
			case "priv":
				if($this->privCmds[$command]["filename"] == ""){
					$this->privCmds[$command]["filename"] = $filename;
					$this->privCmds[$command]["admin level"] = $admin;
				}			
			break;
			case "guild":
				if($this->guildCmds[$command]["filename"] == ""){
					$this->guildCmds[$command]["filename"] = $filename;
					$this->guildCmds[$command]["admin level"] = $admin;
				}
			break;
		}
		
		//Activate Events that are needed for this command
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$command' AND `cmdevent` = 'event' AND `type` != 'setup'");	
		$data = $db->fObject("all");
  		foreach($data as $row)
  		  	bot::regevent($row->type, $row->file);

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$module' AND `cmdevent` = 'event' AND `type` = 'setup'");
		if($db->numrows() != 0) {
			$data = $db->fObject("all");
	  		foreach($data as $row) {
			  	if($row->status == 0) {
				    bot::regevent($row->type, $row->file);
				    $db->query("UPDATE cmdcfg_<myname> SET `status` = 1 WHERE `module` = '$module' AND `cmdevent` = 'event' AND `type` = 'setup'");
				}
			}
		}
	}

/*===============================
** Name: unregcommand
** 	Deactivates an command
*/	function unregcommand($type, $filename, $command) {
  		global $db;
		$command = strtolower($command);

	  	if($this->settings['debug'] > 1) print("Deactivate Command:($command) File:($filename)\n");
		if($this->settings['debug'] > 1) print("              Type:($type)\n");
		if($this->settings['debug'] > 2) sleep(1);

		switch ($type){
			case "msg":
				unset($this->tellCmds[$command]);
			break;
			case "priv":
				unset($this->privCmds[$command]);
			break;
			case "guild":
				unset($this->guildCmds[$command]);
			break;
		}
		
		//Deactivate Events that are asssigned to this command
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `dependson` = '$command' AND `cmdevent` = 'event' AND `type` != 'setup'");
  		while($row = $db->fObject())
  		  	bot::unregevent($row->type, $row->file);
	}
	
/*===============================
** Name: processCommandType
** 	Returns a command type in the proper format
*/	function processCommandType(&$type) {
		if ($type == "all") {
			return array("msg", "priv", "guild");
		} else if (!is_array($type)) {
			return array($type);
		} else {
			return $type;
		}
	}

/*===============================
** Name: Subcommand
** 	Register a subcommand
*/	function subcommand($type, $filename, $command, $admin = 'all', $dependson, $description = 'none'){
		global $db;
		$command = strtolower($command);
		$module = explode("/", strtolower($filename));
		
		$type = bot::processCommandType($type);	  	
		forEach ($type as $typeSingle) {
			if($this->settings['debug'] > 1) print("Adding Subcommand to list:($command) File:($filename)\n");
			if($this->settings['debug'] > 1) print("                    Admin:($admin) Type:($typeSingle)\n");
			if($this->settings['debug'] > 2) sleep(1);

			//Check if the file exists		
			if(($actual_filename = bot::verifyFilename($filename)) != '') { 
				$filename = $actual_filename; 
			} else { 
				echo "Error in registering the File $filename for Subcommand $command. The file doesn´t exists!\n";
				return;
			}
							
			if($command != NULL) // Change commands to lower case.
				$command = strtolower($command);
			
			//Check if the admin status exists
			if(!is_numeric($admin)) {
				if($admin == "leader")
					$admin = 1;
				elseif($admin == "raidleader" || $admin == "rl")
					$admin = 2;
				elseif($admin == "mod" || $admin == "moderator")
					$admin = 3;
				elseif($admin == "admin")
					$admin = 4;
				elseif($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
					echo "Error in registrating the command $command for channel $typeSingle. Reason Unknown Admintype: $admin. Admintype is set to all now.\n";
					$admin = "all";
				}
			}
			
			if($this->existing_subcmds[$typeSingle][$command] == true)
				$db->query("UPDATE cmdcfg_<myname> SET `module` = '$module[0]', `verify` = 1, `file` = '$filename', `description` = '$description', `dependson` = '$dependson' WHERE `cmd` = '$command' AND `type` = '$typeSingle'");
			else
				$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`) VALUES ('$module[0]', '$typeSingle', '$filename', '$command', '$admin', '$description', 1, 'subcmd', '$dependson')");
		}
	}

/*===============================
** Name: event
**  Registers an event
*/	function event($type, $filename, $dependson = 'none', $desc = 'none'){
		global $db;
		
		$module = explode("/", strtolower($filename));

	  	if($this->settings['debug'] > 1) print("Adding Event to list:($type) File:($filename)\n");
		if($this->settings['debug'] > 2) sleep(1);

		if($dependson == "none" && $this->settings["default module status"] == 1)
			$status = 1;
		else
			$status = 0;
			
		if($this->existing_events[$type][$filename] == true)
		  	$db->query("UPDATE cmdcfg_<myname> SET `dependson` = '$dependson', `verify` = 1, `description` = '$desc' WHERE `type` = '$type' AND `cmdevent` = 'event' AND `file` = '$filename' AND `module` = '$module[0]'");
		else
		  	$db->query("INSERT INTO cmdcfg_<myname> (`module`, `cmdevent`, `type`, `file`, `verify`, `dependson`, `description`, `status`) VALUES ('$module[0]', 'event', '$type', '$filename', '1', '$dependson', '$desc', '$status')");
	}


		
/*===============================
** Name: regevent
**  Sets an event as active
*/	function regevent($type, $filename){
		global $db;
	  	if($this->settings['debug'] > 1) print("Activating Event:($type) File:($filename)\n");
		if($this->settings['debug'] > 2) sleep(1);

		$module = explode("/", strtolower($filename));
		$module = $module[0];
		
		//Check if the file exists		
		if ($type == 'loadSQLFile') {
			// do nothing
		} else if(($actual_filename = bot::verifyFilename($filename)) != '') { 
    		$filename = $actual_filename; 
		} else { 
			echo "Error in registering the File $filename for Eventtype $type. The file doesn't exists!\n";
			return;
		}
		
		if($type != "setup") {
			$db->query("SELECT * FROM cmdcfg_<myname> WHERE `module` = '$module' AND `cmdevent` = 'event' AND `type` = 'setup'");
			if($db->numrows() != 0) { 	
				$data = $db->fObject("all");
		  		foreach($data as $row) {
				  	if($row->status == 0) {
						if (file_exists("./modules/$row->file")) 
							$file = "./modules/$row->file";		
						
						if (file_exists("./core/$row->file")) 
							$file = "./core/$row->file";			  	  
				  	  	include($file);
					    $db->query("UPDATE cmdcfg_<myname> SET `status` = 1 WHERE `module` = '$module' AND `cmdevent` = 'event' AND `type` = 'setup'");
					}
				}
			}
		}
		switch ($type){
			case "towers":
				if(!in_array($filename, $this->towers))
					$this->towers[] = $filename;
			break;
			case "orgmsg":
				if(!in_array($filename, $this->orgmsg))
					$this->orgmsg[] = $filename;
			break;
			case "msg":
				if(!in_array($filename, $this->privMsgs))
					$this->privMsgs[] = $filename;		
			break;
			case "priv":	
				if(!in_array($filename, $this->privChat))
					$this->privChat[] = $filename;				
			break;
			case "guild":	
				if(!in_array($filename, $this->guildChat))
					$this->guildChat[] = $filename;								
			break;
			case "joinPriv":	
				if(!in_array($filename, $this->joinPriv))		
					$this->joinPriv[] = $filename;
			break;
			case "leavePriv":	
				if(!in_array($filename, leavePriv))	
					$this->leavePriv[] = $filename;					
			break;
			case "logOn":	
				if(!in_array($filename, $this->logOn))	
					$this->logOn[] = $filename;
			break;
			case "logOff":	
				if(!in_array($filename, $this->logOff))
					$this->logOff[] = $filename;	
			break;
			case "2sec":	
				if(!in_array($filename, $this->_2sec))
					$this->_2sec[] = $filename;
			break;
			case "1min":	
				if(!in_array($filename, $this->_1min)) 	
					$this->_1min[] = $filename;	
			break;
			case "15min":	
				if(!in_array($filename, $this->_15min))	
					$this->_15min[] = $filename;	
			break;
			case "1hour":	
				if(!in_array($filename, $this->_1hour))
					$this->_1hour[] = $filename;
			break;
			case "24hrs":	
				if(!in_array($filename, $this->_24hrs))
					$this->_24hrs[] = $filename;
			break; 
			case "connect":	
				if(!in_array($filename, $this->_connect))
					$this->_connect[] = $filename;
			break;			
			case "setup":
				include $filename;
			break;
		}
	}

/*===============================
** Name: unregevent
**  Disables an event
*/	function unregevent($type, $filename) {
		if($this->settings['debug'] > 1) print("Deactivating Event:($type) File:($filename)\n");
		if($this->settings['debug'] > 2) sleep(1);

		//Check if the file exists		
		if(($actual_filename = bot::verifyFilename($filename)) != '') { 
    		$filename = $actual_filename; 
		} else { 
			echo "Error in unregistering the File $filename for Event $type. The file doesn´t exists!\n";
			return;
		}

		switch ($type){
			case "towers":
				if(in_array($filename, $this->towers)) {
					$temp = array_flip($this->towers);
					unset($this->towers[$temp[$filename]]);
				}
			break;
			case "orgmsg":
				if(in_array($filename, $this->orgmsg)) {
					$temp = array_flip($this->orgmsg);
					unset($this->orgmsg[$temp[$filename]]);
				}
			break;
			case "msg":
				if(in_array($filename, $this->privMsgs)) {
					$temp = array_flip($this->privMsgs);
					unset($this->privMsgs[$temp[$filename]]);
				}
			break;
			case "priv":	
				if(in_array($filename, $this->privChat)) {
					$temp = array_flip($this->privChat);
					unset($this->privChat[$temp[$filename]]);
				}
			break;
			case "guild":	
				if(in_array($filename, $this->guildChat)) {
					$temp = array_flip($this->guildChat);
					unset($this->guildChat[$temp[$filename]]);
				}
			break;
			case "joinPriv":	
				if(in_array($filename, $this->joinPriv)) {
					$temp = array_flip($this->joinPriv);
					unset($this->joinPriv[$temp[$filename]]);
				}
			break;
			case "leavePriv":	
				if(in_array($filename, $this->leavePriv)) {
					$temp = array_flip($this->leavePriv);
					unset($this->leavePriv[$temp[$filename]]);
				}
			break;
			case "logOn":
				if(in_array($filename, $this->logOn)) {
					$temp = array_flip($this->logOn);
					unset($this->logOn[$temp[$filename]]);
				}
			break;
			case "logOff":	
				if(in_array($filename, $this->logOff)) {
					$temp = array_flip($this->logOff);
					unset($this->logOff[$temp[$filename]]);
				}	
			break;
			case "2sec":	
				if(in_array($filename, $this->_2sec)) {
					$temp = array_flip($this->_2sec);
					unset($this->_2sec[$temp[$filename]]);
				}				
			break;
			case "1min":	
				if(in_array($filename, $this->_1min)) {
					$temp = array_flip($this->_1min);
					unset($this->_1min[$temp[$filename]]);
				}
			break;
			case "15min":	
				if(in_array($filename, $this->_15min)) {
					$temp = array_flip($this->_15min);
					unset($this->_15min[$temp[$filename]]);
				}	
			break;
			case "1hour":	
				if(in_array($filename, $this->_1hour)) {
					$temp = array_flip($this->_1hour);
					unset($this->_1hour[$temp[$filename]]);
				}		
			break;
			case "24hrs":	
				if(in_array($filename, $this->_24hrs)) {
					$temp = array_flip($this->_24hrs);
					unset($this->_24hrs[$temp[$filename]]);
				}
			break;
			case "connect":	
				if(in_array($filename, $this->_connect)) {
					$temp = array_flip($this->_connect);
					unset($this->_connect[$temp[$filename]]);
				}
			break;			 
		}
	}

/*===============================
** Name: reggroup
**  Register a group of commands
*/	function regGroup($group, $module = 'none', $desc = 'none'){
		global $db;
		$module = strtolower($module);
		$group = strtolower($group);
		//Check if the module is correct
		if($module == "none") {
			echo "Error in creating group $group. You need to specify a module for the group.\n";
			sleep(5);
			return;	  	
		}
		//Check if the group already exists
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$group'");
		if($db->numrows() != 0) {
			echo "Error in creating group $group. This group already exists.\n";
			sleep(5);
			return;
		}
    	$numargs = func_num_args();
    	$arg_list = func_get_args();
		//Check if enough commands are given for the group
		if($numargs < 5) {
			echo "Not enough commands to build group $group(must be at least 2commands)";
  			sleep(5);
			return;
		}
		//Go through the arg list and assign it to the group
		for($i = 3;$i < $numargs; $i++) {
		  	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '".$arg_list[$i]."' AND `module` = '$module'");
		  	if($db->numrows() != 0)
			    $db->query("UPDATE cmdcfg_<myname> SET `grp` = '".$group."' WHERE `cmd` = '".$arg_list[$i]."' AND `module` = '$module'");
			else {
			  	echo "Error in creating group $group for module $module. Command ".$arg_list[$i]." doesn´t exists.\n";
  				sleep(5);
			}	  
		}
	  	$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `cmdevent`, `verify`, `description`) VALUES ('none', '$group', 'group', '1', '$desc')");
	}


/*===============================
** Name: addsetting
** Adds a setting to the list
*/	function addsetting($name = 'none', $description = 'none', $mode = 'hide', $setting = 'none', $options = 'none', $intoptions = '0', $admin = 'mod', $help = '') {
		global $db;
		global $curMod;
		$name = strtolower($name);

		//Check if the file exists		
		if(($actual_filename = bot::verifyFilename($help)) != '' && $help != "") { 
    		$filename = $actual_filename; 
		} elseif($help != "") { 
			echo "Error in registering the File $filename for Setting $name. The file doesn´t exists!\n";
			return;
		}
		
		if($this->existing_settings[$name] != true) {
			$db->query("INSERT INTO settings_<myname> (`name`, `mod`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('$name', '$curMod', '$mode', '$setting', '$options', '$intoptions', '$description', 'db', '$admin', '$help')");
		  	$this->settings[$name] = $setting;
	  	} else {
			$db->query("UPDATE settings_<myname> SET `mode` = '$mode', `options` = '$options', `intoptions` = '$intoptions', `description` = '$description', `admin` = '$admin', `help` = '$help' WHERE `name` = '$name'");
		}
	}

/*===============================
** Name: getsetting
** Gets an loaded setting
*/	function getsetting($name = "none") {
		$name = strtolower($name);
		if(isset($this->settings[$name]))
	  		return $this->settings[$name];
	  	else
	  		return false;
	}

/*===============================
** Name: savesetting
** Saves a setting to the db
*/	function savesetting($name = "none", $newsetting = "none") {
		global $db;
		$name = strtolower($name);
		if($newsetting == "none")
			return false;

		if(isset($this->settings[$name])) {
			$db->query("UPDATE settings_<myname> SET `setting` = '$newsetting' WHERE `name` = '$name'");
			$this->settings[$name] = $newsetting;
		} else
			return false;
	}


/*===============================
** Name: help
** Add a help command and display text file in a link. 
*/	function help($command, $filename, $admin = 'all', $info = "", $cat = "Unknown Category"){		
	  	global $db;
		if($this->settings['debug'] > 1) print("Registering Helpfile:($filename) Cmd:($command)\n");
		if($this->settings['debug'] > 2) sleep(1);

		$command = strtolower($command);

		//Check if the admin status exists
		if(!is_numeric($admin)) {
			if($admin == "leader")
				$admin = 1;
			elseif($admin == "raidleader" || $admin == "rl")
				$admin = 2;
			elseif($admin == "mod" || $admin == "moderator")
				$admin = 3;
			elseif($admin == "admin")
				$admin = 4;
			elseif($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
				echo "Error in registrating the command $command for channel $type. Reason Unknown Admintype: $admin. Admintype is set to all now.\n";
				$admin = "all";
			}
		}

		$module = explode("/", $filename);

		//Check if the file exists		
		if(($actual_filename = bot::verifyFilename($filename)) != '') { 
    		$filename = $actual_filename;
    		if(substr($filename, 0, 7) == "./core/")
	    		$this->helpfiles[$cat][$command]["status"] = "enabled";
		} else { 
			echo "Error in registering the File $filename for Helpcommand $command. The file doesn´t exists!\n";
			return;
		}
					
		if(isset($this->existing_helps[$command]))
			$db->query("UPDATE hlpcfg_<myname> SET `verify` = 1, `description` = '$info', `cat` = '$cat' WHERE `name` = '$command'");		
		else
			$db->query("INSERT INTO hlpcfg_<myname> VALUES ('$command', '$module[0]', '$cat', '$info', '$admin', 1)");

		$db->query("SELECT * FROM hlpcfg_<myname> WHERE `name` = '$command'");
		$row = $db->fObject();
		$this->helpfiles[$cat][$command]["filename"] = $filename;
		$this->helpfiles[$cat][$command]["admin level"] = $row->admin;
		$this->helpfiles[$cat][$command]["info"] = $info;
		$this->helpfiles[$cat][$command]["module"] = $module[0];
	}


/*===========================================================================================
** Name: processCallback
** Proccess all incoming messages that bot recives
*/	function processCallback($type, $args){
		global $db;
		
		switch ($type){
			case AOCP_GROUP_ANNOUNCE: // 60
				$b = unpack("C*", $args[0]);
				if ($b[1]==3)
					$this->vars["my guild id"] = $b[2]*256*256*256 + $b[3]*256*256 + $b[4]*256 + $b[5];
			break;			
			case AOCP_PRIVGRP_CLIJOIN: // 55, Incoming player joined private chat
				$type = "joinPriv"; // Set message type.
				$sender	= AOChat::get_uname($args[1]);// Get Name
				// Add sender to the chatlist.
				$this->chatlist[$sender] = true;						
				// Echo
				if($this->settings['echo'] >= 1) newLine("Priv Group", $sender, "joined the channel.", $this->settings['echo']);
				
				// Remove sender if they are /ignored or /banned or They gone above spam filter
                if($this->settings["Ignore"][$sender] == true || $this->banlist["$sender"]["name"] == "$sender" || $this->spam[$sender] > 100){
					AOChat::privategroup_kick($sender);
					return;
				}						
				// Check files, for all 'player joined channel events'.
				if($this->joinPriv != NULL)
					foreach($this->joinPriv as $filename)
						include $filename;
				// Kick if there access is restricted.	
				if($restricted === true)
					AOChat::privategroup_kick($sender);
			break;	
			case AOCP_PRIVGRP_CLIPART: // 56, Incoming player left private chat
				$type = "leavePriv"; // Set message type.
				$sender	= AOChat::get_uname($args[1]); // Get Name
				// Echo
				if($this->settings['echo'] >= 1) newLine("Priv Group", $sender, "left the channel.", $this->settings['echo']);

				// Remove from Chatlist array.
				unset($this->chatlist[$sender]);	
				// Remove sender if they are /ignored or /banned or They gone above spam filter				
				if($this->settings["Ignore"][$sender] == true || $this->banlist["$sender"]["name"] == "$sender" || $this->spam[$sender] > 100)
					return;
				// Check files, for all 'player left channel events'.
				foreach($this->leavePriv as $filename)
					include $filename;	
			break;					
			case AOCP_BUDDY_ADD: // 40, Incoming buddy logon or off
				// Basic packet data
				$sender	= AOChat::get_uname($args[0]);
				$status	= 0 + $args[1];
				
				//Ignore Logon/Logoff from other bots or phantom logon/offs
                if($this->settings["Ignore"][$sender] == true || $sender == "")
					return;
				// Update buddylist array
				$this->buddyList[$sender] = $status;
				// If Status == 0(logoff) if Status == 1(logon)
				if($status == 0){
					$type = "logOff"; // Set message type
					// Echo 
					//if($this->settings['echo'] == 1) print("$sender logged off.\n");
					// Check files, for all 'player logged off events'
					if($this->logOff != NULL)
						foreach($this->logOff as $filename) {
							$msg = "";
							include $filename;
						}
				}
				if($status == 1){
					$type = "logOn"; // Set Message Type
					// Echo 
					if($this->settings['echo'] >= 1) newLine("Buddy", $sender, "logged on", $this->settings['echo']);

					// Check files, for all 'player logged on events'.					
					if($this->logOn != NULL)
						foreach($this->logOn as $filename) {
						  	$msg = "";
						  	include $filename;
						} 				  
				}
			break;			
			case AOCP_MSG_PRIVATE: // 30, Incoming Msg
				$type = "msg"; // Set message type.
				$sender	= AOChat::get_uname($args[0]);				
	
                if($this->settings["Ignore"][$sender] == true || $this->banlist["$sender"]["name"] == "$sender" || ($this->spam[$sender] > 100 && $this->vars['spam protection'] == 1)){
					$this->spam[$sender] += 20;
					return;	
				}
				// Removing tell color 
				if(eregi("^<font color='#([0-9a-f]+)'>(.+)$", $args[1], $arr))
					$message = $arr[2];
				else
					$message = $args[1];
									
				// AFk check
				if(eregi("^$sender is afk (.+)$", $message, $arr))				
					return;				
				elseif(eregi("^I am away from my keyboard right now, (.*)your message has been logged.$", $message))
					return;
				
				//Remove the prefix infront if there is one
				if($message[0] == $this->settings["symbol"] && strlen($message) > 1)
					$message = substr($message, 1);
					
				// Echo	
				if($this->settings['echo'] >= 1) newLine("Inc. Msg.", $sender, $message, $this->settings['echo']);

				// Check privatejoin and tell Limits
				if(file_exists("./core/PRIV_TELL_LIMIT/check.php"))
					include("./core/PRIV_TELL_LIMIT/check.php");
					
				// Events
				if($this->privMsgs != NULL)
					foreach($this->privMsgs as $file) {
						$msg = "";
						include $file;
					}
				
				// Admin Code		
				if($restricted != true){
					// Break down in to words.
					$words	= split(' ', strtolower($message));
					$admin 	= $this->tellCmds[$words[0]]["admin level"];
					$filename = $this->tellCmds[$words[0]]["filename"];	

				  	//Check if a subcommands for this exists			
				  	if($this->subcommands[$filename][$type])
					    if(eregi("^{$this->subcommands[$filename][$type]["cmd"]}$", $message))
							$admin = $this->subcommands[$filename][$type]["admin"];

					// Admin Check	
					if(is_numeric($admin)){
						if($this->admins[$sender]["level"] >= $admin && $this->admins[$sender]["level"] != "")
							$restricted = false;
						elseif($this->admins[$sender]["level"] == "" && $this->vars["leader"] == $sender && $admin == 1)
							$restricted = false;
						else
							$restricted = true;						
					}
					elseif($admin == "guild"){			
						if(isset($this->guildmembers[$sender]))
							$restricted = false;
						else
							$restricted = true;
					}
					elseif($admin == "guildadmin"){			
						if($this->guildmembers[$sender] <= $this->settings['guild admin level'])
							$restricted = false;
						else
							$restricted = true;
					}
					else
						$restricted = false;
				}
				// Upload Command File or return error message			
				if($restricted == true || $filename == ""){
					$this->send("Unknown command or Access denied! for more info try /tell <myname> help", $sender);
					$this->spam[$sender] = $this->spam[$sender] + 20;
					return;		
				}
				else{
 				    $syntax_error = false;
 				    $msg = "";
					include $filename;
					if($syntax_error == true)
						bot::send("Syntax error! for more info try /tell <myname> help", $sender);
					$this->spam[$sender] = $this->spam[$sender] + 10; 
				}						
			break;			
			case AOCP_PRIVGRP_MESSAGE: // 57, Incoming priv message
				$type = "priv";
				$sender	= AOChat::get_uname($args[1]);
				$channel = $this->vars["name"];
				$message = $args[2];
				$restricted = false;
				if($sender == $this->vars["name"]) {
					if($this->settings['echo'] >= 1) newLine("Priv Group", $sender, $message, $this->settings['echo']);
					return;
				}
				if($this->banlist["$sender"]["name"] == "$sender")
					return;
				
				if($this->vars['spam protection'] == 1) {
					if($this->spam[$sender] == 40) $this->send("Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
					if($this->spam[$sender] > 60) AOChat::privategroup_kick($sender);																	
					if(strlen($args[1]) > 400){
						$this->largespam[$sender] = $this->largespam[$sender] + 1;
						if($this->largespam[$sender] > 1) AOChat::privategroup_kick($sender);						
						if($this->largespam[$sender] > 0) $this->send("Your client is sending a large chat messages. Stop or be kicked.", $sender);
					}				  
				}
				
				// Echo
				if($this->settings['echo'] >= 1) newLine("Priv Group", $sender, $message, $this->settings['echo']);
			
				if($this->privChat != NULL)
					foreach($this->privChat as $file) {
					  	$msg = "";
						include $file; 	
					}	
				
				$msg = "";
				if(!$restriced && (($message[0] == $this->settings["symbol"] && strlen($message) >= 2) || eregi("^(afk|brb)", $message, $arr))) {
					if($message[0] == $this->settings["symbol"]) {
						$message 	= substr($message, 1);
					}
					$words		= split(' ', strtolower($message));
					$admin 		= $this->privCmds[$words[0]]["admin level"];
					$filename 	= $this->privCmds[$words[0]]["filename"];

				  	//Check if a subcommands for this exists			
				  	if($this->subcommands[$filename][$type])
					    if(eregi("^{$this->subcommands[$filename][$type]["cmd"]}$", $message))
							$admin = $this->subcommands[$filename][$type]["admin"];

						
					if(is_numeric($admin)){		
						if($this->admins[$sender]["level"] >= $admin && $this->admins[$sender]["level"] != "")
							if($filename != "")
								include $filename;

						if($this->admins[$sender]["level"] == "" && $this->vars["leader"] == $sender && $admin == 1)
							if($filename != "")
								include $filename;									
					}
					elseif($admin == "guild"){			
						if(isset($this->guildmembers[$sender]))
							if($filename != "")
								include $filename;
					}
					elseif($admin == "guildadmin"){			
						if($this->guildmembers[$sender] <= $this->settings['guild admin level'])
							if($filename != "")
								include $filename;
					}
					elseif($admin == "all")
						if($filename != "")
							include $filename;
				}
				else
					$this->spam[$sender] = $this->spam[$sender] + 10;
			break;			
			case AOCP_GROUP_MESSAGE: // 65, Public and guild channels
				$syntax_error = false;
				$sender	 = AOChat::get_uname($args[1]);
				$message = $args[2];			
				$channel = AOChat::get_gname($args[0]);

				if($sender) {
					//Ignore Message that are send from the bot self
					if($sender == $this->vars["name"]) {
						if($this->settings['echo'] >= 1) newLine($channel, $sender, $message, $this->settings['echo']);
						return;
					}
					
					//Ignore tells from other bots
	                if($this->settings["Ignore"][$sender] == true)
						return;
						
					if($this->banlist["$sender"]["name"] == "$sender")
						return;
				}
			
				//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...				
				if($channel == "" || $channel == 'IRRK News Wire' || $channel == 'OT OOC' || $channel == 'OT Newbie OOC' || $channel == 'OT Jpn OOC' || $channel == 'OT shopping 11-50' || $channel == 'Tour Announcements' || $channel == 'Neu. Newbie OOC' || $channel == 'Neu. shopping 11-50' || $channel == 'Neu. OOC' || $channel == 'Clan OOC' || $channel == 'Clan Newbie OOC' || $channel == 'Clan shopping 11-50')
					return;
				
				if($this->settings['echo'] >= 1) newLine($channel, $sender, $message, $this->settings['echo']);
				
				if($channel == "All Towers" || $channel == "Tower Battle Outcome"){
                    $type = "towers";
    				if($this->towers != NULL)
    					foreach($this->towers as $file) {
    						$msg = "";
							include $file;
    					}
                    return;
                } elseif($channel == "Org Msg"){
                    $type = "orgmsg";
    				if($this->orgmsg != NULL)
    					foreach($this->orgmsg as $file) {
    						$msg = "";
							include $file;
    					}
                    return;
                } elseif($channel == $this->vars["my guild"]){
                    $type = "guild"; 		
					
					if($this->guildChat != NULL)
    					foreach($this->guildChat as $file) {
							$msg = "";
							include $file;
						}
					
					$msg = "";
					if(!$restriced && (($message[0] == $this->settings["symbol"] && strlen($message) >= 2) || eregi("^(afk|brb)", $message, $arr))) {
						if($message[0] == $this->settings["symbol"]) {
							$message 	= substr($message, 1);
						}
    					$words		= split(' ', strtolower($message));
						$admin 		= $this->guildCmds[$words[0]]["admin level"];
						$filename 	= $this->guildCmds[$words[0]]["filename"];
						
					  	//Check if a subcommands for this exists			
					  	if($this->subcommands[$filename][$type])
						    if(eregi("^{$this->subcommands[$filename][$type]["cmd"]}$", $message))
								$admin = $this->subcommands[$filename][$type]["admin"];

						
						// Admin Check	
						if(is_numeric($admin)){						
							if($this->admins[$sender]["level"] >= $admin && $this->admins[$sender]["level"] != "")
								if($filename != "")
									include $filename;											
						}
						elseif($admin == "guild"){			
							if(isset($this->guildmembers[$sender]))
								if($filename != "")
									include $filename;
						}
						elseif($admin == "guildadmin"){			
							if($this->guildmembers[$sender] <= $this->settings['guild admin level'])	
								if($filename != "")
									include $filename;
						}
						elseif($admin == "all")
							if($filename != "")
								include $filename;
						
						//Shows syntax errors to the user
						if($syntax_error == true)
							bot::send("Syntax error! for more info try /tell <myname> help", "guild");
					}
				}           		
			break;
		}
	}

/*===============================
** Name: crons()
** Call php-Scripts at certin time intervals. 2 sec, 1 min, 15 min, 1 hour, 24 hours
*/	function crons(){
		global $db;	
		switch($this->vars){
			case $this->vars["2sec"] < time();
				$this->vars["2sec"] 	= time() + 2;
				foreach($this->spam as $key => $value){
					if($value > 0)
						$this->spam[$key] = $value - 10;
					else
						$this->spam[$key] = 0;		
				}
				if($this->_2sec != NULL)
					foreach($this->_2sec as $filename)
						include $filename;
				break;
			case $this->vars["1min"] < time();
				foreach($this->largespam as $key => $value){
					if($value > 0)
						$this->largespam[$key] = $value - 1;
					else
						$this->largespam[$key] = 0;		
				}
				$this->vars["1min"] 	= time() + 60;
				if($this->_1min != NULL)
					foreach($this->_1min as $filename)
						include $filename;
												
				break;
			case $this->vars["15min"] < time();
				$this->vars["15min"] 	= time() + (60 * 15);
				if($this->_15min != NULL)
					foreach($this->_15min as $filename)
						include $filename;
											
				break;
			case $this->vars["1hour"] < time();				
				$this->vars["1hour"] 	= time() + (60 * 60);
				if($this->_1hour != NULL)
					foreach($this->_1hour as $filename)
						include $filename;
									
				break;
			case $this->vars["24hours"] < time();
				$this->vars["24hours"] 	= time() + ((60 * 60) * 24);
				if($this->_24hrs != NULL)
					foreach($this->_24hrs as $filename)
						include $filename;
										
				break;
		}		
	}
	
	function verifyFilename($filename) {
		//Replace all \ characters with /
		$filename = str_replace("\\", "/", $filename);
		
		if(!bot::verifyNameConvention($filename))
			return "";
		
		//check if the file exists
	    if(file_exists("./core/$filename")) { 
	        return "./core/$filename"; 
    	} else if(file_exists("./modules/$filename")) {
        	return "./modules/$filename"; 
	    } else { 
	     	return "";
	    }
	}
	
	function verifyNameConvention($filename) {
		eregi("^(.+)/([0-9a-z_]+).php$", $filename, $arr);
		if($arr[2] == strtolower($arr[2])) {
			return true;
		} else {
			echo "Warning: $filename does not match the nameconvention(All php files needs to be in lowercases except loading files)!\n";
			sleep(2);
			return false;
		}
	}
	
	/*===============================
** Name: loadSQLFile
** Loads an sql file if there is an update
** Will load the sql file with name $namexx.xx.xx.xx.sql if xx.xx.xx.xx is newer than settings["module_name_sql_version"]
*/	function loadSQLFile($module, $name, $forceUpdate = false) {
		global $db;
		global $curMod;
		$curMod = $module;
		$name = strtolower($name);
		
		// only letters, numbers, underscores are allowed
		if (!eregi('^[a-z0-9_]+$', $name)) {
			echo "Invalid SQL file name!  Only numbers, letters, and underscores permitted!\n";
			return;
		}
		
		$settingName = $name . "_db_version";
		$currentVersion = bot::getsetting($settingName);
		// if there is no saved version, set it to -1
		// so if the maxFileVersion is 0 (ie, it has no version)
		// it will still update
		if ($currentVersion === false) {
			$currentVersion = -1;
		}
		
		$dir = "./modules/$module";
		
		$file = false;
		$maxFileVersion = 0;  // 0 indicates no version
		if ($d = dir($dir)) {
			while (false !== ($entry = $d->read())) {
				if (is_file("$dir/$entry") && eregi($name . "([0-9.]*)\\.sql", $entry, $temp)) {
					// if there is no version on the file, set the version to 0
					if ($temp[1] === false) {
						$temp[1] = 0;
					}

					if (compareVersionNumbers($temp[1], $maxFileVersion) >= 0) {
						$maxFileVersion = $temp[1];
						$file = $entry;
					}
				}
			}
		}
		
		if ($file === false) {
			echo "No SQL file found with name '$name'!\n";
		} else if (compareVersionNumbers($maxFileVersion, $currentVersion) > 0 || $forceUpdate) {		
			$filearray = file("$dir/$file", FILE_TEXT | FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

			// if the file had a version, tell them the start and end version
			// otherwise, just tell them we're updating the database
			if ($maxFileVersion != 0) {
				echo "Updating $name database...from '$currentVersion' to '$maxFileVersion'\n";
			} else {
				echo "Updating $name database...\n";
			}

			//$db->beginTransaction();
			foreach($filearray as $num => $line) {
				$db->query($line);
			}
			//$db->Commit();
			echo "Finished updating $name database.\n";
		
			// if there was no version on the file, don't save the version number
			// if maxFileVersion isn't 0, and savesetting fails (ie, a setting by that
			// name doesn't exist, then add the setting
			if ($maxFileVersion != 0) {
				if (!bot::savesetting($settingName, $maxFileVersion)) {
					bot::addsetting($settingName, $settingName, 'noedit', $maxFileVersion);
				}
			}
		} else {
			echo "$name database already up to date! version: '$currentVersion'\n";
		}
	}
}
?>