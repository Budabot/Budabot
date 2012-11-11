<?php

/** @Instance */
class Worldnet {
	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $db;

	/** @Inject */
	public $buddylistManager;

	/** @Inject */
	public $banManager;

	/** @Inject */
	public $help;

	/** @Inject */
	public $chatBot;

	/** @Logger */
	public $logger;

	public $moduleName;

	/**
	 * @Setup
	 */
	function setup() {
		// since settings for channels are added dynamically, we need to re-add them manually
		$data = $this->db->query("SELECT * FROM settings_<myname> WHERE module = ? AND name LIKE ?", $this->moduleName, "%_channel");
		forEach ($data as $row) {
			$this->settingManager->add($row->module, $row->name, $row->description, $row->mode, $row->type, $row->value, $row->options, $row->intoptions, $row->admin, $row->help);
		}

		$this->settingManager->add($this->moduleName, 'worldnet_bot', 'Name of bot', 'edit', "text", "Worldnet", "Worldnet;Dnet", '', 'mod', 'worldnet.txt');

		// colors
		$this->settingManager->add($this->moduleName, 'worldnet_channel_color', "Color of channel text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
		$this->settingManager->add($this->moduleName, 'worldnet_message_color', "Color of message text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
		$this->settingManager->add($this->moduleName, 'worldnet_sender_color', "Color of sender text in worldnet messages", 'edit', "color", "<font color='#FFFFFF'>");
	}

	/**
	 * @Event("logOn")
	 * @Description("Requests invite from worldnet bot")
	 */
	function logon($eventObj) {
		if (strtolower($this->settingManager->get('worldnet_bot')) == strtolower($eventObj->sender)) {
			$msg = "!join";
			$this->logger->log_chat("Out. Msg.", $eventObj->sender, $msg);
			$this->chatBot->send_tell($eventObj->sender, $msg);
		}
	}

	/**
	 * @Event("connect")
	 * @Description("Adds worldnet bot to buddylist")
	 */
	function connect($eventObj) {
		$this->buddylistManager->add($this->settingManager->get('worldnet_bot'), 'worldnet');
	}

	/**
	 * @Event("extJoinPrivRequest")
	 * @Description("Accepts invites from worldnet bot")
	 */
	function acceptInvite($eventObj) {
		if (strtolower($this->settingManager->get('worldnet_bot')) == strtolower($eventObj->sender)) {
			$this->chatBot->privategroup_join($eventObj->sender);
		}
	}

	/**
	 * @Event("extPriv")
	 * @Description("Relays incoming messages to the guild/private channel")
	 */
	function incomingMessage($eventObj) {
		$sender = $eventObj->sender;
		$message = $eventObj->message;

		if (strtolower($this->settingManager->get('worldnet_bot')) != strtolower($sender)) {
			return;
		}

		$message = preg_replace("/<font(.+?)>/s", "", $message);
		$message = preg_replace("/<\/font>/s", "", $message);

		if (!preg_match("/\\[([^ ]+)\\] (.*) \\[([a-z0-9-]+)\\]$/i", $message, $arr)) {
			return;
		}

		$worldnetChannel = $arr[1];
		$messageText = $arr[2];
		$name = $arr[3];

		$channelSetting = strtolower($sender . '_' . $worldnetChannel . '_channel');
		if ($this->settingManager->get($channelSetting) === false) {
			$this->settingManager->add('WORLDNET_MODULE', $channelSetting, "Channel $worldnetChannel status", "edit", "options", "1", "true;false", "1;0");
		}

		if ($this->banManager->is_banned($name)) {
			return;
		}

		$channelColor = $this->settingManager->get('worldnet_channel_color');
		$messageColor = $this->settingManager->get('worldnet_message_color');
		$senderColor = $this->settingManager->get('worldnet_sender_color');
		$msg = "$sender: [{$channelColor}$worldnetChannel<end>] {$messageColor}{$messageText}<end> [{$senderColor}{$name}<end>]";

		if ($this->settingManager->get($channelSetting) == 1) {
			// only send to guild or priv if the channel is enabled on the bot,
			// but don't restrict tell subscriptions
			if ($this->settingManager->get('broadcast_to_guild') == 1) {
				$this->chatBot->sendGuild($msg, true);
			}
			if ($this->settingManager->get('broadcast_to_privchan') == 1) {
				$this->chatBot->sendPrivate($msg, true);
			}
		}
	}
}

?>
