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

require_once 'MyCurl.class.php';
require_once 'Playfields.class.php';
require_once 'AccessLevel.class.php';
require_once 'Command.class.php';
require_once 'Event.class.php';
require_once 'Setting.class.php';
require_once 'Help.class.php';
require_once 'Buddylist.class.php';

class bot extends AOChat {

	var $buddyList = array();
	var $chatlist = array();
	var $guildmembers = array();
	
	var $tellCmds = array();
	var $privCmds = array();
	var $guildCmds = array();

/*===============================
** Name: __construct
** Constructor of this class.
*/	function __construct($vars, $settings){
		parent::__construct("callback");

		$this->settings = $settings;
		$this->vars = $vars;
        $this->vars["name"] = ucfirst(strtolower($this->vars["name"]));

		// Set startup time
		$this->vars["startup"] = time();
		
		$db = DB::get_instance();

		// Create command/event settings table if not exists
		$db->exec("CREATE TABLE IF NOT EXISTS cmdcfg_<myname> (`module` VARCHAR(50), `cmdevent` VARCHAR(5), `type` VARCHAR(18), `file` VARCHAR(255), `cmd` VARCHAR(25), `admin` VARCHAR(10), `description` VARCHAR(50) DEFAULT 'none', `verify` INT DEFAULT '0', `status` INT DEFAULT '0', `dependson` VARCHAR(25) DEFAULT 'none', `grp` VARCHAR(25) DEFAULT 'none')");
		$db->exec("CREATE TABLE IF NOT EXISTS settings_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `mode` VARCHAR(10), `setting` VARCHAR(50) Default '0', `options` VARCHAR(255) Default '0', `intoptions` VARCHAR(50) DEFAULT '0', `description` VARCHAR(50), `source` VARCHAR(5), `admin` VARCHAR(25), `help` VARCHAR(60))");
		$db->exec("CREATE TABLE IF NOT EXISTS hlpcfg_<myname> (`name` VARCHAR(30) NOT NULL, `module` VARCHAR(50), `description` VARCHAR(50), `admin` VARCHAR(10), `verify` INT Default '0')");
	}

/*===============================
** Name: connect
** Connect to AO chat servers.
*/	function connectAO($login, $password){
		// Choose Server
		if ($this->vars["dimension"] == 1) {
			$server = "chat.d1.funcom.com";
			$port = 7101;
		} else if ($this->vars["dimension"] == 2) {
			$server = "chat.d2.funcom.com";
			$port = 7102;
		} else if ($this->vars["dimension"] == 3) {
			$server = "chat.d3.funcom.com";
			$port = 7103;
		} else if ($this->vars["dimension"] == 4) {
			$server = "chat.dt.funcom.com";
			$port = 7109;
		} else {
			Logger::log('ERROR', 'StartUp', "No valid Server to connect with! Available dimensions are 1, 2, 3 and 4.");
		  	sleep(10);
		  	die();
		}

		// Begin the login process
		Logger::log('INFO', 'StartUp', "Connecting to AO Server...($server)");
		AOChat::connect($server, $port);
		sleep(2);
		if ($this->state != "auth") {
			Logger::log('ERROR', 'StartUp', "Connection failed! Please check your Internet connection and firewall.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "Authenticate login data...");
		AOChat::authenticate($login, $password);
		sleep(2);
		if ($this->state != "login") {
			Logger::log('ERROR', 'StartUp', "Authentication failed! Please check your username and password.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "Logging in {$this->vars["name"]}...");
		AOChat::login($this->vars["name"]);
		sleep(2);
		if ($this->state != "ok") {
			Logger::log('ERROR', 'StartUp', "Logging in of {$this->vars["name"]} failed! Please check the character name and dimension.");
			sleep(10);
			die();
		}

		Logger::log('INFO', 'StartUp', "All Systems ready!");
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
		$uid = $this->get_uid($name);
		if ($uid === false || !isset($this->buddyList[$uid])) {
			return null;
		} else {
			return $this->buddyList[$uid];
		}
    }
	
	function init() {
		Logger::log('DEBUG', 'Core', 'Initializing bot');
		
		$db = DB::get_instance();
		
		//Delete old vars in case they exist
		unset($this->subcommands);
		unset($this->tellCmds);
		unset($this->privCmds);
		unset($this->guildCmds);
		unset($this->towers);
		unset($this->orgmsg);
		unset($this->privMsgs);
		unset($this->privChat);
		unset($this->guildChat);
		unset($this->joinPriv);
		unset($this->leavePriv);
		unset($this->logOn);
		unset($this->logOff);
		unset($this->_2sec);
		unset($this->_1min);
		unset($this->_10mins);
		unset($this->_15mins);
		unset($this->_30mins);
		unset($this->_1hour);
		unset($this->_24hrs);
		unset($this->_connect);
		unset($this->helpfiles);
		
		//Prepare command/event settings table
		$db->exec("UPDATE cmdcfg_<myname> SET `verify` = 0");
		$db->exec("UPDATE hlpcfg_<myname> SET `verify` = 0");
		$db->exec("UPDATE cmdcfg_<myname> SET `status` = 1 WHERE `cmdevent` = 'event' AND `type` = 'setup'");
		$db->exec("UPDATE cmdcfg_<myname> SET `grp` = 'none'");

		//To reduce querys save the current commands/events in arrays
		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'cmd'");
		while ($row = $db->fObject()) {
		  	$this->existing_commands[$row->type][$row->cmd] = true;
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'subcmd'");
		while ($row = $db->fObject()) {
		  	$this->existing_subcmds[$row->type][$row->cmd] = true;
		}

		$db->query("SELECT * FROM cmdcfg_<myname> WHERE `cmdevent` = 'event'");
		while ($row = $db->fObject()) {
			if (bot::verifyNameConvention($row->file)) {
			  	$this->existing_events[$row->type][$row->file] = true;
			}
		}

		$db->query("SELECT * FROM hlpcfg_<myname>");
		while ($row = $db->fObject()) {
		  	$this->existing_helps[$row->name] = true;
		}

		$db->query("SELECT * FROM settings_<myname>");
		while ($row = $db->fObject()) {
		  	$this->existing_settings[$row->name] = true;
		}

		// Load the Core Modules -- SETINGS must be first in case the other modules have settings
		Logger::log('INFO', 'Core', "Loading CORE modules...");
		
		Logger::log('debug', 'Core', "MODULE_NAME:(SETTINGS.php)");
		include "./core/SETTINGS/SETTINGS.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(SYSTEM.php)");
		include "./core/SYSTEM/SYSTEM.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(ADMIN.php)");
		include "./core/ADMIN/ADMIN.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(BAN.php)");
		include "./core/BAN/BAN.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(HELP.php)");
		include "./core/HELP/HELP.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(CONFIG.php)");
		include "./core/CONFIG/CONFIG.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(BASIC_CONNECTED_EVENTS.php)\n");
		include "./core/BASIC_CONNECTED_EVENTS/BASIC_CONNECTED_EVENTS.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(PRIV_TELL_LIMIT.php)\n");
		include "./core/PRIV_TELL_LIMIT/PRIV_TELL_LIMIT.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(PLAYER_LOOKUP.php)\n");
		include "./core/PLAYER_LOOKUP/PLAYER_LOOKUP.php";
		
		Logger::log('debug', 'Core', "MODULE_NAME:(FRIENDLIST.php)\n");
		include "./core/FRIENDLIST/FRIENDLIST.php";

		Logger::log('INFO', 'Core', "Loading USER modules...");

		//Start Transaction
		$db->beginTransaction();
		//Load modules
		$this->loadModules();
		//Submit the Transactions
		$db->Commit();
		
		//remove arrays
		unset($this->existing_commands);
		unset($this->existing_events);
		unset($this->existing_subcmds);
		unset($this->existing_settings);
		unset($this->existing_helps);
		
		//Delete old entrys in the DB
		$db->exec("DELETE FROM hlpcfg_<myname> WHERE `verify` = 0");
		$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0");
		$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'event'");
		$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'cmd'");
		$db->exec("DELETE FROM cmdcfg_<myname> WHERE `verify` = 0 AND `cmdevent` = 'subcmd'");

		//Load active commands
		Logger::log('debug', 'Core', "Loading active commands");
		Command::loadCommands();

		//Load active subcommands
		Logger::log('debug', 'Core', "Loading active subcommands");
		Command::loadSubcommands();

		//Load active events
		Logger::log('debug', 'Core', "Loading active events");
		Event::loadEvents();
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
		$db = DB::get_instance();

		Logger::log('DEBUG', 'Core', "Executing connected events");

		// Check files, for all 'connect' events.
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

		$color = $this->settings['default_header_color'];
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
			forEach ($links as $thislink){
				preg_match("/^(.+);(.+)$/i", $thislink, $arr);
				if ($arr[1] && $arr[2]) {
					$header .= $color4.":".$color3.":".$color2.":";
					$header .= "<a style='text-decoration:none' href='$arr[2]'>".$color."$arr[1]</font></a>";
					$header .= ":</font>:</font>:</font>";
				}
			}
		}

		$header .= $this->settings["default_window_color"]."\n\n";

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
				$array = explode("<pagebreak>", $content);
				$pagebreak = true;
				
				// if the blob hasn't specified how to split it, split on linebreaks
				if (count($array) == 1) {
					$array = explode("\n", $content);
					$pagebreak = false;
				}
				$page = 1;
				$page_size = 0;
			  	forEach ($array as $line) {
					// preserve newline char if we split on newlines
					if ($pagebreak == false) {
						$line .= "\n";
					}
					$line_length = strlen($line);
					if ($page_size + $line_length < $this->settings["max_blob_size"]) {
						$result[$page] .= $line;
						$page_size += $line_length;
				    } else {
						$result[$page] = "<a $style href=\"text://".$this->settings["default_window_color"].$result[$page]."\">$name</a> (Page <highlight>$page<end>)";
				    	$page++;
						
						$result[$page] .= "<header>::::: $name Page $page :::::<end>\n\n";
						$page_size = strlen($result[$page]);
					}
				}
				$result[$page] = "<a $style href=\"text://".$chatBot->settings["default_window_color"].$result[$page]."\">$name</a> (Page <highlight>$page - End<end>)";
				return $result;
			} else {
				$content = str_replace('<pagebreak>', '', $content);
				return "<a $style href=\"text://".$this->settings["default_window_color"].$content."\">$name</a>";
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
		$message = str_replace("<header>", $this->settings['default_header_color'], $message);
		$message = str_replace("<highlight>", $this->settings['default_highlight_color'], $message);
		$message = str_replace("<black>", "<font color='#000000'>", $message);
		$message = str_replace("<white>", "<font color='#FFFFFF'>", $message);
		$message = str_replace("<yellow>", "<font color='#FFFF00'>", $message);
		$message = str_replace("<blue>", "<font color='#8CB5FF'>", $message);
		$message = str_replace("<green>", "<font color='#00DE42'>", $message);
		$message = str_replace("<red>", "<font color='#ff0000'>", $message);
		$message = str_replace("<orange>", "<font color='#FCA712'>", $message);
		$message = str_replace("<grey>", "<font color='#C3C3C3'>", $message);
		$message = str_replace("<cyan>", "<font color='#00FFFF'>", $message);
		
		$message = str_replace("<neutral>", $this->settings['default_neut_color'], $message);
		$message = str_replace("<omni>", $this->settings['default_omni_color'], $message);
		$message = str_replace("<clan>", $this->settings['default_clan_color'], $message);
		$message = str_replace("<unknown>", $this->settings['default_unknown_color'], $message);

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
		AOChat::send_privgroup($group, $this->settings["default_priv_color"].$message);
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
			AOChat::send_privgroup($this->vars["name"], $this->settings["default_priv_color"].$message);
			
			// relay to guild channel
			if (!$disable_relay && $this->settings["guest_relay"] == 1 && $this->settings["guest_relay_commands"] == 1) {
				AOChat::send_group($this->vars["my guild"], "</font>{$this->settings["guest_color_channel"]}[Guest]</font> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default_priv_color"]}$message</font>");
			}

			// relay to bot relay
			if (!$disable_relay && $this->settings["relaybot"] != "Off" && $this->settings["bot_relay_commands"] == 1) {
				send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$message);
			}
		} else if ($who == $this->vars["my guild"] || $who == 'org') {// Target is guild chat.
    		AOChat::send_group($this->vars["my guild"], $this->settings["default_guild_color"].$message);
			
			// relay to private channel
			if (!$disable_relay && $this->settings["guest_relay"] == 1 && $this->settings["guest_relay_commands"] == 1) {
				AOChat::send_privgroup($this->vars["name"], "</font>{$this->settings["guest_color_channel"]}[{$this->vars["my guild"]}]</font> {$this->settings["guest_color_username"]}".bot::makeLink($this->vars["name"],$this->vars["name"],"user")."</font>: {$this->settings["default_guild_color"]}$message</font>");
			}
			
			// relay to bot relay
			if (!$disable_relay && $this->settings["relaybot"] != "Off" && $this->settings["bot_relay_commands"] == 1) {
				send_message_to_relay("grc <grey>[".$this->vars["my guild"]."] ".$message);
			}
		} else if (AOChat::get_uid($who) != NULL) {// Target is a player.
			Logger::log_chat("Out. Msg.", $who, $message);
    		AOChat::send_tell($who, $this->settings["default_tell_color"].$message);
		} else { // Public channels that are not myguild.
	    	AOChat::send_group($who,$this->settings["default_guild_color"].$message);
		}
	}

