<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'test', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testorgjoin', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testtowerattack', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'testtowervictory', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'showcmdregex', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'connectirc', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'disconnectirc', 
 *		accessLevel = 'admin', 
 *		description = "Test the bot commands", 
 *		help        = 'test.txt'
 *	)
 */
class TestController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $eventManager;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $accessLevel;

	/** @Inject */
	public $commandManager;

	/** @Inject */
	public $subcommandManager;
	
	/** @Logger */
	public $logger;
	
	private $irc;

	/**
	 * @Setup
	 */
	public function setup() {
		$this->path = getcwd() . "/modules/" . $this->moduleName . "/tests/";
	}

	/**
	 * @HandlesCommand("test")
	 * @Matches("/^test$/i")
	 */
	public function testCommand($message, $channel, $sender, $sendto, $args) {
		$type = "msg";
		$mockSendto = new MockCommandReply();
	
		$files = $this->util->getFilesInDirectory($this->path);
		forEach ($files as $file) {
			$lines = file($this->path . $file, FILE_IGNORE_NEW_LINES);
			forEach ($lines as $line) {
				if ($line[0] == "!") {
					$this->chatBot->sendTell($line, $sender);
					$line = substr($line, 1);
					$this->commandManager->process($type, $line, $sender, $sendto);
				}
			}
		}
	}
	
	/**
	 * @HandlesCommand("testorgjoin")
	 * @Matches("/^testorgjoin (.+)$/i")
	 */
	public function testorgjoinCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid('org msg');
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "$sender invited $args[1] to your organization.";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("testtowerattack")
	 * @Matches("/^testtowerattack (clan|neutral|omni) (.+) (.+) (clan|neutral|omni) (.+) (.+) (\d+) (\d+)$/i")
	 */
	public function testtowerattackCommand($message, $channel, $sender, $sendto, $args) {
		$eventObj = new stdClass;
		$eventObj->sender = -1;
		$eventObj->channel = "All Towers";
		$eventObj->message = "The $args[1] organization $args[2] just entered a state of war! $args[3] attacked the $args[4] organization $args[5]'s tower in $args[6] at location ($args[7],$args[8]).";
		$eventObj->type = 'towers';
		$this->eventManager->fireEvent($eventObj);
	}
	
	/**
	 * @HandlesCommand("testtowervictory")
	 * @Matches("/^testtowervictory (Clan|Neutral|Omni) (.+) (Clan|Neutral|Omni) (.+) (.+)$/i")
	 */
	public function testtowervictoryCommand($message, $channel, $sender, $sendto, $args) {
		$packet = new stdClass;
		$packet->type = AOCP_GROUP_MESSAGE;
		$packet->args = array();
		$packet->args[0] = $this->chatBot->get_gid('tower battle outcome');
		$packet->args[1] = (int)0xFFFFFFFF;
		$packet->args[2] = "The $args[1] organization $args[2] attacked the $args[3] $args[4] at their base in $args[5]. The attackers won!!";

		$this->chatBot->process_packet($packet);
	}
	
	/**
	 * @HandlesCommand("showcmdregex")
	 * @Matches("/^showcmdregex (.+)$/i")
	 */
	public function showcmdregexCommand($message, $channel, $sender, $sendto, $args) {
		$cmd = $args[1];
		
		// get all command handlers
		$handlers = $this->getAllCommandHandlers($cmd, $channel);
		
		// filter command handlers by access level
		$accessLevel = $this->accessLevel;
		$handlers = array_filter($handlers, function ($handler) use ($sender, $accessLevel) {
			return $accessLevel->checkAccess($sender, $handler->admin);
		});
		
		// get calls for handlers
		$calls = array_reduce($handlers, function ($handlers, $handler) {
			return array_merge($handlers, explode(',', $handler->file));
		}, array());

		// get regexes for calls
		$regexes = array();
		forEach ($calls as $call) {
			list($name, $method) = explode(".", $call);
			$instance = Registry::getInstance($name);
			try {
				$reflectedMethod = new ReflectionAnnotatedMethod($instance, $method);
				$regexes = array_merge($regexes, $this->commandManager->retrieveRegexes($reflectedMethod));
			} catch (ReflectionException $e) {
				continue;
			}
		}

		if (count($regexes) > 0) {
			$blob = '';
			forEach ($regexes as $regex) {
				$blob .= $regex . "\n";
			}
			$msg = $this->text->make_blob("Regexes for $cmd", $blob);
		} else {
			$msg = "No regexes found for command <highlight>$cmd<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("connectirc")
	 * @Matches("/^connectirc$/i")
	 */
	public function connectircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}
		
		$realname = 'Budabot - SmartIRC Client ' . SMARTIRC_VERSION;
		
		$bot = new IRCListener();
		//Registry::importChanges($bot);
		Registry::injectDependencies($bot);

		$this->irc = new Net_SmartIRC();
		$this->irc->setUseSockets(TRUE);
		$this->irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL, '', $bot, 'channelMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUERY, '', $bot, 'queryMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_JOIN, '', $bot, 'joinMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_PART, '', $bot, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_QUIT, '', $bot, 'leaveMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_KICK, '', $bot, 'kickMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_NAME, '', $bot, 'nameMessage');
		$this->irc->registerActionhandler(SMARTIRC_TYPE_NOTICE, '', $bot, 'noticeMessage');
		$this->irc->connect($this->setting->get('irc_server'), $this->setting->get('irc_port'));
		$this->irc->login($this->setting->get('irc_nickname'), $realname, 0, $this->setting->get('irc_password'));
		$this->irc->join(array($this->setting->get('irc_channel')));
		//$this->irc->setSenddelay(0);
		$this->irc->listen();
	}
	
	/**
	 * @HandlesCommand("disconnectirc")
	 * @Matches("/^disconnectirc$/i")
	 */
	public function disconnectircCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->irc != null) {
			$this->irc->disconnect();
			$this->irc = null;
		}
	}
	
	/**
	 * @Event("2s")
	 * @Description("Listen to IRC")
	 */
	public function checkForIRCEvent($eventObj) {
		if ($this->irc != null) {
			$this->irc->listen();
		}
	}
	
	public function getAllCommandHandlers($cmd, $channel) {
		$handlers = array();
		if (isset($this->commandManager->commands[$channel][$cmd])) {
			$handlers []= $this->commandManager->commands[$channel][$cmd];
		}
		if (isset($this->subcommandManager->subcommands[$cmd])) {
			forEach ($this->subcommandManager->subcommands[$cmd] as $handler) {
				if ($handler->type == $channel) {
					$handlers []= $handler;
				}
			}
		}
		return $handlers;
	}
}

