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

	var $buddyList = array();

/*===============================
** Name: __construct
** Constructor of this class.
*/	function __construct($vars, $settings){
		parent::__construct("callback");

		global $db;
		global $curMod;

		$curMod = "Basic Settings";
		$this->settings = $settings;
		$this->vars = $vars;
        $this->vars["name"] = ucfirst(strtolower($this->vars["name"]));

		//Set startuptime
		$this->vars["startup"] = time();

		//Create command/event settings table if not exists
		$db->query("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(5), `type` VARCHAR(18), `file` VARCHAR(255), `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `grp` VARCHAR(25) DEFAULT 'none')");
		$db->query("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `mode` VARCHAR(10), `setting` VARCHAR(50) Default '0', `options` VARCHAR(255) Default '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `help` VARCHAR(60))");
		$db->query("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `cat` VARCHAR(50), `description` VARCHAR(50), `admin` VARCHAR(10), `verify` INT Default '0')");

		//Prepare command/event settings table
		$db->query("UPDATE cmdcfg_<myname> SET `verify` = 0");
		$db->query("UPDATE hlpcfg_<myname> SET `verify` = 0");
		$db->query("UPDATE cmdcfg_<myname> SET `status` = 1 WHERE `cmdevent` = 'event' AND `type` = 'setup'");
		$db->query("UPDATE cmdcfg_<myname> SET `grp` = 'none'");
		$db->query("DELETE FROM cmdcfg_<myname> WHERE `module` = 'none'");

		//To reduce query's save the current commands/events in an array
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
		while ($row = $db->fObject()) {
		  	$this->existing_helps[$row->name] = true;
		}

		$db->query("SELECT * FROM settings_<myname>");
		while($row = $db->fObject()) {
		  	$this->existing_settings[$row->name] = true;
		}

		// Load the Core Modules -- SETINGS must be first in case the other modules have settings
		if($this->settings['debug'] > 0) print("\n:::::::CORE MODULES::::::::\n");
		if($this->settings['debug'] > 0) print("MODULE_NAME:(SETTINGS.php)\n");
				include "./core/SETTINGS/SETTINGS.php";
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
		// remove any old entries on buddy list
		$buddyList = array();

		echo "\n\n";

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
			echo "No valid Server to connect with! Available dimensions are 1, 2, 3 and 4.\n";
		  	sleep(10);
		  	die();
		}

		// Begin the login process
		echo "Connecting to AO Server...($server)\n";
		AOChat::connect($server, $port);
		sleep(2);
		if($this->state != "auth") {
			echo "Connection failed! Please check your Internet connection and firewall.\n";
			sleep(10);
			die();
		}

		echo "Authenticate login data...\n";
		AOChat::authenticate($login, $password);
		sleep(2);
		if($this->state != "login") {
			echo "Authentication failed! Please check your username and password.\n";
			sleep(10);
			die();
		}

		echo "Logging in {$this->vars["name"]}...\n";
		AOChat::login($this->vars["name"]);
		sleep(2);
		if($this->state != "ok") {
			echo "Logging in of {$this->vars["name"]} failed! Please check the character name and dimension.\n";
			sleep(10);
			die();
		}

		echo "All Systems ready....\n\n\n";
		sleep(2);

		// Set cron timers
		$this->vars["2sec"] 			= time() + $this->settings["CronDelay"];
		$this->vars["1min"] 			= time() + $this->settings["CronDelay"];
		$this->vars["10mins"] 			= time() + $this->settings["CronDelay"];
		$this->vars["15mins"] 			= time() + $this->settings["CronDelay"];
		$this->vars["30mins"] 			= time() + $this->settings["CronDelay"];
		$this->vars["1hour"] 			= time() + $this->settings["CronDelay"];
		$this->vars["24hours"]			= time() + $this->settings["CronDelay"];
		$this->vars["15min"] 			= time() + $this->settings["CronDelay"];
	}
	
	function get_buddy($name) {
		if (($uid = $this->get_uid($name)) === false || !isset($this->buddyList[$uid])) {
			return null;
		} else {
			return $this->buddyList[$uid];
		}
    }

/*===============================
** Name: buddy_online
** Returns null when online status is unknown, 1 when buddy is online, 0 when buddy is offline
*/	function buddy_online($name) {
		$buddy = $this->get_buddy($name);
		return ($buddy === null ? null : $buddy['online']);
    }
	
	function add_buddy($name, $type) {
		if (($uid = $this->get_uid($name)) === false || $type === null || $type == '') {
			return false;
		} else {
			if (!isset($this->buddyList[$uid])) {
				if ($this->settings['echo'] >= 1) newLine("Buddy", $name, "buddy added", $this->settings['echo']);
				$this->buddy_add($uid);
			}
			
			if (!isset($this->buddyList[$uid]['types'][$type])) {
				$this->buddyList[$uid]['types'][$type] = 1;
				if ($this->settings['echo'] >= 1) newLine("Buddy", $name, "buddy type added (type: $type)", $this->settings['echo']);
			}
			
			return true;
		}
	}
	
	function remove_buddy($name, $type = '') {
		if (($uid = $this->get_uid($name)) === false) {
			return false;
		} else if (isset($this->buddyList[$uid])) {
			if (isset($this->buddyList[$uid]['types'][$type])) {
				unset($this->buddyList[$uid]['types'][$type]);
				if ($this->settings['echo'] >= 1) newLine("Buddy", $name, "buddy type removed (type: $type)", $this->settings['echo']);
			}

			if (count($this->buddyList[$uid]['types']) == 0) {
				unset($this->buddyList[$uid]);
				if ($this->settings['echo'] >= 1) newLine("Buddy", $name, "buddy removed", $this->settings['echo']);
				$this->buddy_remove($uid);
			}
			
			return true;
		} else {
			return false;
		}
	}

	function is_buddy($name, $type) {
		if (($uid = $this->get_uid($name)) === false) {
			return false;
		} else {
			if ($type == null || $type == false) {
				return isset($this->buddyList[$uid]);
			} else {
				return isset($this->buddyList[$uid]['types'][$type]);
			}
		}
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
		global $db;

		// Check files, for all 'connect events'.
		forEach ($this->_connect as $filename) {
			include $filename;
		}
	}