/*===============================
** Name: loadModules
** Load all Modules
*/	function loadModules(){
		$db = DB::get_instance();

		if ($d = dir("./modules")) {
			while (false !== ($entry = $d->read())) {
				if (!is_dir($entry)) {
					// Look for the plugin's ... setup file
					if (file_exists("./modules/$entry/$entry.php")) {
						Logger::log('DEBUG', 'Core', "MODULE_NAME:($entry.php)");
						include "./modules/$entry/$entry.php";
					}
				}
			}
			$d->close();
		}
	}

/*===============================
** Name: Command
** 	Register a command
*/	function command($type, $filename, $command, $admin = 'all', $description = ''){
		$db = DB::get_instance();

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$array = explode("/", strtolower($filename));
		$module = strtoupper($array[0]);
		
		if (!bot::processCommandArgs($type, $admin)) {
			Logger::log('ERROR', 'Core', "invalid args for $module:command($command)");
			return;
		}
		
		//Check if the file exists
		if (bot::verifyFilename($filename) == '') {
			Logger::log('ERROR', 'Core', "Error in registering the File $filename for command $command. The file doesn't exists!");
			return;
		}

		for ($i = 0; $i < count($type); $i++) {
			Logger::log('debug', 'Core', "Adding Command to list:($command) File:($filename) Admin:({$admin[$i]}) Type:({$type[$i]})");
			
			if ($this->existing_commands[$type[$i]][$command] == true) {
				$db->exec("UPDATE cmdcfg_<myname> SET `module` = '$module', `verify` = 1, `file` = '$filename', `description` = '$description' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				if ($this->settings["default_module_status"] == 1) {
					$status = 1;
				} else {
					$status = 0;
				}
				$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `status`) VALUES ('$module', '{$type[$i]}', '$filename', '$command', '{$admin[$i]}', '$description', 1, 'cmd', '$status')");
			}
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
			Logger::log('ERROR', 'Core', "ERROR! the number of type arguments does not equal the number of admin arguments for command/subcommand registration!");
			return false;
		}
		return true;
	}

