<?php
/**
 * Authors:
 *  - Tyrence
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'tellrelay',
 *		accessLevel = 'mod',
 *		description = 'Convenience command to quickly set up org relay over tells between two orgs',
 *		help        = 'tellrelay.txt'
 *	)
 *  @DefineCommand(
 *		command     = 'grc',
 *		accessLevel = 'all',
 *		description = 'Relays incoming messages to guildchat',
 *		channels    = 'msg'
 *	)
 */
class RelayController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $setting;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $alts;
	
	/** @Inject */
	public $preferences;
	
	/** @Inject */
	public $playerManager;
	
	/** @Logger */
	public $logger;

	/** @Setup */
	public function setup() {
		$this->setting->add($this->moduleName, "relaytype", "Type of relay", "edit", "options", "1", "tell;private channel", '1;2', "mod");
		$this->setting->add($this->moduleName, "relaysymbol", "Symbol for external relay", "edit", "options", "@", "!;#;*;@;$;+;-;Always relay", '', "mod");
		$this->setting->add($this->moduleName, "relaybot", "Bot for Guildrelay", "edit", "text", "Off", "Off", '', "mod", "relaybot.txt");
		$this->setting->add($this->moduleName, "bot_relay_commands", "Relay commands and results over the bot relay", "edit", "options", "0", "true;false", "1;0");
		$this->setting->add($this->moduleName, 'relay_color_guild', "Color of messages from relay to guild channel", 'edit', "color", "<font color='#C3C3C3'>");
		$this->setting->add($this->moduleName, 'relay_color_priv', "Color of messages from relay to private channel", 'edit', "color", "<font color='#C3C3C3'>");
		$this->setting->add($this->moduleName, 'relay_guild_abbreviation', 'Abbreviation to use for org name', 'edit', 'text', 'none', 'none');
	}
	
	/**
	 * @HandlesCommand("tellrelay")
	 * @Matches("/^tellrelay (.*)$/")
	 */
	public function tellrelayCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
			$sendto->reply($msg);
			return;
		}

		$this->setting->save('relaytype', 1);  // 1 for 'tell'
		$this->setting->save('relaysymbol', 'Always relay');
		$this->setting->save('relaybot', $name);

		$msg = "Relay set up successfully with <highlight>$name<end>.  Please issue command '/tell $name tellrelay <myname>' if not done so already to complete the setup.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("grc")
	 */
	public function grcCommand($message, $channel, $sender, $sendto, $args) {
		$this->processIncomingRelayMessage($sender, $message);
	}
	
	/**
	 * @Event("extPriv")
	 * @Description("Receive relay messages from other bots in the relay bot private channel")
	 */
	public function receiveRelayMessageExtPrivEvent($eventObj) {
		$this->processIncomingRelayMessage($eventObj->sender, $eventObj->message);
	}
	
	/**
	 * @Event("priv")
	 * @Description("Receive relay messages from other bots in this bot's own private channel")
	 */
	public function receiveRelayMessagePrivEvent($eventObj) {
		$this->processIncomingRelayMessage($eventObj->sender, $eventObj->message);
	}
	
	public function processIncomingRelayMessage($sender, $message) {
		if (($sender == ucfirst(strtolower($this->setting->get('relaybot'))) || $channel == ucfirst(strtolower($this->setting->get('relaybot')))) && preg_match("/^grc (.+)$/s", $message, $arr)) {
			$msg = $arr[1];
			$this->chatBot->sendGuild($setting->get('relay_color_guild') . $msg, true);

			if ($this->setting->get("guest_relay") == 1) {
				$this->chatBot->sendPrivate($this->setting->get('relay_color_priv') . $msg, true);
			}
		}
	}
	
	/**
	 * @Event("guild")
	 * @Description("Sends org chat to relay")
	 */
	public function orgChatToRelayEvent($eventObj) {
		$this->processOutgoingRelayMessage($eventObj->sender, $eventObj->message);
	}
	
	/**
	 * @Event("priv")
	 * @Description("Sends private channel chat to relay")
	 */
	public function privChatToRelayEvent($eventObj) {
		$this->processOutgoingRelayMessage($eventObj->sender, $eventObj->message);
	}
	
	public function processOutgoingRelayMessage($sender, $message) {
		if (($this->setting->get("relaybot") != "Off") && ($this->setting->get("bot_relay_commands") == 1 || $message[0] != $this->setting->get("symbol"))) {
			$relayMessage = '';
			if ($this->setting->get('relaysymbol') == 'Always relay') {
				$relayMessage = $message;
			} else if ($message[0] == $this->setting->get('relaysymbol')) {
				$relayMessage = substr($message, 1);
			}

			if ($relayMessage != '') {
				if (!$this->util->isValidSender($sender)) {
					$sender_link = '';
				} else {
					$sender_link = ' ' . $this->text->make_userlink($sender) . ':';
				}

				if ($type == "guild") {
					$msg = "grc [<myguild>]{$sender_link} {$relayMessage}";
				} else if ($type == "priv") {
					$msg = "grc [<myguild>] [Guest]{$sender_link} {$relayMessage}";
				}
				$this->send_message_to_relay($msg);
			}
		}
	}
	
	/**
	 * @Event("extJoinPrivRequest")
	 * @Description("Accept private channel join invitation from the relay bot")
	 */
	public function acceptPrivJoinEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->setting->get("relaytype") == 2 && strtolower($sender) == strtolower($this->setting->get("relaybot"))) {
			$this->chatBot->privategroup_join($sender);
		}
	}
	
	/**
	 * @Event("orgmsg")
	 * @Description("Relay Org Messages")
	 */
	public function relayOrgMessagesEvent($eventObj) {
		if ($this->setting->get("relaybot") != "Off") {
			$msg = "grc [<myguild>] {$eventObj->message}<end>";
			$this->send_message_to_relay($msg);
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Sends Logon messages over the relay")
	 */
	public function relayLogonMessagesEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->setting->get("relaybot") != "Off" && isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			$whois = $this->playerManager->get_by_name($sender);

			$msg = '';
			if ($whois === null) {
				$msg = "$sender logged on.";
			} else {
				$msg = $this->playerManager->get_info($whois);

				$msg .= " logged on.";

				$altInfo = $this->alts->get_alt_info($sender);
				if (count($altInfo->alts) > 0) {
					$msg .= " " . $altInfo->get_alts_blob(false, true);
				}

				$logon_msg = $this->preferences->get($sender, 'logon_msg');
				if ($logon_msg !== false && $logon_msg != '') {
					$msg .= " - " . $logon_msg;
				}
			}

			$this->send_message_to_relay("grc [<myguild>] ".$msg);
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Sends Logoff messages over the relay")
	 */
	public function relayLogoffMessagesEvent($eventObj) {
		$sender = $eventObj->sender;
		if ($this->setting->get("relaybot") != "Off" && isset($this->chatBot->guildmembers[$sender]) && $this->chatBot->is_ready()) {
			$this->send_message_to_relay("grc [<myguild>] <highlight>{$sender}<end> logged off");
		}
	}
	
	/**
	 * @Event("joinPriv")
	 * @Description("Sends a message to the relay when someone joins the private channel")
	 */
	public function relayJoinPrivMessagesEvent($eventObj) {
		if ($this->setting->get('relaybot') != 'Off') {
			$whois = $this->playerManager->get_by_name($sender);
			$altInfo = $this->alts->get_alt_info($sender);

			if ($whois !== null) {
				if (count($altInfo->alts) > 0) {
					$msg = $this->playerManager->get_info($whois) . " has joined the private channel. " . $altInfo->get_alts_blob(false, true);
				} else {
					$msg = $this->playerManager->get_info($whois) . " has joined the private channel.";
				}
			} else {
				if (count($altInfo->alts) > 0) {
					$msg .= "$sender has joined the private channel. " . $altInfo->get_alts_blob(false, true);
				} else {
					$msg = "$sender has joined the private channel.";
				}
			}

			$this->send_message_to_relay("grc [<myguild>] " . $msg);
		}
	}
	
	/**
	 * @Event("leavePriv")
	 * @Description("Sends a message to the relay when someone leaves the private channel")
	 */
	public function relayLeavePrivMessagesEvent($eventObj) {
		if ($this->setting->get('relaybot') != 'Off') {
			$msg = "<highlight>{$sender}<end> has left the private channel.";
			$this->send_message_to_relay("grc [<myguild>] " . $msg);
		}
	}
	
	function send_message_to_relay($message) {
		$relayBot = $this->setting->get('relaybot');
		$message = str_ireplace("<myguild>", $this->getGuildAbbreviation(), $message);

		// since we are using the aochat methods, we have to call format_message manually to handle colors and bot name replacement
		$message = $this->text->format_message($message);

		// we use the aochat methods so the bot doesn't prepend default colors
		if ($this->setting->get('relaytype') == 2) {
			$this->chatBot->send_privgroup($relayBot, $message);
		} else if ($this->setting->get('relaytype') == 1) {
			$this->chatBot->send_tell($relayBot, $message);

			// manual logging is only needed for tell relay
			$this->logger->log_chat("Out. Msg.", $relayBot, $message);
		}
	}

	function getGuildAbbreviation() {
		if ($this->setting->get('relay_guild_abbreviation') != 'none') {
			return $this->setting->get('relay_guild_abbreviation');
		} else {
			return $chatBot->vars["my_guild"];
		}
	}
}

