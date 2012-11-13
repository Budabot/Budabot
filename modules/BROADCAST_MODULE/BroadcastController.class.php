<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'broadcast', 
 *		accessLevel = 'mod', 
 *		description = 'View/edit the broadcast bots list', 
 *		help        = 'broadcast.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'dnet', 
 *		accessLevel = 'mod', 
 *		description = 'Enable/disable Dnet support (RK 1 only)', 
 *		help        = 'dnet.txt'
 *	)
 */
class BroadcastController {

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
	public $settingManager;
	
	/** @Inject */
	public $whitelist;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	private $broadcastList = array();
	private $dnetBot = "Dnetorg";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'broadcast');
		
		$this->loadBroadcastListIntoMemory();
		
		$this->settingManager->add($this->moduleName, "broadcast_to_guild", "Send broadcast message to guild channel", "edit", "options", "1", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "broadcast_to_privchan", "Send broadcast message to private channel", "edit", "options", "0", "true;false", "1;0");
		$this->settingManager->add($this->moduleName, "dnet_status", "Enable Dnet support", "noedit", "options", "0", "true;false", "1;0");
	}
	
	private function loadBroadcastListIntoMemory() {
		//Upload broadcast bots to memory
		$data = $this->db->query("SELECT * FROM broadcast_<myname>");
		$this->broadcastList = array();
		forEach ($data as $row) {
			$this->broadcastList[$row->name] = $row;
		}
	}
	
	/**
	 * @HandlesCommand("broadcast")
	 * @Matches("/^broadcast$/i")
	 */
	public function broadcastListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';

		$sql = "SELECT * FROM broadcast_<myname> ORDER BY dt DESC";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$remove = $this->text->make_chatcmd('Remove', "/tell <myname> <symbol>broadcast rem $row->name");
			$dt = $this->util->date($row->dt);
			$blob .= "<highlight>{$row->name}<end> [added by {$row->added_by}] {$dt} {$remove}\n";
		}

		if (count($data) == 0) {
			$msg = "No bots are on the broadcast list.";
		} else {
			$msg = $this->text->make_blob('Broadcast Bots', $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("broadcast")
	 * @Matches("/^broadcast add (.+)$/i")
	 */
	public function broadcastAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));

		$charid = $this->chatBot->get_uid($name);
		if ($charid == false) {
			$sendto->reply("'$name' is not a valid character name.");
			return;
		}

		if (isset($this->broadcastList[$name])) {
			$sendto->reply("'$name' is already on the broadcast bot list.");
			return;
		}

		$this->db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $name, $sender, time());
		$msg = "Broadcast bot added successfully.";

		// reload broadcast bot list
		$this->loadBroadcastListIntoMemory();

		$this->whitelist->add($name, $sender . " (bot)");

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("broadcast")
	 * @Matches("/^broadcast (rem|remove) (.+)$/i")
	 */
	public function broadcastRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[2]));

		if (!isset($this->broadcastList[$name])) {
			$sendto->reply("'$name' is not on the broadcast bot list.");
			return;
		}

		$this->db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $name);
		$msg = "Broadcast bot removed successfully.";

		// reload broadcast bot list
		$this->loadBroadcastListIntoMemory();

		$this->whitelist->remove($name);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("dnet")
	 * @Matches("/^dnet (enable|on|add)/i")
	 */
	public function dnetEnableCommand($message, $channel, $sender, $sendto, $args) {
		if (!isset($this->broadcastList[$this->dnetBot])) {
			$this->settingManager->save('dnet_status', 1);
			$this->db->query("INSERT INTO broadcast_<myname> (`name`, `added_by`, `dt`) VALUES (?, ?, ?)", $this->dnetBot, $sender, time());
			$this->whitelist->add($this->dnetBot, $sender . " (broadcast bot)");

			// reload broadcast bot list
			$this->loadBroadcastListIntoMemory();

			$msg = "!join";
			$this->logger->log_chat("Out. Msg.", $this->dnetBot, $msg);
			$this->chatBot->send_tell($this->dnetBot, $msg);
		}

		$msg = "Dnet support has been <green>enabled<end>.";
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("dnet")
	 * @Matches("/^dnet (disable|off|rem|remove)$/i")
	 */
	public function dnetDisableCommand($message, $channel, $sender, $sendto, $args) {
		$this->settingManager->save('dnet_status', 0);
		$this->db->exec("DELETE FROM broadcast_<myname> WHERE name = ?", $this->dnetBot);
		$this->whitelist->remove($this->dnetBot);

		// reload broadcast bot list
		$this->loadBroadcastListIntoMemory();

		$this->chatBot->privategroup_leave($this->dnetBot);

		$msg = "Dnet support has been <orange>disabled<end>.";
		$sendto->reply($msg);
	}

	/**
	 * @Event("msg")
	 * @Description("Relays incoming messages to the guild/private channel")
	 */
	public function incomingMessageEvent($eventObj) {
		$this->processIncomingMessage($eventObj->sender, $eventObj->message);
	}
	
	/**
	 * @Event("extPriv")
	 * @Description("Relays incoming private channel messages to the guild/private channel")
	 */
	public function incomingPrivateChannelMessageEvent($eventObj) {
		$this->processIncomingMessage($eventObj->sender, $eventObj->message);
	}
	
	/**
	 * @Event("extjoinprivrequest")
	 * @Description("Accepts private channel invites from broadcast bots")
	 */
	public function incomingPrivateChannelJoinEvent($eventObj) {
		if (isset($this->broadcastList[$eventObj->sender])) {
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

	private function processIncomingMessage($sender, $message) {
		if (isset($this->broadcastList[$sender])) {
			$msg = "[$sender]: $message";

			if ($this->settingManager->get('broadcast_to_guild')) {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->settingManager->get('broadcast_to_privchan')) {
				$this->chatBot->sendPrivate($msg, true);
			}

			// keeps the bot from sending a message back to the neutnet satellite bot
			throw new StopExecutionException();
		}
	}
}