/*===============================
** Name: Subcommand
** 	Register a subcommand
*/	function subcommand($type, $filename, $command, $admin = 'all', $dependson, $description = 'none') {
		$db = DB::get_instance();

		$command = strtolower($command);
		$description = str_replace("'", "''", $description);
		$array = explode("/", strtolower($filename));
		$module = $array[0];

		Logger::log('debug', 'Core', "Adding $module:subcommand($command) File:($filename) Admin:($admin) Type:($type)");

		if (!bot::processCommandArgs($type, $admin)) {
			Logger::log('ERROR', 'Core', "Invalid args for $module:subcommand($command)");
			return;
		}

		//Check if the file exists
		if (($actual_filename = bot::verifyFilename($filename)) != '') {
			$filename = $actual_filename;
		} else {
			Logger::log('ERROR', 'Core', "Error in registering the file $filename for Subcommand $command. The file doesn't exists!");
			return;
		}

		if($command != NULL) // Change commands to lower case.
			$command = strtolower($command);

		for ($i = 0; $i < count($type); $i++) {
			Logger::log('debug', 'Core', "Adding Subcommand to list:($command) File:($filename) Admin:($admin) Type:({$type[$i]})");
			
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
					Logger::log('ERROR', 'Core', "Error in registrating $module:subcommand($command) for channel {$type[$i]}. Reason Unknown Admintype: {$admin[$i]}. Admintype is set to all now.");
					$admin[$i] = "all";
				}
			}

			if ($this->existing_subcmds[$type[$i]][$command] == true) {
				$db->exec("UPDATE cmdcfg_<myname> SET `module` = '$module', `verify` = 1, `file` = '$filename', `description` = '$description', `dependson` = '$dependson' WHERE `cmd` = '$command' AND `type` = '{$type[$i]}'");
			} else {
				$db->exec("INSERT INTO cmdcfg_<myname> (`module`, `type`, `file`, `cmd`, `admin`, `description`, `verify`, `cmdevent`, `dependson`) VALUES ('$module', '{$type[$i]}', '$filename', '$command', '{$admin[$i]}', '$description', 1, 'subcmd', '$dependson')");
			}
		}
	}