class MockCommandReply implements CommandReply {
	public function reply($msg) {
		echo "got reply\n";
		//echo $msg . "\n";
	}
}

class IRCCommandReply2 implements CommandReply {
	private $irc;
	private $channel;
	private $type;

	public function __construct(&$irc, $channel, $type) {
		//var_dump($irc);
		$this->irc = $irc;
		$this->channel = $channel;
		$this->type = $type;
	}

	public function reply($msg) {
		$this->irc->message($this->type, $this->channel, strip_tags($msg));
	}
}

class IRCListener {
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $setting;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $onlineController;
	
	/** @Inject */
	public $commandManager;

	public function channelMessage(&$irc, &$obj) {
		$ircIgnore = explode(",", strtolower($this->setting->get('irc_ignore')));
		if (in_array(strtolower($obj->nick), $ircIgnore)) {
			return;
		}
		
		$msgColor = $this->setting->get('irc_message_color');
		$guildMsgColor = $this->setting->get('irc_guild_message_color');
		$guildNameColor = $this->setting->get('irc_guild_name_color');

		if ($obj->message == "!online") {
			$numguild = 0;

			$numguest = 0;
			//guild listing
			if ($this->chatBot->vars['my_guild'] != "") {
				$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'guild'");
				$numguild = count($data);
				if ($numguild != 0) {
					forEach ($data as $row) {
						switch ($row->afk) {
							case "kiting": $afk = " KITING"; break;
							case       "": $afk = ""; break;
							default      : $afk = " AFK"; break;
						}

						$row1 = $this->db->queryRow("SELECT * FROM alts WHERE `alt` = ?", $row->name);
						$list .= "$row->name".($row1 === null ? "":" ($row1->main)")."$afk, ";
						$g++;
					}
				}
			}
			//priv listing
			$data = $this->db->query("SELECT * FROM online WHERE channel_type = 'priv'");
			$numguest = count($data);
			if ($numguest != 0) {
				forEach ($data as $row) {
					switch ($row->afk) {
						case "kiting": $afk = " KITING"; break;
						case       "": $afk = ""; break;
						default      : $afk = " AFK"; break;
					}

					$row1 = $this->db->queryRow("SELECT * FROM alts WHERE `alt` = ?", $row->name);
					$list .= "$row->name".($row1 === null ? "":" ($row1->main)")."$afk, ";
					$p++;
				}
			}

			$membercount = "$numguild guildmembers and $numguest private chat members are online";
			$list = substr($list,0,-2);

			$irc->message($obj->type, $obj->channel, $membercount);
			$irc->message($obj->type, $obj->channel, $list);
		} else if ($obj->message[0] == $this->setting->get('symbol')) {
			$sendto = new IRCCommandReply2($irc, $obj->channel, $obj->type);
			$this->commandManager->process('msg', substr(rtrim($obj->message), 1), '', $sendto);
		} else {
			// handle relay messages from other bots
			if (preg_match("/" . chr(2) . chr(2) . chr(2) . "(.+)" . chr(2) . " (.+)/i", $obj->message, $arr)) {
				$ircmessage = "{$guildNameColor}{$arr[1]}<end> {$guildMsgColor}{$arr[2]}<end>";
			} else {
				$ircmessage = "<yellow>[IRC]<end> {$msgColor}{$obj->nick}: {$obj->message}<end>";
			}

			// handle item links from other bots
			$pattern = "/" . chr(3) . chr(3) . "(.+?)" . chr(3) . ' ' . chr(3) . "[(](.+?)id=([0-9]+)&amp;id2=([0-9]+)&amp;ql=([0-9]+)[)]" . chr(3) . chr(3) . "/";
			$replace = '<a href="itemref://\3/\4/\5">\1</a>';
			$ircmessage = preg_replace($pattern, $replace, $ircmessage);

			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($ircmessage, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($ircmessage, true);
			}
		}
	}
	
