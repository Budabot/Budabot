<?php
/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'ban',
 *		accessLevel   = 'mod',
 *		description   = 'Ban a player from this bot',
 *		help          = 'ban.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'banlist',
 *		accessLevel   = 'mod',
 *		description   = 'Shows who is on the banlist',
 *		help          = 'ban.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'banorg',
 *		accessLevel   = 'mod',
 *		description   = 'Ban an organization from this bot',
 *		help          = 'ban.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'unban',
 *		accessLevel   = 'mod',
 *		description   = 'Unban a player from this bot',
 *		help          = 'ban.txt',
 *		defaultStatus = '1'
 *	)
 *	@DefineCommand(
 *		command       = 'unbanorg',
 *		accessLevel   = 'mod',
 *		description   = 'Unban an organization from this bot',
 *		help          = 'ban.txt',
 *		defaultStatus = '1'
 *	)
 */
class BanController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $banManager;

	/** @Inject */
	public $accessManager;

	/** @Inject */
	public $util;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $settingManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $eventManager;

	/** @Inject */
	public $db;

	/**
	 * @Setting("notify_banned_player")
	 * @Description("Notify player when banned from bot")
	 * @Visibility("edit")
	 * @Type("options")
	 * @Options("true;false")
	 * @Intoptions("1;0")
	 * @AccessLevel("mod")
	 */
	public $defaultNotifyBannedPlayer = "1";

	/**
	 * @Setup
	 * This handler is called on bot startup.
	 */
	public function setup() {
		$this->db->exec("CREATE TABLE IF NOT EXISTS banlist_<myname> (name VARCHAR(25) NOT NULL PRIMARY KEY, admin VARCHAR(25), time INT, reason TEXT, banend INT)");
		$this->banManager->upload_banlist();
		$this->eventManager->activate('1min', 'BanController.checkTempBan');
	}

	/**
	 * This command handler bans a player from this bot.
	 *
	 * Command parameters are:
	 *  - name of the player
	 *  - time of ban
	 *  - banning reason string
	 *
	 * @HandlesCommand("ban")
	 * @Matches("/^ban ([a-z0-9-]+) ([a-z0-9]+) (for|reason) (.+)$/i")
	 */
	public function banPlayerWithTimeAndReasonCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$length = $this->util->parseTime($args[2]);
		$reason = $args[4];
	
		if (!$result = $this->banPlayer($who, $sender, $length, $reason, $sendto)) {
			return $result;
		}

		$timeString = $this->util->unixtime_to_readable($length);
		$sendto->reply("You have banned <highlight>$who<end> from this bot for $timeString.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been banned from this bot by <highlight>$sender<end> for $timeString. Reason: $reason", $who);
		}
	}

	/**
	 * This command handler bans a player from this bot.
	 *
	 * Command parameters are:
	 *  - name of the player
	 *  - time of ban
	 *
	 * @HandlesCommand("ban")
	 * @Matches("/^ban ([a-z0-9-]+) ([a-z0-9]+)$/i")
	 */
	public function banPlayerWithTimeCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$length = $this->util->parseTime($args[2]);
	
		if (!$result = $this->banPlayer($who, $sender, $length, '', $sendto)) {
			return $result;
		}
	
		$timeString = $this->util->unixtime_to_readable($length);
		$sendto->reply("You have banned <highlight>$who<end> from this bot for $timeString.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been banned from this bot by <highlight>$sender<end> for $timeString.", $who);
		}
	}

	/**
	 * This command handler bans a player from this bot.
	 *
	 * Command parameters are:
	 *  - name of the player
	 *  - banning reason string
	 *
	 * @HandlesCommand("ban")
	 * @Matches("/^ban ([a-z0-9-]+) (for|reason) (.+)$/i")
	 */
	public function banPlayerWithReasonCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
		$reason = $args[3];
	
		if (!$result = $this->banPlayer($who, $sender, null, $reason, $sendto)) {
			return $result;
		}
	
		$sendto->reply("You have permanently banned <highlight>$who<end> from this bot.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been permanently banned from this bot by <highlight>$sender<end>. Reason: $reason", $who);
		}
	}

	/**
	 * This command handler bans a player from this bot.
	 *
	 * Command parameter is:
	 *  - name of the player
	 *
	 * @HandlesCommand("ban")
	 * @Matches("/^ban ([a-z0-9-]+)$/i")
	 */
	public function banPlayerCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));

		if (!$result = $this->banPlayer($who, $sender, null, '', $sendto)) {
			return $result;
		}
	
		$sendto->reply("You have permanently banned <highlight>$who<end> from this bot.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been permanently banned from this bot by <highlight>$sender<end>.", $who);
		}
	}

	/**
	 * This command handler shows who is on the banlist.
	 *
	 * @HandlesCommand("banlist")
	 * @Matches("/^banlist$/i")
	 */
	public function banlistCommand($message, $channel, $sender, $sendto, $args) {
		$banlist = $this->banManager->getBanlist();
		$count = count($banlist);

		if ($count == 0) {
		    $msg = "No one is currently banned from this bot.";
		} else {
			$blob = '';
			forEach ($banlist as $ban) {
				$blob .= "Name: <highlight>{$ban->name}<end>\n";
				$blob .= "<tab>Date: <highlight>" . $this->util->date($ban->time) . "<end>\n";
				$blob .= "<tab>By: <highlight>{$ban->admin}<end>\n";
				if ($ban->banend != 0) {
					$blob .= "<tab>Ban ends: <highlight>" . $this->util->unixtime_to_readable($ban->banend - time(), false) . "<end>\n";
				} else {
					$blob .= "<tab>Ban ends: <highlight>Never<end>\n";
				}
		
				if ($ban->reason != '') {
					$blob .= "<tab>Reason: <highlight>{$ban->reason}<end>\n";
				}
				$blob .= "\n";
			}
			$msg = $this->text->make_blob("Banlist ($count)", $blob);
		}
		$sendto->reply($msg);
	}

	/**
	 * This command handler unbans a player from this bot.
	 *
	 * Command parameter is:
	 *  - name of the player
	 *
	 * @HandlesCommand("unban")
	 * @Matches("/^unban (.+)$/i")
	 */
	public function unbanCommand($message, $channel, $sender, $sendto, $args) {
		$who = ucfirst(strtolower($args[1]));
	
		if (!$this->banManager->is_banned($who)) {
			$sendto->reply("<highlight>$who<end> is not banned on this bot.");
			return;
		}
	
		$this->banManager->remove($who);
	
		$sendto->reply("You have unbanned <highlight>$who<end> from this bot.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been unbanned from this bot by $sender.", $who);
		}
	}
	
	/**
	 * This command handler bans an organization from this bot.
	 *
	 * Command parameter is:
	 *  - name of the organization
	 *
	 * @HandlesCommand("banorg")
	 * @Matches("/^banorg (.+)$/i")
	 */
	public function banorgCommand($message, $channel, $sender, $sendto, $args) {
		$who = $args[1];
	
		if ($this->banManager->is_banned($who)) {
			$sendto->reply("The organization <highlight>$who<end> is already banned.");
			return;
		}
	
		$this->banManager->add($who, $sender, null, '');
	
		$sendto->reply("You have banned the organization <highlight>$who<end> from this bot.");
	}

	/**
	 * This command handler unbans an organization from this bot.
	 *
	 * Command parameter is:
	 *  - name of the organization
	 *
	 * @HandlesCommand("unbanorg")
	 * @Matches("/^unbanorg (.+)$/i")
	 */
	public function unbanorgCommand($message, $channel, $sender, $sendto, $args) {
		$who = $args[1];
	
		if (!$this->banManager->is_banned($who)) {
			$sendto->reply("The org <highlight>$who<end> is not banned on this bot.");
			return;
		}
	
		$this->banManager->remove($who);
	
		$sendto->reply("You have unbanned the org <highlight>$who<end> from this bot.");
	}

	/**
	 * This event handler is called every minute to check temp bans.
	 * Note: This handler is only activated and not registered.
	 */
	public function checkTempBan($eventObj) {
		$update = false;
		forEach ($this->banManager->getBanlist() as $ban){
			if ($ban->banend != 0 && ((time() - $ban->banend) >= 0)) {
				$update = true;
				$this->db->exec("DELETE FROM banlist_<myname> WHERE name = ?", $ban->name);
			}
		}

		if ($update) {
			$this->banManager->upload_banlist();
		}
	}

	/**
	 * This helper method bans player with given arguments.
	 */
	private function banPlayer($who, $sender, $length, $reason, $sendto) {
		if ($this->chatBot->get_uid($who) == NULL) {
			$sendto->reply("Player <highlight>$who<end> does not exist.");
			return;
		}

		if ($this->banManager->is_banned($who)) {
			$sendto->reply("Player <highlight>$who<end> is already banned.");
			return;
		}
	
		if ($this->accessManager->compareCharacterAccessLevels($sender, $who) <= 0) {
			$sendto->reply("You must have a higher access level than <highlight>$who<end> to perform this function.");
			return;
		}

		if ($length === 0) {
			return false;
		}

		$this->banManager->add($who, $sender, $length, $reason);
		return true;
	}
}

?>