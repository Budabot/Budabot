<?php

namespace Budabot\Core\Modules;

/**
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command       = 'ban',
 *		accessLevel   = 'mod',
 *		description   = 'Ban a character from this bot',
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
 *		command       = 'unban',
 *		accessLevel   = 'mod',
 *		description   = 'Unban a character from this bot',
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
	public $db;

	private $banlist = array();

	/**
	 * @Setting("notify_banned_player")
	 * @Description("Notify character when banned from bot")
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
		$this->db->loadSQLFile($this->moduleName, "banlist");
	}

	/**
	 * @Event("connect")
	 * @Description("Upload banlist into memory")
	 * @DefaultStatus("1")
	 */
	public function initializeBanList($eventObj) {
		$this->uploadBanlist();
	}

	/**
	 * Command parameters are:
	 *  - name of the character
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

		$timeString = $this->util->unixtimeToReadable($length);
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
	
		$timeString = $this->util->unixtimeToReadable($length);
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
		$banlist = $this->getBanlist();
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
					$blob .= "<tab>Ban ends: <highlight>" . $this->util->unixtimeToReadable($ban->banend - time(), false) . "<end>\n";
				} else {
					$blob .= "<tab>Ban ends: <highlight>Never<end>\n";
				}
		
				if ($ban->reason != '') {
					$blob .= "<tab>Reason: <highlight>{$ban->reason}<end>\n";
				}
				$blob .= "\n";
			}
			$msg = $this->text->makeBlob("Banlist ($count)", $blob);
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

		$charId = $this->chatBot->get_uid($who);
		if (!$this->isBanned($charId)) {
			$sendto->reply("<highlight>$who<end> is not banned on this bot.");
			return;
		}
	
		$this->remove($charId);
	
		$sendto->reply("You have unbanned <highlight>$who<end> from this bot.");
		if ($this->settingManager->get('notify_banned_player') == 1) {
			$this->chatBot->sendTell("You have been unbanned from this bot by $sender.", $who);
		}
	}

	/**
	 * @Event("timer(1min)")
	 * @Description("Check temp bans to see if they have expired")
	 * @DefaultStatus("1")
	 */
	public function checkTempBan($eventObj) {
		$numRows = $this->db->exec("DELETE FROM banlist_<myname> WHERE banend != 0 AND banend < ?", time());

		if ($numRows > 0) {
			$this->uploadBanlist();
		}
	}

	/**
	 * This helper method bans player with given arguments.
	 */
	private function banPlayer($who, $sender, $length, $reason, $sendto) {
		$charId = $this->chatBot->get_uid($who);
		if ($charId == null) {
			$sendto->reply("Character <highlight>$who<end> does not exist.");
			return;
		}

		if ($this->isBanned($charId)) {
			$sendto->reply("Character <highlight>$who<end> is already banned.");
			return;
		}
	
		if ($this->accessManager->compareCharacterAccessLevels($sender, $who) <= 0) {
			$sendto->reply("You must have an access level higher than <highlight>$who<end> to perform this action.");
			return;
		}

		if ($length === 0) {
			return false;
		}

		$this->add($charId, $sender, $length, $reason);
		return true;
	}

	public function add($charId, $sender, $length, $reason) {

		if ($length == null) {
			$ban_end = "0";
		} else {
			$ban_end = time() + $length;
		}

		$sql = "INSERT INTO banlist_<myname> (`charid`, `admin`, `time`, `reason`, `banend`) VALUES (?, ?, ?, ?, ?)";
		$numrows = $this->db->exec($sql, $charId, $sender, time(), $reason, $ban_end);

		$this->uploadBanlist();

		return $numrows;
	}

	public function remove($charId) {
		$sql = "DELETE FROM banlist_<myname> WHERE charid = ?";
		$numrows = $this->db->exec($sql, $charId);

		$this->uploadBanlist();

		return $numrows;
	}

	public function uploadBanlist() {
		$this->banlist = array();

		$sql = "
			SELECT b.*, IFNULL(p.name, b.charid) AS name
			FROM banlist_<myname> b LEFT JOIN players p ON b.charid = p.charid";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$this->banlist[$row->charid] = $row;
		}
	}

	public function isBanned($charId) {
		return isset($this->banlist[$charId]);
	}

	public function getBanlist() {
		return $this->banlist;
	}
}
