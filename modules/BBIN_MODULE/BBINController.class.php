<?php

/**
 * Authors: 
 *	- Mindrila (RK1)
 *	- Legendadv (RK2)
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'startbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Connect to BBIN', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'stopbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Disconnect from BBIN', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'setbbin', 
 *		accessLevel = 'mod', 
 *		description = 'Configure BBIN settings', 
 *		help        = 'bbin_help.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'onlinebbin', 
 *		accessLevel = 'all', 
 *		description = 'View who is in IRC channel', 
 *		help        = 'bbin_help.txt'
 *	)
 */
class BBINController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Logger */
	public $logger;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $whitelist;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	private $bbinSocket;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		if ($this->setting->exists('bbin_channel')) {
			$channel = $this->setting->get('bbin_channel');
		} else {
			$channel = false;
		}
		if ($channel === false) {
			if ($this->chatBot->vars['my_guild'] == "") {
				$channel = "#".strtolower($this->chatBot->vars['name']);
			} else {
				if (strpos($this->chatBot->vars['my_guild']," ")) {
					$sandbox = explode(" ", $this->chatBot->vars['my_guild']);
					for ($i = 0; $i < count($sandbox); $i++) {
						$channel .= ucfirst(strtolower($sandbox[$i]));
					}
					$channel = "#".$channel;
				} else {
					$channel = "#".$this->chatBot->vars['my_guild'];
				}
			}
		}

		// Setup
		$this->db->loadSQLFile($this->moduleName, "bbin_chatlist");
		
		$this->setting->add($this->moduleName, "bbin_status", "Status of BBIN uplink", "noedit", "options", "0", "Offline;Online", "0;1", "mod", "bbin_help.txt");
		$this->setting->add($this->moduleName, "bbin_server", "IRC server to connect to", "noedit", "text", "irc.funcom.com", "", "", "mod", "bbin_help.txt");
		$this->setting->add($this->moduleName, "bbin_port", "IRC server port to use", "noedit", "number", "6667", "", "", "mod", "bbin_help.txt");
		$this->setting->add($this->moduleName, "bbin_nickname", "Nickname to use while in IRC", "noedit", "text", $this->chatBot->vars['name'], "", "", "mod", "bbin_help.txt");
		$this->setting->add($this->moduleName, "bbin_channel", "Channel to join", "noedit", "text", $channel, "", "", "mod", "bbin_help.txt");
		$this->setting->add($this->moduleName, "bbin_password", "IRC password to join channel", "edit", "text", "none", "none");
		
		$this->onlineController->register($this);
	}
	
	public function getOnlineList() {
		$blob = '';
		$numonline = 0;
		if ($this->setting->get("bbin_status") == 1) {
			
			
			// members
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 0) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinmembers = count($data);

			if ($numbbinmembers >= 1) {
				$blob .= "\n\n<header2>$numbbinmembers ".($numbbinmembers == 1 ? "Member":"Members")." in BBIN<end>\n";

				$blob .= $this->onlineController->createListByProfession($data, false, true);
			}

			// guests
			$data = $this->db->query("SELECT * FROM bbin_chatlist_<myname> WHERE (`guest` = 1) {$prof_query} ORDER BY `profession`, `level` DESC");
			$numbbinguests = count($data);

			if ($numbbinguests >= 1) {
				$blob .= "\n\n<header2>$numbbinguests ".($numbbinguests == 1 ? "Guest":"Guests")." in BBIN<end>\n";

				$blob .= $this->onlineController->createListByProfession($data, false, true);
			}

			$numonline = $numbbinguests + $numbbinmembers;
		}
		return array($numonline, $blob);
	}
	
	/**
	 * @HandlesCommand("startbbin")
	 * @Matches("/^startbbin$/i")
	 */
	public function startBBINCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->setting->get('bbin_server') == "") {
			$sendto->reply("The BBIN <highlight>server address<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.");
			return;
		}
		if ($this->setting->get('bbin_port') == "") {
			$sendto->reply("The BBIN <highlight>server port<end> seems to be missing. <highlight>/tell <myname> <symbol>help bbin<end> for details on setting this.");
			return;
		}

		$sendto->reply("Intializing BBIN connection. Please wait...");
		if ($this->bbinConnect()) {
			$sendto->reply("Finished connecting to BBIN.");
		} else {
			$sendto->reply("Error connectiong to BBIN.");
		}
	}
	
	/**
	 * @HandlesCommand("stopbbin")
	 * @Matches("/^stopbbin$/i")
	 */
	public function stopBBINCommand($message, $channel, $sender, $sendto, $args) {
		$this->setting->save("bbin_status", "0");

		if (!IRC::isConnectionActive($this->bbinSocket)) {
			$sendto->reply("There is no active BBIN connection.");
		} else {
			IRC::disconnect($this->bbinSocket);
			$this->logger->log('INFO', "Disconnected from BBIN");
			$sendto->reply("The BBIN connection has been disconnected.");
		}
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin server (.+)$/i")
	 */
	public function setBBINServerCommand($message, $channel, $sender, $sendto, $args) {
		$server = trim($args[1]);
		$this->setting->save("bbin_server", $server);
		$sendto->reply("Setting saved.  Bot will connect to IRC server: $server.");
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin port (.+)$/i")
	 */
	public function setBBINPortCommand($message, $channel, $sender, $sendto, $args) {
		$port = trim($args[1]);
		if (is_numeric($port)) {
			$this->setting->save("bbin_port", $port);
			$sendto->reply("Setting saved.  Bot will use port {$port} to connect to the IRC server.");
		} else {
			$sendto->reply("The port should be a number.");
		}
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin nickname (.+)$/i")
	 */
	public function setBBINNicknameCommand($message, $channel, $sender, $sendto, $args) {
		$nickname = trim($args[1]);
		$this->setting->save("bbin_nickname", $nickname);
		$sendto->reply("Setting saved.  Bot will use $nickname as its nickname while in IRC.");
	}
	
	/**
	 * @HandlesCommand("setbbin")
	 * @Matches("/^setbbin channel (.+)$/i")
	 */
	public function setBBINChannelCommand($message, $channel, $sender, $sendto, $args) {
		$channel = trim($args[1]);
		if (strpos($channel, " ") !== false) {
			$msg = "IRC channels cannot have spaces in them";
		} else {
			if (strpos($channel, "#") === false) {
				$channel = "#" . $channel;
			}
			$this->setting->save("bbin_channel", $channel);
			$msg = "Setting saved.  Bot will join $channel when it connects to IRC.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("onlinebbin")
	 * @Matches("/^onlinebbin$/i")
	 */
	public function onlineBBINCommand($message, $channel, $sender, $sendto, $args) {
		if (!IRC::isConnectionActive($this->bbinSocket)) {
			$msg = "There is no active IRC connection.";
		} else {
			$names = IRC::getUsersInChannel($this->bbinSocket, $this->setting->get('bbin_channel'));
			$numusers = count($names);
			$blob = '';
			forEach ($names as $value) {
				$blob .= "$value\n";
			}

			$msg = $this->text->make_blob("BBIN Online ($numusers)", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * @Event("1min")
	 * @Description("Automatically reconnect to IRC server")
	 * @DefaultStatus("0")
	 */
	public function autoReconnectEvent($eventObj) {
		if ($this->setting->get('bbin_status') == '1' && !IRC::isConnectionActive($this->bbinSocket)) {
			$this->bbinConnect();
		}
	}
	
	/**
	 * @Event("2sec")
	 * @Description("The main BBIN message loop")
	 */
	public function checkForBBINMessagesEvent($eventObj) {
		if (!IRC::isConnectionActive($this->bbinSocket)) {
			return;
		}

		if ($data = fgets($this->bbinSocket)) {
			$ex = explode(' ', $data);
			$this->logger->log('DEBUG', trim($data));
			$channel = rtrim(strtolower($ex[2]));
			$nicka = explode('@', $ex[0]);
			$nickb = explode('!', $nicka[0]);
			$nickc = explode(':', $nickb[0]);

			$host = $nicka[1];
			$nick = $nickc[1];
			if ($ex[0] == "PING") {
				fputs($this->bbinSocket, "PONG ".$ex[1]."\n");
				$this->logger->log('DEBUG', "PING received. PONG sent.");
			} else if ($ex[1] == "NOTICE") {
				if (false != stripos($data, "exiting")) {
					// the irc server shut down (i guess)
					// send notification to channel
					$extendedinfo = $this->text->make_blob("Extended information", $data);
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[BBIN]<end> Lost connection with server:".$extendedinfo, true);
					}
				}
			} else if ("KICK" == $ex[1]) {
				$extendedinfo = $this->text->make_blob("Extended information", $data);
				if ($ex[3] == $this->setting->get('bbin_nickname')) {
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[BBIN]<end> Our uplink was kicked from the server:".$extendedinfo, true);
					}
				} else {
					// yay someone else was kicked
					$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$ex[3]'");
					if ($this->chatBot->vars['my_guild'] != "") {
						$this->chatBot->sendGuild("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, true);
					}
					if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
						$this->chatBot->sendPrivate("<yellow>[BBIN]<end> The uplink ".$ex[3]." was kicked from the server:".$extendedinfo, true);
					}
				}
			} else if (($ex[1] == "QUIT") || ($ex[1] == "PART")) {
				$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
				if ($this->chatBot->vars['my_guild'] != "") {
					$this->chatBot->sendGuild("<yellow>[BBIN]<end> Lost uplink with $nick", true);
				}
				if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
					$this->chatBot->sendPrivate("<yellow>[BBIN]<end> Lost uplink with $nick", true);
				}
			} else if ($ex[1] == "JOIN") {
				if ($this->chatBot->vars['my_guild'] != "") {
					$this->chatBot->sendGuild("<yellow>[BBIN]<end> Uplink established with $nick.", true);
				}
				if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
					$this->chatBot->sendPrivate("<yellow>[BBIN]<end> Uplink established with $nick.", true);
				}
			} else if ("PRIVMSG" == $ex[1] && $channel == trim(strtolower($this->setting->get('bbin_channel')))) {
				// tweak the third message a bit to remove beginning ":"
				$ex[3] = substr($ex[3],1,strlen($ex[3]));
				for ($i = 3; $i < count($ex); $i++) {
					$bbinmessage .= rtrim(htmlspecialchars_decode($ex[$i]))." ";
				}

				$this->logger->log_chat("Inc. BBIN Msg.", $nick, $bbinmessage);
				$this->parse_incoming_bbin($bbinmessage, $nick);
			}
			unset($sandbox);
		}
	}
	
	/**
	 * @Event("priv")
	 * @Description("Relay (priv) messages to BBIN")
	 */
	public function relayPrivMessagesEvent($eventObj) {
		$message = $eventObj->message;
		$sender = $eventObj->sender;
		if (IRC::isConnectionActive($this->bbinSocket)) {
			// do not relay commands and ignored chars
			if ($message[0] != $this->setting->get("symbol")) {
				$outmsg = htmlspecialchars($message);

				$msg = "$sender: $message";
				$this->logger->log_chat("Out. BBIN Msg.", $sender, $msg);
				IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
			}
		}
	}
	
	/**
	 * @Event("guild")
	 * @Description("Relay (guild) messages to BBIN")
	 */
	public function relayGuildMessagesEvent($eventObj) {
		$message = $eventObj->message;
		$sender = $eventObj->sender;
		if (IRC::isConnectionActive($this->bbinSocket)) {
			// do not relay commands and ignored chars
			if ($message[0] != $this->setting->get("symbol")) {
				$outmsg = htmlspecialchars($message);

				$msg = "$sender: $message";
				$this->logger->log_chat("Out. BBIN Msg.", $sender, $msg);
				IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
			}
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends joined channel messages")
	 */
	public function joinPrivEvent($eventObj) {
		if (IRC::isConnectionActive($this->bbinSocket)) {
			$msg = "[BBIN:LOGON:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",1]";
			$this->logger->log('DEBUG', $msg);
			IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Shows a logon from a member")
	 */
	public function logonEvent($eventObj) {
		if (IRC::isConnectionActive($this->bbinSocket) && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = "[BBIN:LOGON:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",0]";
			$this->logger->log('DEBUG', $msg);
			IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
		}
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Sends left channel messages")
	 */
	public function leavePrivEvent($eventObj) {
		if (IRC::isConnectionActive($this->bbinSocket)) {
			$msg = "[BBIN:LOGOFF:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",1]";
			$this->logger->log('DEBUG', $msg);
			IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Shows a logoff from a member")
	 */
	public function logoffEvent($eventObj) {
		if (IRC::isConnectionActive($this->bbinSocket) && isset($this->chatBot->guildmembers[$eventObj->sender])) {
			$msg = "[BBIN:LOGOFF:".$eventObj->sender.",".$this->chatBot->vars["dimension"].",0]";
			$this->logger->log('DEBUG', $msg);
			IRC::send($this->bbinSocket, $this->setting->get('bbin_channel'), $msg);
		}
	}

	/*
	 * This is the main parse function for incoming messages other than
	 * IRC related stuff
	 */
	public function parse_incoming_bbin($bbinmsg, $nick) {
		if (preg_match("/^\[BBIN:LOGON:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
			// a user logged on somewhere in the network
			// first argument is name, second is dimension, third indicates a guest
			$name = $arr[1];
			$dimension = $arr[2];
			$isguest = $arr[3];

			// get character information
			$character = $this->playerManager->get_by_name($name, $dimension);

			// add user to bbin_chatlist_<myname>
			$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
				"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$this->db->exec($sql, $name, $isguest, $nick, $character->faction, $character->profession, $character->guild, $character->breed, $character->level, $character->ai_level, $dimension, '');

			// send notification to channels
			$msg = "<highlight>$name<end> (<highlight>{$character->level}<end>/<green>{$character->ai_level}<end>, <highlight>{$character->profession}<end>, {$character->faction})";
			if ($character->guild != "") {
				$msg .=	" {$character->guild_rank} of {$character->guild}";
			}
			$msg .= " has joined the network";
			if ($isguest == 1) {
				$msg .= " as a guest";
			}
			$msg .= ".";

			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $msg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $msg", true);
			}

		} else if (preg_match("/^\[BBIN:LOGOFF:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
			// a user logged off somewhere in the network
			$name = $arr[1];
			$dimension = $arr[2];
			$isguest = $arr[3];

			// delete user from bbin_chatlist table
			$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE (`name` = ?) AND (`dimension` = ?) AND (`ircrelay` = ?)", $name, $dimension, $nick);

			// send notification to channels
			$msg = "";
			if ($isguest == 1) {
				$msg = "Our guest ";
			}
			$msg .= "<highlight>$name<end> has left the network.";


			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $msg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $msg", true);
			}

		} else if (preg_match("/^\[BBIN:SYNCHRONIZE\]/",$bbinmsg)) {
			// a new bot joined and requested a full online synchronization

			// send actual online members
			$msg = "[BBIN:ONLINELIST:".$this->chatBot->vars["dimension"].":";
			$data = $this->db->query("SELECT name FROM online WHERE channel_type = 'guild' AND added_by = '<myname>'");
			$numrows = count($data);
			forEach ($data as $row) {
				$msg .= $row->name . ",0,";
			}

			$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'priv' AND added_by = '<myname>'");
			$numrows += count($data);
			forEach ($data as $row) {
				$msg .= $row->name . ",1,";
			}
			if ($numrows != 0) {
				// remove trailing , if there is one
				$msg = substr($msg,0,strlen($msg)-1);
			}

			$msg .= "]";

			// send complete list back to bbin channel
			fputs($this->bbinSocket, "PRIVMSG ".$this->setting->get('bbin_channel')." :$msg\n");

		} else if (preg_match("/^\[BBIN:ONLINELIST:(.):(.*?)\]/", $bbinmsg, $arr)) {
			// received a synchronization list

			$this->db->begin_transaction();

			// delete all buddies from that nick
			$this->db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = ?", $nick);

			// Format: [BBIN:ONLINELIST:dimension:name,isguest,name,isguest....]
			$dimension = $arr[1];
			$listplode = explode(',', $arr[2]);

			// listplode should be: {name,isguest,name,isguest ...}
			while (true) {
				// as using array_pop will lead to null some time,
				// this loop will exit when all chars are parsed

				// pop last value off array (isguest of last member)
				$isguest = array_pop($listplode);

				// pop next value off array (name of last member)
				$name = array_pop($listplode);

				if ($isguest == null || $name == null) {
					// we popped all items of the array, break
					break;
				}

				// get character information
				$character = $this->playerManager->get_by_name($name, $dimension);

				// add user to bbin_chatlist_<myname>
				$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
					"VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->exec($sql, $name, $isguest, $nick, $character->faction, $character->profession, $character->guild, $character->breed, $character->level, $character->ai_level, $dimension, '');
			}

			$this->db->commit();
		} else {
			// normal message
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild("<yellow>[BBIN]<end> $bbinmsg", true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate("<yellow>[BBIN]<end> $bbinmsg", true);
			}
		}
	}

	public function bbinConnect() {
		IRC::connect(
			$this->bbinSocket,
			$this->setting->get('bbin_nickname'),
			$this->setting->get('bbin_server'),
			$this->setting->get('bbin_port'),
			$this->setting->get('bbin_password'),
			$this->setting->get('bbin_channel'));

		if (IRC::isConnectionActive($this->bbinSocket)) {
			$this->setting->save("bbin_status", "1");
			$this->db->exec("DELETE FROM bbin_chatlist_<myname>");
			fputs($this->bbinSocket, "PRIVMSG ".$this->setting->get('bbin_channel')." :[BBIN:SYNCHRONIZE]\n");
			$this->parse_incoming_bbin("[BBIN:SYNCHRONIZE]", '');
			return true;
		} else {
			return false;
		}
	}
}
