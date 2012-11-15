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
			if (preg_match("~<a href=\"text://(.+)\">([^<]+)</a>~s", $eventObj->message, $arr)) {
				$messages = explode("\n", trim($arr[1]));
			} else {
				$messages = array($eventObj->message);
			}

			forEach ($messages as $msg) {
				$this->broadcastController->processIncomingMessage("Dnet", strip_tags($msg));
			}

			// keeps the bot from sending a message back to the neutnet satellite bot
			throw new StopExecutionException();
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
}
