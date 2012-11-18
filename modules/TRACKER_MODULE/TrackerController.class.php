<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'track', 
 *		accessLevel = 'all', 
 *		description = 'Show and manage trackers players', 
 *		help        = 'track.txt'
 *	)
 */
class TrackerController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $buddylistManager;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'tracked_users');
		$this->db->loadSQLFile($this->moduleName, 'tracking');
	}
	
	/**
	 * @Event("connect")
	 * @Description("Adds all players on the track list to the friendlist")
	 */
	public function trackedUsersConnectEvent($eventObj) {
		$sql = "SELECT name FROM tracked_users_<myname>";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$this->buddylistManager->add($row->name, 'tracking');
		}
	}
	
	/**
	 * @Event("logOn")
	 * @Description("Records a tracked user logging on")
	 */
	public function trackLogonEvent($eventObj) {
		if ($this->chatBot->is_ready()) {
			$uid = $this->chatBot->get_uid($eventObj->sender);
			$data = $this->db->query("SELECT * FROM tracked_users_<myname> WHERE uid = ?", $uid);
			if (count($data) > 0) {
				$this->db->exec("INSERT INTO tracking_<myname> (uid, dt, event) VALUES (?, ?, ?)", $uid, time(), 'logon');
			}
		}
	}
	
	/**
	 * @Event("logOff")
	 * @Description("Records a tracked user logging off")
	 */
	public function trackLogoffEvent($eventObj) {
		if ($this->chatBot->is_ready()) {
			$uid = $this->chatBot->get_uid($eventObj->sender);
			$data = $this->db->query("SELECT * FROM tracked_users_<myname> WHERE uid = ?", $uid);
			if (count($data) > 0) {
				$this->db->exec("INSERT INTO tracking_<myname> (uid, dt, event) VALUES (?, ?, ?)", $uid, time(), 'logoff');
			}
		}
	}

	/**
	 * @HandlesCommand("track")
	 * @Matches("/^track$/i")
	 */
	public function trackListCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT * FROM tracked_users_<myname> ORDER BY `name`");
		$numrows = count($data);
		if ($numrows != 0) {
			$blob = '';
			forEach ($data as $row) {
				$row2 = $this->db->queryRow("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = ? ORDER BY `dt` DESC LIMIT 1", $row->uid);
				$last_action = '';
				if ($row2 != null) {
					$last_action = " " . $this->util->date($row2->dt);
				}

				if ($row2->event == 'logon') {
					$status = "<green>logon<end>";
				} else if ($row2->event == 'logoff') {
					$status = "<orange>logoff<end>";
				} else {
					$status = "<grey>None<end>";
				}

				$remove = $this->text->make_chatcmd('Remove', "/tell <myname> track rem $row->name");

				$history = $this->text->make_chatcmd('History', "/tell <myname> track $row->name");

				$blob .= "<tab>-[{$history}] {$row->name} ({$status}{$last_action}) - {$remove}\n";
			}

			$msg = $this->text->make_blob("Tracklist ({$numrows})", $blob);
		} else {
			$msg = "No characters are on the track list.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("track")
	 * @Matches("/^track rem (.+)$/i")
	 */
	public function trackRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$data = $this->db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
			if (count($data) == 0) {
				$msg = "Character <highlight>$name<end> is not on the track list.";
			} else {
				$this->db->exec("DELETE FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
				$msg = "Character <highlight>$name<end> has been removed from the track list.";
				$this->buddylistManager->remove($name, 'tracking');
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("track")
	 * @Matches("/^track add (.+)$/i")
	 */
	public function trackAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$data = $this->db->query("SELECT * FROM tracked_users_<myname> WHERE `uid` = ?", $uid);
			if (count($data) != 0) {
				$msg = "Character <highlight>$name<end> is already on the track list.";
			} else {
				$this->db->exec("INSERT INTO tracked_users_<myname> (`name`, `uid`, `added_by`, `added_dt`) VALUES (?, ?, ?, ?)", $name, $uid, $sender, time());
				$msg = "Character <highlight>$name<end> has been added to the track list.";
				$this->buddylistManager->add($name, 'tracking');
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("track")
	 * @Matches("/^track (.+)$/i")
	 */
	public function trackShowCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$uid = $this->chatBot->get_uid($name);

		if (!$uid) {
			$msg = "Character <highlight>$name<end> does not exist.";
		} else {
			$data = $this->db->query("SELECT `event`, `dt` FROM tracking_<myname> WHERE `uid` = $uid ORDER BY `dt` DESC");
			if (count($data) > 0) {
				$blob = '';
				forEach ($data as $row) {
					$blob .= "<highlight>$row->event<end> " . $this->util->date($row->dt) ."\n";
				}

				$msg = $this->text->make_blob("Track History for $name", $blob);
			} else {
				$msg = "Character <highlight>$name<end> has never logged on or is not being tracked.";
			}
		}
		$sendto->reply($msg);
	}
}
