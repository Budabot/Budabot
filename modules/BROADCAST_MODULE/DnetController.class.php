<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'dnet', 
 *		accessLevel = 'mod', 
 *		description = 'Enable/disable Dnet support (RK 1 only)', 
 *		help        = 'dnet.txt'
 *	)
 */
class DnetController {

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
	public $settingManager;
	
	/** @Inject */
	public $banManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $broadcastController;
	
	/** @Logger */
	public $logger;
	
	private $dnetBot = "Dnetorg";
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->settingManager->add($this->moduleName, "dnet_status", "Enable Dnet support", "noedit", "options", "0", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_pvp", "Enable Dnet PVP Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_pvm", "Enable Dnet PVM Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_event", "Enable Dnet Event Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_shopping", "Enable Dnet Shopping Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_lootrights", "Enable Dnet Lootrights Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_general", "Enable Dnet General Channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_channel_announce", "Enable Dnet Announce Channel", "noedit", "options", "1", "true;false", "1;0");
	}

	/**
	 * @HandlesCommand("dnet")
	 * @Matches("/^dnet (enable|on|add)/i")
	 */
	public function dnetEnableCommand($message, $channel, $sender, $sendto, $args) {
		$this->settingManager->save('dnet_status', 1);

		$msg = "!join";
		$this->logger->log_chat("Out. Msg.", $this->dnetBot, $msg);
		$this->chatBot->send_tell($this->dnetBot, $msg);

		$msg = "Dnet support has been <green>enabled<end>.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("dnet")
	 * @Matches("/^dnet (disable|off|rem|remove)$/i")
	 */
	public function dnetDisableCommand($message, $channel, $sender, $sendto, $args) {
		$this->settingManager->save('dnet_status', 0);

		$this->chatBot->privategroup_leave($this->dnetBot);

		$msg = "Dnet support has been <orange>disabled<end>.";
		$sendto->reply($msg);
	}
	
	/**
	 * @Event("extPriv")
	 * @Description("Relays incoming private channel messages to the guild/private channel")
	 */
	public function incomingPrivateChannelMessageEvent($eventObj) {
		if ($this->isDnetBot($eventObj)) {
			$rawmsg = $this->util->stripColors($eventObj->message);
			if (preg_match_all("/\\[([^\\]]+)\\] (.+?) \\[([^\\]]+)\\]/s", $rawmsg, $arr, PREG_SET_ORDER) > 0) {
			} else {
				$this->logger->log("WARN", "Invalid Dnet message format: $rawmsg");
			}

			forEach ($arr as $entry) {
				$channel = $entry[1];
				$text = $entry[2];
				$sender = $entry[3];

				if ($this->banManager->is_banned($sender)) {
					continue;
				}

				if (!$this->isChannelEnabled($channel)) {
					continue;
				}
				
				$spamMessage = "[<highlight>$channel<end>] $text [<highlight>$sender<end>]";
				$this->broadcastController->processIncomingMessage("Dnet", $spamMessage);
			}
		}
	}
	
	/**
	 * @Event("extjoinprivrequest")
	 * @Description("Accepts private channel invite from Dnet")
	 */
	public function incomingPrivateChannelJoinEvent($eventObj) {
		if ($this->isDnetBot($eventObj)) {
			$this->chatBot->privategroup_join($eventObj->sender);
		}
	}
	
	/**
	 * @Event("connect")
	 * @Description("Joins Dnet channel if enabled")
	 */
	public function joinDnetOnConnectEvent($eventObj) {
		if ($this->settingManager->get('dnet_status') == 1) {
			$msg = "!join";
			$this->logger->log_chat("Out. Msg.", $this->dnetBot, $msg);
			$this->chatBot->send_tell($this->dnetBot, $msg);
		}
	}
	
	private function isDnetBot($eventObj) {
		return $this->dnetBot == $eventObj->sender;
	}
	
	private function isChannelEnabled($channel) {
		$val = 0;
		switch ($channel) {
			case "PVP":
				$val = $this->settingManager->get("dnet_channel_pvp");
				break;
			case "PVM":
				$val = $this->settingManager->get("dnet_channel_pvm");
				break;
			case "Event":
				$val = $this->settingManager->get("dnet_channel_event");
				break;
			case "Shopping":
				$val = $this->settingManager->get("dnet_channel_shopping");
				break;
			case "Lootrights":
				$val = $this->settingManager->get("dnet_channel_lootrights");
				break;
			case "General":
				$val = $this->settingManager->get("dnet_channel_general");
				break;
			case "Announce":
				$val = $this->settingManager->get("dnet_channel_announce");
				break;
			default:
				$this->logger->log("WARN", "Unknown channel '$channel'");
				$val = 0;
		}
		return $val == 1;
	}
}