/*===========================================================================================
** Name: processCallback
** Proccess all incoming messages that bot recives
*/	function processCallback($type, $args){
		$db = DB::get_instance();

		// modules can set this to true to stop execution after they are called
		$stop_execution = false;
		$restricted = false;

		switch ($type){
			case AOCP_GROUP_ANNOUNCE: // 60
				$b = unpack("C*", $args[0]);
				Logger::log('DEBUG', 'Packets', "AOCP_GROUP_ANNOUNCE => name: '$args[1]'");
				if ($b[1] == 3) {
					$this->vars["my guild id"] = $b[2]*256*256*256 + $b[3]*256*256 + $b[4]*256 + $b[5];
				}
				break;
			case AOCP_PRIVGRP_CLIJOIN: // 55, Incoming player joined private chat
				$channel = $this->lookup_user($args[0]);
				$sender = $this->lookup_user($args[1]);

				Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIJOIN => channel: '$channel' sender: '$sender'");
				
				if ($channel == $this->vars['name']) {
					$type = "joinPriv";

					Logger::log_chat("Priv Group", -1, "$sender joined the channel.");

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
				
				Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_CLIPART => channel: '$channel' sender: '$sender'");
				
				if ($channel == $this->vars['name']) {
					$type = "leavePriv";
				
					Logger::log_chat("Priv Group", -1, "$sender left the channel.");

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
				$sender	= $this->lookup_user($args[0]);
				$status	= 0 + $args[1];
				
				Logger::log('DEBUG', 'Packets', "AOCP_BUDDY_ADD => sender: '$sender' status: '$status'");
				
				// store buddy info
				list($bid, $bonline, $btype) = $args;
				$this->buddyList[$bid]['uid'] = $bid;
				$this->buddyList[$bid]['name'] = $sender;
				$this->buddyList[$bid]['online'] = ($bonline ? 1 : 0);
				$this->buddyList[$bid]['known'] = (ord($btype) ? 1 : 0);

				// Ignore Logon/Logoff from other bots or phantom logon/offs
                if ($this->settings["Ignore"][$sender] == true || $sender == "") {
					return;
				}

				// If Status == 0(logoff) if Status == 1(logon)
				if ($status == 0) {
					$type = "logOff";
					
					Logger::log('debug', "Buddy", "$sender logged off");

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
					$type = "logOn";
					
					Logger::log('info', "Buddy", "$sender logged on");

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
				$type = "msg";
				$sender	= $this->lookup_user($args[0]);
				$sendto = $sender;
				
				Logger::log('DEBUG', 'Packets', "AOCP_MSG_PRIVATE => sender: '$sender' message: '$args[1]'");
				
				// Removing tell color
				if (preg_match("/^<font color='#([0-9a-f]+)'>(.+)$/si", $args[1], $arr)) {
					$message = $arr[2];
				} else {
					$message = $args[1];
				}

				$message = html_entity_decode($message, ENT_QUOTES);

				Logger::log_chat("Inc. Msg.", $sender, $message);

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
				} else if (preg_match("/Unknown command input/si", $message, $arr)) {
					return;
				}

				if ($this->settings["Ignore"][$sender] == true || $this->banlist[$sender]["name"] == $sender || ($this->spam[$sender] > 100 && $this->vars['spam_protection'] == 1)){
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

				// Remove the prefix infront if there is one
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
				
				Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");
				Logger::log_chat($channel, $sender, $message);
				
				if ($sender == $this->vars["name"]) {
					return;
				}
				
				if ($this->banlist[$sender]["name"] == $sender) {
					return;
				}

				if ($this->vars['spam_protection'] == 1) {
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
				
				Logger::log('DEBUG', 'Packets', "AOCP_GROUP_MESSAGE => sender: '$sender' channel: '$channel' message: '$message'");

				//Ignore Messages from Vicinity/IRRK New Wire/OT OOC/OT Newbie OOC...
				$channelsToIgnore = array("", 'IRRK News Wire', 'OT OOC', 'OT Newbie OOC', 'OT Jpn OOC', 'OT shopping 11-50',
					'Tour Announcements', 'Neu. Newbie OOC', 'Neu. Jpn OOC', 'Neu. shopping 11-50', 'Neu. OOC', 'Clan OOC',
					'Clan Newbie OOC', 'Clan Jpn OOC', 'Clan shopping 11-50', 'OT German OOC', 'Clan German OOC', 'Neu. German OOC');

				if (in_array($channel, $channelsToIgnore)) {
					return;
				}

				// don't log tower messages with rest of chat messages
				if ($channel != "All Towers" && $channel != "Tower Battle Outcome") {
					Logger::log_chat($channel, $sender, $message);
				} else {
					Logger::log('DEBUG', $channel, $message);
				}

				if ($sender) {
					// Ignore Message that are sent from the bot self
					if ($sender == $this->vars["name"]) {
						return;
					}

					// Ignore messages from other bots
	                if ($this->settings["Ignore"][$sender] == true) {
						return;
					}

					if ($this->banlist[$sender]["name"] == $sender) {
						return;
					}
				}

				if ($channel == "All Towers" || $channel == "Tower Battle Outcome") {
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
				} else if ($channel == 'OT shopping 11-50' || $channel == 'OT shopping 50-100' || $channel == 'OT shopping 100+' || $channel == 'Neu. shopping 11-50' || $channel == 'Neu. shopping 50-100' || $channel == 'Neu. shopping 100+' || $channel == 'Clan shopping 11-50' || $channel == 'Clan shopping 50-100' || $channel == 'Clan shopping 100+') {
					$type = "shopping";
    				foreach($this->shopping as $file) {
						$msg = "";
						include $file;
						if ($stop_execution) {
							return;
						}
					}
				}
				break;
			case AOCP_PRIVGRP_INVITE:  // 50, private channel invite
				$type = "extJoinPrivRequest"; // Set message type.
				$uid = $args[0];
				$sender = $this->lookup_user($uid);
				
				Logger::log('DEBUG', 'Packets', "AOCP_PRIVGRP_INVITE => sender: '$sender'");

				Logger::log_chat("Priv Channel Invitation", -1, "$sender channel invited.");

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
		$db = DB::get_instance();
		
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
		list($cmd) = explode(' ', $message, 2);
		$cmd = strtolower($cmd);
		$admin 	= $cmds[$cmd]["admin level"];
		$filename = $cmds[$cmd]["filename"];

		// Check if a subcommands for this exists
		if ($this->subcommands[$filename][$type]) {
			if (preg_match("/^{$this->subcommands[$filename][$type]["cmd"]}$/i", $message)) {
				$admin = $this->subcommands[$filename][$type]["admin"];
			}
		}

		// Admin Check
		$access = AccessLevel::checkAccess($sender, $admin);

		if ($access !== true || $filename == "") {
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
				$output = Help::find($message, $sender);
				if ($output !== false) {
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
		$db = DB::get_instance();
		switch($this->vars){
			case $this->vars["2sec"] < time();
				Logger::log('DEBUG', 'Cron', "2secs");
				$this->vars["2sec"] 	= time() + 2;
				forEach ($this->spam as $key => $value){
					if ($value > 0) {
						$this->spam[$key] = $value - 10;
					} else {
						$this->spam[$key] = 0;
					}
				}
				
				forEach ($this->_2sec as $filename) {
					include $filename;
				}
				break;
			case $this->vars["1min"] < time();
				Logger::log('DEBUG', 'Cron', "1min");
				forEach ($this->largespam as $key => $value){
					if ($value > 0) {
						$this->largespam[$key] = $value - 1;
					} else {
						$this->largespam[$key] = 0;
					}
				}
				
				$this->vars["1min"] = time() + 60;
				forEach ($this->_1min as $filename) {
					include $filename;
				}
				break;
			case $this->vars["10mins"] < time();
				Logger::log('DEBUG', 'Cron', "10mins");
				$this->vars["10mins"] 	= time() + (60 * 10);
				forEach ($this->_10mins as $filename) {
					include $filename;
				}
				break;
			case $this->vars["15mins"] < time();
				Logger::log('DEBUG', 'Cron', "15mins");
				$this->vars["15mins"] 	= time() + (60 * 15);
				forEach ($this->_15mins as $filename) {
					include $filename;
				}
				break;
			case $this->vars["30mins"] < time();
				Logger::log('DEBUG', 'Cron', "30mins");
				$this->vars["30mins"] 	= time() + (60 * 30);
				forEach ($this->_30mins as $filename) {
					include $filename;
				}
				break;
			case $this->vars["1hour"] < time();
				Logger::log('DEBUG', 'Cron', "1hour");
				$this->vars["1hour"] 	= time() + (60 * 60);
				forEach ($this->_1hour as $filename) {
					include $filename;
				}
				break;
			case $this->vars["24hours"] < time();
				Logger::log('DEBUG', 'Cron', "24hours");
				$this->vars["24hours"] 	= time() + ((60 * 60) * 24);
				forEach ($this->_24hrs as $filename) {
					include $filename;
				}
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
			Logger::log('ERROR', 'Core', "Warning: $filename does not match the nameconvention(All php files needs to be in lowercases except loading files)!");
			return false;
		}
	}
}
?>