/*===============================
** Name: makeHeader
** Make header.
*/	function makeHeader($title, $links = NULL){
		// if !$links, then makeHeader function will show default links:  Help, About, Download.
	        // if $links = "none", then makeHeader wont show ANY links.
		// if $links = array("Help;chatcmd:///tell <myname> help"),  slap in your own array for your own links.

		$color = $this->settings['default header color'];
		$baseR = hexdec(substr($color,14,2)); $baseG = hexdec(substr($color,16,2)); $baseB = hexdec(substr($color,18,2));
		$color2 = "<font color='#".strtoupper(substr("00".dechex($baseR*.75),-2).substr("00".dechex($baseG*.75),-2).substr("00".dechex($baseB*.75),-2))."'>";
		$color3 = "<font color='#".strtoupper(substr("00".dechex($baseR*.50),-2).substr("00".dechex($baseG*.50),-2).substr("00".dechex($baseB*.50),-2))."'>";
		$color4 = "<font color='#".strtoupper(substr("00".dechex($baseR*.25),-2).substr("00".dechex($baseG*.25),-2).substr("00".dechex($baseB*.25),-2))."'>";

		//Title
		$header = $color4.":::".$color3.":::".$color2.":::".$color;
		$header .= $title;
		$header .= "</font>:::</font>:::</font>:::</font> ";


		if (!$links) {
			$links = array( "Help;chatcmd:///tell ".$this->vars["name"]." help",
					"About;chatcmd:///tell ".$this->vars["name"]." about",
					"Download;chatcmd:///start http://budabot.aodevs.com/index.php?page=14");
		}
		if (strtolower($links) != "none") {
			foreach ($links as $thislink){
				preg_match("/^(.+);(.+)$/i", $thislink, $arr);
				if ($arr[1] && $arr[2]) {
					$header .= $color4.":".$color3.":".$color2.":";
					$header .= "<a style='text-decoration:none' href='$arr[2]'>".$color."$arr[1]</font></a>";
					$header .= ":</font>:</font>:</font>";
				}
			}
		}

		$header .= $this->settings["default window color"]."\n\n";

		return $header;
	}

/*===============================
** Name: makeLink
** Make click link reference.
*/	function makeLink($name, $content, $type = "blob", $style = NULL){
		// escape double quotes
		if ($type != 'blob') {
			$content = str_replace('"', '&quote;', $content);
		}

		if ($type == "blob") { // Normal link.
			$content = str_replace('"', '&quot;', $content);
			if (strlen($content) > $this->settings["max_blob_size"]) {  //Split the windows if they are too big
			  	$content = explode("\n", $content);
				$page = 1;
			  	forEach ($content as $line) {
					if ($page > 1 && $display) {
						$result[$page] .= "<header>::::: $name Page $page :::::<end>\n";
					}
					$display = false;
				    $result[$page] .= $line."\n";
				    if (strlen($result[$page]) >= $this->settings["max_blob_size"]) {
						$result[$page] = "<a $style href=\"text://".$this->settings["default window color"].$result[$page]."\">$name</a> (Page <highlight>$page<end>)";
				    	$page++;
						$display = true;
					}
				}
				$result[$page] = "<a $style href=\"text://".$chatBot->settings["default window color"].$result[$page]."\">$name</a> (Page <highlight>$page - End<end>)";
				return $result;
			} else {
				return "<a $style href=\"text://".$this->settings["default window color"].$content."\">$name</a>";
			}
		} else if ($type == "text") { // Majic link.
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='text://$content'>$name</a>";
		} else if ($type == "chatcmd") { // Chat command.
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='chatcmd://$content'>$name</a>";
		} else if ($type == "user") { // Adds support for right clicking usernames in chat, providing you with a menu of options (ignore etc.) (see 18.1 AO patchnotes)
			$content = str_replace("'", '&#39;', $content);
			return "<a $style href='user://$content'>$name</a>";
		}
	}
	
/*===============================
** Name: makeItem
** Make item link reference.
*/	function makeItem($lowID, $hiID,  $ql, $name){
		return "<a href='itemref://$lowID/$hiID/$ql'>$name</a>";
	}
	
/*===============================
** Name: formatMessage
** Formats an outgoing message with correct colors, replaces values, etc
*/	function formatMessage($message) {
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
		$message = str_replace("<cyan>", "<font color='#00FFFF'>", $message);
		
		$message = str_replace("<neutral>", "<font color='#EEEEEE'>", $message);
		$message = str_replace("<omni>", "<font color='#00FFFF'>", $message);
		$message = str_replace("<clan>", "<font color='#F79410'>", $message);
		$message = str_replace("<unknown>", "<font color='#FF0000'>", $message);

		$message = str_replace("<myname>", $this->vars["name"], $message);
		$message = str_replace("<tab>", "    ", $message);
		$message = str_replace("<end>", "</font>", $message);
		$message = str_replace("<symbol>", $this->settings["symbol"] , $message);

		return $message;
	}
	
	function sendPrivate($message, $group, $disable_relay = false) {
		// for when makeLink generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->send($page, $group, $disable_relay);
			}
			return;
		}
	
		$message = bot::formatMessage($message);
		AOChat::send_privgroup($group, $this->settings["default priv color"].$message);
		if (($this->settings["guest_relay"] == 1 && $this->settings["guest_relay_commands"] == 1 && !$disable_relay)) {
			AOChat::send_group($group, "</font>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}{$this->vars["name"]}</font>: {$this->settings["default priv color"]}$message</font>");
		}
	}

