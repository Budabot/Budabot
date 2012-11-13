<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = "orghistory", 
 *		accessLevel = "guild", 
 *		description = "Shows the org history (invites and kicks and leaves) for a character", 
 *		help        = "orghistory.txt"
 *	)
 */
class OrgHistoryController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "org_history");
	}

	/**
	 * @HandlesCommand("orghistory")
	 * @Matches("/^orghistory$/i")
	 * @Matches("/^orghistory ([0-9]+)$/i")
	 */
	public function orghistoryCommand($message, $channel, $sender, $sendto, $args) {
		$pageSize = 20;
		$page = 1;
		if (count($args) == 2) {
			$page = $args[1];
		}

		$startingRecord = ($page - 1) * $pageSize;

		$blob = '';

		$sql = "SELECT actor, actee, action, organization, time FROM `org_history` ORDER BY time DESC LIMIT $startingRecord, $pageSize";
		$data = $this->db->query($sql);
		if (count($data) != 0) {
			forEach ($data as $row) {
				$blob .= "<highlight>$row->actor<end> $row->action <highlight>$row->actee<end> in $row->organization at " . $this->util->date($row->time) . "\n";
			}

			$msg = $this->text->make_blob('Org History', $blob);
		} else {
			$msg = "No org history has been recorded.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("orghistory")
	 * @Matches("/^orghistory (.+)$/i")
	 */
	public function orghistoryPlayerCommand($message, $channel, $sender, $sendto, $args) {
		$player = $args[1];

		$blob = '';

		$blob .= "\n<header2>Actions on $player<end>\n";
		$sql = "SELECT actor, actee, action, organization, time FROM `org_history` WHERE actee LIKE ? ORDER BY time DESC";
		$data = $this->db->query($sql, $player);
		forEach ($data as $row) {
			$blob .= "<highlight>$row->actor<end> $row->action <highlight>$row->actee<end> - $row->organization - " . $this->util->date($row->time) . "\n";
		}

		$blob .= "\n<header2>Actions by $player<end>\n";
		$sql = "SELECT actor, actee, action, organization, time FROM `org_history` WHERE actor LIKE ? ORDER BY time DESC";
		$data = $this->db->query($sql, $player);
		forEach ($data as $row) {
			$blob .= "<highlight>$row->actor<end> $row->action <highlight>$row->actee<end> - $row->organization - " . $this->util->date($row->time) . "\n";
		}

		$msg = $this->text->make_blob('Org History', $blob);

		$sendto->reply($msg);
	}

	/**
	 * @Event("orgmsg")
	 * @Description("Capture Org Invite/Kick/Leave messages for orghistory")
	 */
	public function captureOrgMessagesEvent($eventObj) {
		$message = $eventObj->message;
		if (preg_match("/^(.+) just left your organization.$/", $message, $arr)) {
			$actor = $arr[1];
			$actee = "";
			$action = "left";
			$time = time();

			$sql = "INSERT INTO `org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
			$this->db->exec($sql, $actor, $actee, $action, $time);
		} else if (preg_match("/^(.+) kicked (.+) from your organization.$/", $message, $arr)) {
			$actor = $arr[1];
			$actee = $arr[2];
			$action = "kicked";
			$time = time();

			$sql = "INSERT INTO `org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
			$this->db->exec($sql, $actor, $actee, $action, $time);
		} else if (preg_match("/^(.+) invited (.+) to your organization.$/", $message, $arr)) {
			$actor = $arr[1];
			$actee = $arr[2];
			$action = "invited";
			$time = time();

			$sql = "INSERT INTO `org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
			$this->db->exec($sql, $actor, $actee, $action, $time);
		} else if (preg_match("/^(.+) removed inactive character (.+) from your organization.$/", $message, $arr)) {
			$actor = $arr[1];
			$actee = $arr[2];
			$action = "removed";
			$time = time();

			$sql = "INSERT INTO `org_history` (actor, actee, action, organization, time) VALUES (?, ?, ?, '<myguild>', ?) ";
			$this->db->exec($sql, $actor, $actee, $action, $time);
		}
	}
}