	public function queryMessage(&$irc, &$obj) {
		
	}
	
	public function joinMessage(&$irc, &$obj) {
		$this->onlineController->addPlayerToOnlineList($obj->nick, $obj->channel, 'irc');
		
		$msgColor = $this->setting->get('irc_message_color');
		$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick joined the channel.<end>";

		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function leaveMessage(&$irc, &$obj) {
		$this->onlineController->removePlayerFromOnlineList($obj->nick, 'irc');
		
		$msgColor = $this->setting->get('irc_message_color');
		$msg = "<yellow>[IRC]<end> {$msgColor}$obj->nick left the channel.<end>";
		
		if ($this->chatBot->vars['my_guild'] != "") {
			$this->chatBot->sendGuild($msg, true);
		}
		if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
			$this->chatBot->sendPrivate($msg, true);
		}
	}
	
	public function nameMessage(&$irc, &$obj) {
		print_r($obj);
		echo "nameMessage\n";
	}
	
	public function kickMessage(&$irc, &$obj) {
		$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
		if ($ex[3] == $this->setting->get('irc_nickname')) {
			$msg = "<yellow>[IRC]<end> Bot was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		} else {
			$msg = "<yellow>[IRC]<end> ".$ex[3]." was kicked from the server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
	
	public function noticeMessage(&$irc, &$obj) {
		if (false != stripos($obj->message, "exiting")) {
			// the irc server shut down (i guess)
			// send notification to channel
			$extendedinfo = $this->text->make_blob("Extended information", $obj->message);
			$msg = "<yellow>[IRC]<end> Lost connection with server:".$extendedinfo;
			if ($this->chatBot->vars['my_guild'] != "") {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->chatBot->vars['my_guild'] == "" || $this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	
		print_r($obj);
		echo "nameMessage\n";
	}

	function test(&$irc, &$obj) {
		$irc->message(SMARTIRC_TYPE_CHANNEL, $obj->channel, $obj->nick.': It works!');
	}

	function quit(&$irc, &$obj) {
		if ($obj->nick == "Tyrence") {
			$irc->disconnect();
		}
	}
}