/*===============================
** Name: send
** Send chat messages back to aochat servers thru aochat.
*/	function send($message, $who = 'prv', $disable_relay = false) {
		// for when makeLink generates several pages
		if (is_array($message)) {
			forEach ($message as $page) {
				$this->send($page, $who, $disable_relay);
			}
			return;
		}

		if ($who == 'guild') {
			$who = 'org';
		}
		if ($who == 'priv') {
			$who = 'prv';
		}

		$message = bot::formatMessage($message);

		// Send
		if ($who == 'prv') { // Target is private chat by defult.
			AOChat::send_privgroup($this->vars["name"], $this->settings["default priv color"].$message);
			if ($this->settings["guest_relay"] == 1 && $this->settings["guest_relay_commands"] == 1 && !$disable_relay) {
				AOChat::send_group($this->vars["my guild"], "</font>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default priv color"]}$message</font>");
			}
		} else if ($who == $this->vars["my guild"] || $who == 'org') {// Target is guild chat.
    		AOChat::send_group($this->vars["my guild"], $this->settings["default guild color"].$message);
			if ($this->settings["guest_relay"] == 1 && $this->settings["guest_relay_commands"] == 1 && !$disable_relay) {
				AOChat::send_privgroup($this->vars["name"], "</font>{$this->settings["guest_color_channel"]}[{$this->vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default guild color"]}$message</font>");
			}
		} else if (AOChat::get_uid($who) != NULL) {// Target is a player.
    		AOChat::send_tell($who, $this->settings["default tell color"].$message);
			// Echo
			if ($this->settings['echo'] >= 1) newLine("Out. Msg.", $who, $message, $this->settings['echo']);
		} else { // Public channels that are not myguild.
	    	AOChat::send_group($who,$this->settings["default guild color"].$message);
		}
	}

/*===============================
** Name: loadModules
** Load all Modules
*/	function loadModules(){
		global $db;
		global $curMod;
		if($d = dir("./modules")){
			while (false !== ($entry = $d->read())){
				if(!is_dir($entry)){
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
		forEach ($data as $row) {
			bot::regcommand($row->type, $row->file, $row->cmd, $row->admin);
		}
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
		forEach ($data as $row) {
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
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `status` = '1' AND `cmdevent` = 'event'");
		$data = $db->fObject("all");
		forEach ($data as $row) {
			bot::regevent($row->type, $row->file);
		}
	}

/*===============================
** Name: Command
** 	Register a command
*/	function command($type, $filename, $command, $admin = 'all', $description = ''){
		global $curMod;
		global $db;

		if (!bot::processCommandArgs($type, $admin)) {
			echo "invalid args for command '$command'!!\n";
			return;
		}

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$module = explode("/", strtolower($filename));

		for ($i = 0; $i < count($type); $i++) {
			if($this->settings['debug'] > 1) print("Adding Command to list:($command) File:($filename)\n");
			if($this->settings['debug'] > 1) print("                 Admin:({$admin[$i]}) Type:({$type[$i]})\n");
			if($this->settings['debug'] > 2) sleep(1);
			
			if ($this->existing_commands[$type[$i]][$command] == true) {
				$db->query("UPDATE cmdcfg_<myname> SET `module` = '$curMod', `verify` = 1, `file` = '$filename', `description` = '$description' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`) VALUES ('$curMod', '{$type[$i]}', '$filename', '$command', '{$admin[$i]}', '$description', 1, 'cmd', '".$this->settings["default module status"]."')");
			}
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
		$module = strtoupper($module[0]);

		//Check if the file exists
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
    		$filename = $actual_filename;
		} else {
			echo "Error in registering the File $filename for command $command. The file doesn't exists!\n";
			return;
		}

		if ($command != NULL) { // Change commands to lower case.
			$command = strtolower($command);
		}

		$admin = strtolower($admin);

		//Check if the admin status exists
		if (!is_numeric($admin)) {
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
	}

/*===============================
** Name: processCommandType
** 	Returns a command type in the proper format
*/	function processCommandArgs(&$type, &$admin) {
		if ($type == "") {
			$type = array("msg", "priv", "guild");
		} else {
			$type = explode(' ', $type);
		}

		$admin = explode(' ', $admin);
		if (count($admin) == 1) {
			$admin = array_fill(0, count($type), $admin[0]);
		} else if (count($admin) != count($type)) {
			echo "ERROR! the number of type arguments does not equal the number of admin arguments for command/subcommand registration!";
			return false;
		}
		return true;
	}

/*===============================
** Name: Subcommand
** 	Register a subcommand
*/	function subcommand($type, $filename, $command, $admin = 'all', $dependson, $description = 'none') {
		global $db;
		global $curMod;

		if (!bot::processCommandArgs($type, $admin)) {
			echo "invalid args for subcommand '$command'!!\n";
			return;
		}

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$module = explode("/", strtolower($filename));
	  	
		if($this->settings['debug'] > 1) print("Adding Subcommand to list:($command) File:($filename)\n");
		if($this->settings['debug'] > 1) print("                    Admin:($admin) Type:($type)\n");
		if($this->settings['debug'] > 2) sleep(1);

		//Check if the file exists
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
			$filename = $actual_filename;
		} else {
			echo "Error in registering the file $filename for Subcommand $command. The file doesn't exists!\n";
			return;
		}

		if($command != NULL) // Change commands to lower case.
			$command = strtolower($command);

		for ($i = 0; $i < count($type); $i++) {
			if($this->settings['debug'] > 1) print("Adding Subcommand to list:($command) File:($filename)\n");
			if($this->settings['debug'] > 1) print("                    Admin:($admin) Type:({$type[$i]})\n");
			if($this->settings['debug'] > 2) sleep(1);
			
			//Check if the admin status exists
			if (!is_numeric($admin[$i])) {
				if ($admin[$i] == "leader") {
					$admin[$i] = 1;
				} else if ($admin[$i] == "raidleader" || $admin[$i] == "rl") {
					$admin = 2;
				} else if ($admin[$i] == "mod" || $admin[$i] == "moderator") {
					$admin[$i] = 3;
				} else if ($admin[$i] == "admin") {
					$admin[$i] = 4;
				} else if ($admin[$i] != "all" && $admin[$i] != "guild" && $admin[$i] != "guildadmin") {
					echo "Error in registrating the command $command for channel {$type[$i]}. Reason Unknown Admintype: {$admin[$i]}. Admintype is set to all now.\n";
					$admin[$i] = "all";
				}
			}

			if ($this->existing_subcmds[$type[$i]][$command] == true) {
				$db->query("UPDATE cmdcfg_<myname> SET `module` = '$curMod', `verify` = 1, `file` = '$filename', `description` = '$description', `dependson` = '$dependson' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`) VALUES ('$curMod', '{$type[$i]}', '$filename', '$command', '{$admin[$i]}', '$description', 1, 'subcmd', '$dependson')");
			}
		}
	}

/*===============================
** Name: event
**  Registers an event
*/	function event($type, $filename, $dependson = 'none', $description = 'none'){
		global $db;
		global $curMod;
		
		// disable depends on
		$description = str_replace("'", "''", $description);
		$module = explode("/", strtolower($filename));

	  	if($this->settings['debug'] > 1) print("Adding Event to list:($type) File:($filename)\n");
		if($this->settings['debug'] > 2) sleep(1);

		if ($this->settings["default module status"] == 1) {
			$status = 1;
		} else {
			$status = 0;
		}

		if ($this->existing_events[$type][$filename] == true) {
		  	$db->query("UPDATE cmdcfg_<myname> SET `verify` = 1, `description` = '$description' WHERE `type` = '$type' AND `cmdevent` = 'event' AND `file` = '$filename' AND `module` = '$curMod'");
		} else {
		  	$db->query("INSERT INTO cmdcfg_<myname> (`module`, `cmdevent`, `type`, `file`, `verify`, `description`, `status`) VALUES ('$curMod', 'event', '$type', '$filename', '1', '$description', '$status')");
		}
	}

/*===============================
** Name: regevent
**  Sets an event as active
*/	function regevent($type, $filename){
		global $db;
		global $curMod;

	  	if($this->settings['debug'] > 1) print("Activating Event:($type) File:($filename)\n");
		if($this->settings['debug'] > 2) sleep(1);

		//Check if the file exists
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
    		$filename = $actual_filename;
		} else {
			echo "Error in registering the File $filename for Eventtype $type. The file doesn't exists!\n";
			return;
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
			case "extPriv":
				if(!in_array($filename, $this->extPrivChat))
					$this->extPrivChat[] = $filename;
			break;
			case "guild":
				if(!in_array($filename, $this->guildChat))
					$this->guildChat[] = $filename;
			break;
			case "joinPriv":
				if(!in_array($filename, $this->joinPriv))
					$this->joinPriv[] = $filename;
			break;
			case "extJoinPriv":
				if(!in_array($filename, $this->extJoinPriv))
					$this->extJoinPriv[] = $filename;
			break;
			case "leavePriv":
				if(!in_array($filename, leavePriv))
					$this->leavePriv[] = $filename;
			break;
			case "extLeavePriv":
				if(!in_array($filename, extLeavePriv))
					$this->extLeavePriv[] = $filename;
			break;
			case "extJoinPrivRequest":
				if(!in_array($filename, $this->extJoinPrivRequest))
					$this->extJoinPrivRequest[] = $filename;
			break;
			case "extKickPriv":
				if(!in_array($filename, $this->extKickPriv))
					$this->extKickPriv[] = $filename;
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
			case "10mins":
				if(!in_array($filename, $this->_10mins))
					$this->_10mins[] = $filename;
			break;
			case "15mins":
				if(!in_array($filename, $this->_15mins))
					$this->_15mins[] = $filename;
			break;
			case "30mins":
				if(!in_array($filename, $this->_30mins))
					$this->_30mins[] = $filename;
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
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
    		$filename = $actual_filename;
		} else {
			echo "Error in unregistering the File $filename for Event $type. The file doesn't exists!\n";
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
			case "extPriv":
				if(in_array($filename, $this->extPrivChat)) {
					$temp = array_flip($this->extPrivChat);
					unset($this->extPrivChat[$temp[$filename]]);
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
			case "extJoinPriv":
				if(in_array($filename, $this->extJoinPriv)) {
					$temp = array_flip($this->extJoinPriv);
					unset($this->extJoinPriv[$temp[$filename]]);
				}
			break;
			case "leavePriv":
				if(in_array($filename, $this->leavePriv)) {
					$temp = array_flip($this->leavePriv);
					unset($this->leavePriv[$temp[$filename]]);
				}
			break;
			case "extLeavePriv":
				if(in_array($filename, $this->extLeavePriv)) {
					$temp = array_flip($this->extLeavePriv);
					unset($this->extLeavePriv[$temp[$filename]]);
				}
			break;
			case "extJoinPrivRequest":
				if(in_array($filename, $this->extJoinPrivRequest)) {
					$temp = array_flip($this->extJoinPrivRequest);
					unset($this->extJoinPrivRequest[$temp[$filename]]);
				}
			break;
			case "extKickPriv":
				if(in_array($filename, $this->extKickPriv)) {
					$temp = array_flip($this->extKickPriv);
					unset($this->extKickPriv[$temp[$filename]]);
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
			case "10mins":
				if(in_array($filename, $this->_10mins)) {
					$temp = array_flip($this->_10mins);
					unset($this->_10mins[$temp[$filename]]);
				}
			break;
			case "15mins":
				if(in_array($filename, $this->_15mins)) {
					$temp = array_flip($this->_15mins);
					unset($this->_15mins[$temp[$filename]]);
				}
			break;
			case "30mins":
				if(in_array($filename, $this->_30mins)) {
					$temp = array_flip($this->_30mins);
					unset($this->_30mins[$temp[$filename]]);
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
*/	function regGroup($group, $module = 'none', $description = 'none'){
		global $db;
		global $curMod;
		
		$description = str_replace("'", "''", $description);

		$group = strtolower($group);
		//Check if the module is correct
		if($module == "none") {
			echo "Error in creating group $group. You need to specify a module for the group.\n";
			return;
		}
		//Check if the group already exists
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `grp` = '$group'");
		if($db->numrows() != 0) {
			echo "Error in creating group $group. This group already exists.\n";
			return;
		}
    	$numargs = func_num_args();
    	$arg_list = func_get_args();
		//Check if enough commands are given for the group
		if($numargs < 5) {
			echo "Not enough commands to build group $group(must be at least 2commands)";
			return;
		}
		//Go through the arg list and assign it to the group
		for($i = 3;$i < $numargs; $i++) {
		  	$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmd` = '".$arg_list[$i]."' AND `module` = '$curMod'");
		  	if($db->numrows() != 0) {
			    $db->query("UPDATE cmdcfg_<myname> SET `grp` = '$group' WHERE `cmd` = '".$arg_list[$i]."' AND `module` = '$curMod'");
			} else {
			  	echo "Error in creating group $group for module $curMod. Command ".$arg_list[$i]." doesn't exists.\n";
			}
		}
	  	$db->query("INSERT INTO cmdcfg_<myname> (`module`, `type`, `cmdevent`, `verify`, `description`) VALUES ('none', '$group', 'group', '1', '$description')");
	}


/*===============================
** Name: addsetting
** Adds a setting to the list
*/	function addsetting($name, $description = 'none', $mode = 'hide', $setting = 'none', $options = 'none', $intoptions = '0', $admin = 'mod', $help = '') {
		global $db;
		global $curMod;
		$name = strtolower($name);

		//Check if the file exists
		if ($help != '' && ($actual_filename = bot::verifyFilename($help)) != '') {
    		$filename = $actual_filename;
		} else if ($help != "") {
			echo "Error in registering the File $filename for Setting $name. The file doesn't exists!\n";
			return;
		}

		if ($this->existing_settings[$name] != true) {
			$db->query("INSERT INTO settings_<myname> (`name`, `module`, `mode`, `setting`, `options`, `intoptions`, `description`, `source`, `admin`, `help`) VALUES ('$name', '$curMod', '$mode', '$setting', '$options', '$intoptions', '" . str_replace("'", "''", $description) . "', 'db', '$admin', '$help')");
		  	$this->settings[$name] = $setting;
	  	} else {
			$db->query("UPDATE settings_<myname> SET `module` = '$curMod', `mode` = '$mode', `options` = '$options', `intoptions` = '$intoptions', `description` = '" . str_replace("'", "''", $description) . "', `admin` = '$admin', `help` = '$help' WHERE `name` = '$name'");
		}
	}

/*===============================
** Name: getsetting
** Gets an loaded setting
*/	function getsetting($name) {
		$name = strtolower($name);
		if (isset($this->settings[$name])) {
	  		return $this->settings[$name];
	  	} else {
	  		return false;
		}
	}

/*===============================
** Name: savesetting
** Saves a setting to the db
*/	function savesetting($name, $newsetting = null) {
		global $db;
		$name = strtolower($name);
		if ($newsetting === null) {
			return false;
		}

		if (isset($this->settings[$name])) {
			$db->query("UPDATE settings_<myname> SET `setting` = '" . str_replace("'", "''", $newsetting) . "' WHERE `name` = '$name'");
			$this->settings[$name] = $newsetting;
		} else {
			return false;
		}
	}


/*===============================
** Name: help
** Add a help command and display text file in a link.
*/	function help($command, $filename, $admin, $description, $cat) {
	  	global $db;
		if($this->settings['debug'] > 1) print("Registering Helpfile:($filename) Cmd:($command)\n");
		if($this->settings['debug'] > 2) sleep(1);

		$command = strtolower($command);

		//Check if the admin status exists
		if (!is_numeric($admin)) {
			if ($admin == "leader") {
				$admin = 1;
			} else if ($admin == "raidleader" || $admin == "rl") {
				$admin = 2;
			} else if ($admin == "mod" || $admin == "moderator") {
				$admin = 3;
			} else if ($admin == "admin") {
				$admin = 4;
			} else if($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
				echo "Error in registrating the command $command for channel '$type'. Unknown Admin type: '$admin'. Admin type is set to 'all'.\n";
				$admin = "all";
			}
		}

		$module = explode("/", $filename);

		//Check if the file exists
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
    		$filename = $actual_filename;
    		if(substr($filename, 0, 7) == "./core/")
	    		$this->helpfiles[$module[0]][$command]["status"] = "enabled";
		} else {
			echo "Error in registering the File $filename for Help command $command. The file doesn't exists!\n";
			return;
		}

		if (isset($this->existing_helps[$command])) {
			$db->query("UPDATE hlpcfg_<myname> SET `verify` = 1, `description` = '$description', `cat` = '$module[0]' WHERE `name` = '$command'");
		} else {
			$db->query("INSERT INTO hlpcfg_<myname> VALUES ('$command', '$module[0]', '$module[0]', '$description', '$admin', 1)");
		}

		$db->query("SELECT * FROM hlpcfg_<myname> WHERE `name` = '$command'");
		$row = $db->fObject();
		$this->helpfiles[$module[0]][$command]["filename"] = $filename;
		$this->helpfiles[$module[0]][$command]["admin level"] = $row->admin;
		$this->helpfiles[$module[0]][$command]["info"] = $description;
		$this->helpfiles[$module[0]][$command]["module"] = $module[0];
	}
	
/*===========================================================================================
** Name: help_lookup
** Find a help topic for a command if it exists
*/	function help_lookup($helpcmd, $sender) {
		$helpcmd = explode(' ', $helpcmd, 2);
		$helpcmd = $helpcmd[0];
		$helpcmd = strtolower($helpcmd);
		forEach ($this->helpfiles as $cat => $commands) {
			if (isset($commands[$helpcmd])) {
				$filename = $this->helpfiles[$cat][$helpcmd]["filename"];
				$admin = $this->helpfiles[$cat][$helpcmd]["admin level"];
				break;
			}
		}

		// if help isn't found
		if ($filename == '') {
			return FALSE;
		}

		$restricted = true;
		switch ($admin) {
			case "guild":
				if (isset($this->guildmembers[$sender]) || isset($this->admins[$sender])) {
					$restricted = false;
				}
				break;
			
			case "guildadmin":
				if ($this->guildmembers[$sender] <= $this->settings['guild admin level'] || isset($this->admins[$sender])) {
					$restricted = false;
				}
				break;
			
			case "1":
			case "2":
			case "3":
				if ($this->admins[$sender]["level"] >= $admin) {
					$restricted = false;
				}
				break;
			
			case "all":
			default:
				$restricted = false;
				break;
		}

		if ($restricted === false && file_exists($filename)) {
			$data = file_get_contents($filename);
			$helpcmd = ucfirst($helpcmd);
			$msg = bot::makeLink("Help($helpcmd)", $data);
		} else {
			return FALSE;
		}

		return $msg;
	}


/*===========================================================================================
** Name: processCallback
** Proccess all incoming messages that bot recives
*/	function processCallback($type, $args){
		global $db;

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
		$restricted = false;

		switch ($type){
			case AOCP_GROUP_ANNOUNCE: // 60
				$b = unpack("C*", $args[0]);
				if ($b[1] == 3) {
					$this->vars["my guild id"] = $b[2]*256*256*256 + $b[3]*256*256 + $b[4]*256 + $b[5];
				}
			break;
			case AOCP_PRIVGRP_CLIJOIN: // 55, Incoming player joined private chat
				$channel = $this->lookup_user($args[0]);
				$sender = $this->lookup_user($args[1]);
				
				if ($channel == $this->vars['name']) {
					$type = "joinPriv";
					
					// Echo
					if ($this->settings['echo'] >= 1) newLine("Priv Group", $sender, "joined the channel.", $this->settings['echo']);

					// Remove sender if they are /ignored or /banned or if spam filter is blocking them
					if ($this->settings["Ignore"][$sender] == true || $this->banlist[$sender]["name"] == $sender || $this->spam[$sender] > 100){
						AOChat::privategroup_kick($sender);
						return;
					}
					
					// Add sender to the chatlist.
					$this->chatlist[$sender] = true;
					
					// Check files, for all 'player joined channel events'.
					if ($this->joinPriv != NULL) {
						forEach ($this->joinPriv as $filename) {
							include $filename;
							if ($stop_execution) {
								return;
							}
						}
					}
					
					// Kick if their access is restricted.
					if ($restricted === true) {
						AOChat::privategroup_kick($sender);
					}
				} else {
					$type = "extJoinPriv";
					// TODO
				}
			break;
			case AOCP_PRIVGRP_CLIPART: // 56, Incoming player left private chat
				$channel = $this->lookup_user($args[0]);
				$sender	= $this->lookup_user($args[1]);
				
				if ($channel == $this->vars['name']) {
					$type = "leavePriv";
				
					// Echo
					if ($this->settings['echo'] >= 1) newLine("Priv Group", $sender, "left the channel.", $this->settings['echo']);

					// Remove from Chatlist array.
					unset($this->chatlist[$sender]);
					
					// Check files, for all 'player left channel events'.
					forEach ($this->leavePriv as $filename) {
						include $filename;
						if ($stop_execution) {
							return;
						}
					}
				} else {
					$type = "extLeavePriv";
					// TODO
				}
			break;
			case AOCP_BUDDY_ADD: // 40, Incoming buddy logon or off
				// Basic packet data
				$sender	= $this->lookup_user($args[0]);
				$status	= 0 + $args[1];
				
				// store buddy info
				list($bid, $bonline, $btype) = $args;
				$this->buddyList[$bid]['uid'] = $bid;
				$this->buddyList[$bid]['name'] = $sender;
				$this->buddyList[$bid]['online'] = ($bonline ? 1 : 0);
				$this->buddyList[$bid]['known'] = (ord($btype) ? 1 : 0);

				//Ignore Logon/Logoff from other bots or phantom logon/offs
                if ($this->settings["Ignore"][$sender] == true || $sender == "") {
					return;
				}

				// If Status == 0(logoff) if Status == 1(logon)
				if ($status == 0) {
					$type = "logOff"; // Set message type
					
					// Echo
					//if ($this->settings['echo'] >= 1) newLine("Buddy", $sender, "logged off", $this->settings['echo']);

					// Check files, for all 'player logged off events'
					if ($this->logOff != NULL) {
						forEach ($this->logOff as $filename) {
							$msg = "";
							include $filename;
							if ($stop_execution) {
								return;
							}
						}
					}
				} else if ($status == 1) {
					$type = "logOn"; // Set Message Type
					
					// Echo
					if ($this->settings['echo'] >= 1) newLine("Buddy", $sender, "logged on", $this->settings['echo']);

					// Check files, for all 'player logged on events'.
					if ($this->logOn != NULL) {
						forEach ($this->logOn as $filename) {
						  	$msg = "";
						  	include $filename;
							if ($stop_execution) {
								return;
							}
						}
					}
				}
			break;
			case AOCP_MSG_PRIVATE: // 30, Incoming Msg
				$type = "msg"; // Set message type.
				$sender	= $this->lookup_user($args[0]);
				$sendto = $sender;
				
				// Removing tell color
				if (preg_match("/^<font color='#([0-9a-f]+)'>(.+)$/si", $args[1], $arr)) {
					$message = $arr[2];
				} else {
					$message = $args[1];
				}

				$message = html_entity_decode($message, ENT_QUOTES);

				// Echo
				if ($this->settings['echo'] >= 1) newLine("Inc. Msg.", $sender, $message, $this->settings['echo']);

				// AFK/bot check
				if (preg_match("/$sender is AFK/si", $message, $arr)) {
					return;
				} else if (preg_match("/I am away from my keyboard right now/si", $message)) {
					return;
				} else if (preg_match("/Unknown command or access denied!/si", $message, $arr)) {
					return;
				} else if (preg_match("/I am responding/si", $message, $arr)) {
					return;
				} else if (preg_match("/I only listen/si", $message, $arr)) {
					return;
				} else if (preg_match("/Error!/si", $message, $arr)) {
					return;
				}

				if ($this->settings["Ignore"][$sender] == true || $this->banlist[$sender]["name"] == $sender || ($this->spam[$sender] > 100 && $this->vars['spam protection'] == 1)){
					$this->spam[$sender] += 20;
					return;
				}
				
				// Events
				forEach ($this->privMsgs as $file) {
					$msg = "";
					include $file;
					if ($stop_execution) {
						return;
					}
				}

				//Remove the prefix infront if there is one
				if ($message[0] == $this->settings["symbol"] && strlen($message) > 1) {
					$message = substr($message, 1);
				}

				// Check privatejoin and tell Limits
				if (file_exists("./core/PRIV_TELL_LIMIT/check.php")) {
					include './core/PRIV_TELL_LIMIT/check.php';
					if ($restricted) {
						return;
					}
				}
				
				$this->handle_command($type, $message, $sender, $sendto);

			break;
			case AOCP_PRIVGRP_MESSAGE: // 57, Incoming priv message
				$sender	= $this->lookup_user($args[1]);
				$sendto = 'prv';
				$channel = $this->lookup_user($args[0]);
				$message = $args[2];
				$restricted = false;
				
				if ($sender == $this->vars["name"]) {
					if($this->settings['echo'] >= 1) newLine("Priv Group", $sender, $message, $this->settings['echo']);
					return;
				}
				
				if ($this->banlist[$sender]["name"] == $sender) {
					return;
				}

				if ($this->vars['spam protection'] == 1) {
					if ($this->spam[$sender] == 40) $this->send("Error! Your client is sending a high frequency of chat messages. Stop or be kicked.", $sender);
					if ($this->spam[$sender] > 60) AOChat::privategroup_kick($sender);
					if (strlen($args[1]) > 400){
						$this->largespam[$sender] = $this->largespam[$sender] + 1;
						if ($this->largespam[$sender] > 1) AOChat::privategroup_kick($sender);
						if ($this->largespam[$sender] > 0) $this->send("Error! Your client is sending large chat messages. Stop or be kicked.", $sender);
					}
				}

				if ($channel == $this->vars['name']) {

					$type = "priv";

					// Echo
					if ($this->settings['echo'] >= 1) newLine("Priv Group", $sender, $message, $this->settings['echo']);
					
					// Events
					forEach ($this->privChat as $file) {
						$msg = "";
						include $file;
						if ($stop_execution) {
							return;
						}
					}
					
					if ($message[0] == $this->settings["symbol"] && strlen($message) > 1) {
						$message = substr($message, 1);
						$this->handle_command($type, $message, $sender, $sendto);
					}
				
				} else {  // ext priv group message
					
					$type = "extPriv";
					
					if ($this->settings['echo'] >= 1) newLine($channel, $sender, $message, $this->settings['echo']);
					
					if ($this->extPrivChat != NULL) {
						forEach ($this->extPrivChat as $file) {
						  	$msg = "";
							include $file;
							if ($stop_execution) {
								return;
							}
						}
					}
				}
			break;
			case AOCP_GROUP_MESSAGE: // 65, Public and guild channels
				$syntax_error = false;
				$sender	 = $this->lookup_user($args[1]);
				$message = $args[2];
				$channel = $this->get_gname($args[0]);

				//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
				$channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
					'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
					'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');

				if (in_array($channel, $channelsToIgnore)) {
					return;
				}

				// if it's an extended message
				$em = null;
				if (isset($args['extended_message'])) {
					$em = $args['extended_message'];
					$db->query("SELECT category, entry, message FROM mmdb_data WHERE category = $em->category AND entry = $em->instance");
					if ($row = $db->fObject()) {
						$message = vsprintf($row->message, $em->args);
					} else {
						echo "Error: cannot find extended message with category: '$em->category' and instance: '$em->instance'\n";
					}
				}

				if ($this->settings['echo'] >= 1) newLine($channel, $sender, $message, $this->settings['echo']);

				if ($sender) {
					//Ignore Message that are sent from the bot self
					if ($sender == $this->vars["name"]) {
						return;
					}

					//Ignore messages from other bots
	                if ($this->settings["Ignore"][$sender] == true) {
						return;
					}

					if ($this->banlist[$sender]["name"] == $sender) {
						return;
					}
				}

				if ($channel == "All Towers" || $channel == "Tower Battle Outcome"){
                    $type = "towers";
    				if ($this->towers != NULL) {
    					forEach ($this->towers as $file) {
    						$msg = "";
							include $file;
							if ($stop_execution) {
								return;
							}
    					}
					}
                    return;
                } else if ($channel == "Org Msg"){
                    $type = "orgmsg";
    				if ($this->orgmsg != NULL) {
						forEach ($this->orgmsg as $file) {
    						$msg = "";
							include $file;
							if ($stop_execution) {
								return;
							}
    					}
					}
                    return;
                } else if ($channel == $this->vars["my guild"]) {
                    $type = "guild";
					$sendto = 'org';
					
					// Events
					forEach ($this->guildChat as $file) {
						$msg = "";
						include $file;
						if ($stop_execution) {
							return;
						}
					}
					
					if ($message[0] == $this->settings["symbol"] && strlen($message) > 1) {
						$message = substr($message, 1);
						$this->handle_command($type, $message, $sender, $sendto);
					}
				}
			break;
			case AOCP_PRIVGRP_INVITE:  // 50, private group invite
				$type = "extJoinPrivRequest"; // Set message type.
				$uid = $args[0];
				$sender = $this->lookup_user($uid);

				// Echo
				if ($this->settings['echo'] >= 1) newLine("Priv Group Invitation", $sender, " channel invited.", $this->settings['echo']);

				if ($this->extJoinPrivRequest != NULL) {
					forEach ($this->extJoinPrivRequest as $file) {
						$msg = "";
						include $file;
						if ($stop_execution) {
							return;
						}
					}
				}
                return;
			break;
		}
	}
	
	function handle_command($type, $message, $sender, $sendto) {
		global $db;
		
		$restricted = false;
		
		switch ($type){
			case "msg":
				$cmds  = &$this->tellCmds;
				break;
			case "priv":
				$cmds  = &$this->privCmds;
				break;
			case "guild":
				$cmds =  &$this->guildCmds;
				break;
		}
		
		// Admin Code
		if ($restricted != true) {
			list($cmd) = explode(' ', $message, 2);
			$cmd = strtolower($cmd);
			$admin 	= $cmds[$cmd]["admin level"];
			$filename = $cmds[$cmd]["filename"];

			//Check if a subcommands for this exists
			if ($this->subcommands[$filename][$type]) {
				if (preg_match("/^{$this->subcommands[$filename][$type]["cmd"]}$/i", $message)) {
					$admin = $this->subcommands[$filename][$type]["admin"];
				}
			}

			// Admin Check
			if (is_numeric($admin)) {
				if ($this->admins[$sender]["level"] >= $admin && $this->admins[$sender]["level"] != "")
					$restricted = false;
				else if ($this->admins[$sender]["level"] == "" && $this->vars["leader"] == $sender && $admin == 1)
					$restricted = false;
				else
					$restricted = true;
			} else if ($admin == "guild") {
				if (isset($this->guildmembers[$sender]))
					$restricted = false;
				else
					$restricted = true;
			} else if ($admin == "guildadmin") {
				if ($this->guildmembers[$sender] <= $this->settings['guild admin level'])
					$restricted = false;
				else
					$restricted = true;
			} else {
				$restricted = false;
			}
		}

		if ($restricted == true || $filename == "") {
			if ($type != 'guild') {
				// don't notify user of unknown command in org chat, in case they are running more than one bot
				$this->send("Error! Unknown command or Access denied! for more info try /tell <myname> help", $sendto);
				$this->spam[$sender] = $this->spam[$sender] + 20;
			}
			return;
		} else {
			$syntax_error = false;
			$msg = "";
			include $filename;
			if ($syntax_error == true) {
				if (($output = bot::help_lookup($message, $sender)) !== FALSE) {
					bot::send($output, $sendto);
				} else {
					bot::send("Error! Check your syntax or for more info try /tell <myname> help", $sendto);
				}
			}
			$this->spam[$sender] = $this->spam[$sender] + 10;
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
			case $this->vars["10mins"] < time();
				$this->vars["10mins"] 	= time() + (60 * 10);
				if($this->_10mins != NULL)
					foreach($this->_10mins as $filename)
						include $filename;

				break;
			case $this->vars["15mins"] < time();
				$this->vars["15mins"] 	= time() + (60 * 15);
				if($this->_15mins != NULL)
					foreach($this->_15mins as $filename)
						include $filename;

				break;
			case $this->vars["30mins"] < time();
				$this->vars["30mins"] 	= time() + (60 * 30);
				if($this->_30mins != NULL)
					foreach($this->_30mins as $filename)
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

		if (!bot::verifyNameConvention($filename)) {
			return "";
		}

		//check if the file exists
	    if (file_exists("./core/$filename")) {
	        return "./core/$filename";
    	} else if (file_exists("./modules/$filename")) {
        	return "./modules/$filename";
	    } else {
	     	return "";
	    }
	}

	function verifyNameConvention($filename) {
		preg_match("/^(.+)/([0-9a-z_]+).php$/i", $filename, $arr);
		if ($arr[2] == strtolower($arr[2])) {
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
** Will load the sql file with name $namexx.xx.xx.xx.sql if xx.xx.xx.xx is greater
** than settings[$name . "_sql_version"]
*/	function loadSQLFile($module, $name, $forceUpdate = false) {
		global $db;
		global $curMod;
		$curMod = $module;
		$name = strtolower($name);
		
		// only letters, numbers, underscores are allowed
		if (!preg_match('/^[a-z0-9_]+$/', $name)) {
			echo "Invalid SQL file name: '$name' for module: '$module'!  Only numbers, letters, and underscores permitted!\n";
			return;
		}
		
		$settingName = $name . "_db_version";
		
		$core_dir = "./core/$module";
		$modules_dir = "./modules/$module";
		$dir = '';
		if ($d = dir($modules_dir)) {
			$dir = $modules_dir;
		} else if ($d = dir($core_dir)) {
			$dir = $core_dir;
		}
		
		$currentVersion = bot::getsetting($settingName);
		if ($currentVersion === false) {
			$currentVersion = 0;
		}

		$file = false;
		$maxFileVersion = 0;  // 0 indicates no version
		if ($d) {
			while (false !== ($entry = $d->read())) {
				if (is_file("$dir/$entry") && preg_match("/^" . $name . "([0-9.]*)\\.sql$/i", $entry, $arr)) {
					// if there is no version on the file, set the version to 0, and force update every time
					if ($arr[1] == '') {
						$file = $entry;
						$maxFileVersion = 0;
						$forceUpdate = true;
						break;
					}

					if (compareVersionNumbers($arr[1], $maxFileVersion) >= 0) {
						$maxFileVersion = $arr[1];
						$file = $entry;
					}
				}
			}
		}
		
		if ($file === false) {
			echo "No SQL file found with name '$name'!\n";
		} else if ($forceUpdate || compareVersionNumbers($maxFileVersion, $currentVersion) > 0) {
			// if the file had a version, tell them the start and end version
			// otherwise, just tell them we're updating the database
			if ($maxFileVersion != 0) {
				echo "Updating '$name' database from '$currentVersion' to '$maxFileVersion'...";
			} else {
				echo "Updating '$name' database...";
			}

			$fileArray = file("$dir/$file");
			//$db->beginTransaction();
			forEach ($fileArray as $num => $line) {
				$line = trim($line);
				// don't process comment lines or blank lines
				if ($line != '' && substr($line, 0, 1) != "#" && substr($line, 0, 2) != "--") {
					$db->exec($line);
				}
			}
			//$db->Commit();
			echo "Finished!\n";
		
			if (!bot::savesetting($settingName, $maxFileVersion)) {
				bot::addsetting($settingName, $settingName, 'noedit', $maxFileVersion);
			}
		} else {
			echo "Updating '$name' database...already up to date! version: '$currentVersion'\n";
		}
	}
}
?>