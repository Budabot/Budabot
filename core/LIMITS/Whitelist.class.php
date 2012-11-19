<?php
/**
 * Authors:
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command       = 'whitelist',
 *		accessLevel   = 'all',
 *		description   = 'Add players to whitelist to bypass limits check',
 *		help          = 'whitelist.txt',
 *		defaultStatus = '1'
 *	)
 */
class Whitelist {
	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $text;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'whitelist');
	}
	
	/**
	 * @HandlesCommand("whitelist")
	 * @Matches("/^whitelist$/i")
	 */
	public function whitelistCommand($message, $channel, $sender, $sendto, $args) {
		$list = $this->all();
		if (count($list) == 0) {
			$sendto->reply("No entries in whitelist");
		} else {
			$blob = '';
			forEach ($list as $entry) {
				$remove = $this->text->make_chatcmd('Remove', "/tell <myname> whitelist remove $entry->name");
				$blob .= "<highlight>{$entry->name}<end> [added by {$entry->added_by}] {$entry->added_dt} {$remove}\n";
			}
			$msg = $this->text->make_blob("Whitelist", $blob);
			$sendto->reply($msg);
		}
	}
	
	/**
	 * @HandlesCommand("whitelist")
	 * @Matches("/^whitelist add (.+)$/i")
	 */
	public function whitelistAddCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->add($args[1], $sender));
	}
	
	/**
	 * @HandlesCommand("whitelist")
	 * @Matches("/^whitelist (rem|remove|del|delete) (.+)$/i")
	 */
	public function whitelistRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$sendto->reply($this->remove($args[2]));
	}

	public function add($user, $sender) {
		$user = ucfirst(strtolower($user));
		$sender = ucfirst(strtolower($sender));

		if ($user == '' || $sender == '') {
			return "User or sender is blank";
		}

		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) != 0) {
			return "Error! $user already added to the whitelist";
		} else {
			$this->db->exec("INSERT INTO whitelist (name, added_by, added_dt) VALUES (?, ?, ?)", $user, $sender, time());
			return "$user has been added to the whitelist";
		}
	}

	public function remove($user) {
		$user = ucfirst(strtolower($user));

		if ($user == '') {
			return "User is blank";
		}

		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return "Error! $user is not on the whitelist";
		} else {
			$this->db->exec("DELETE FROM whitelist WHERE name = ?", $user);
			return "$user has been removed from the whitelist";
		}
	}

	public function check($user) {
		$user = ucfirst(strtolower($user));

		$data = $this->db->query("SELECT * FROM whitelist WHERE name = ?", $user);
		if (count($data) == 0) {
			return false;
		} else {
			return true;
		}
	}

	public function all() {
		return $this->db->query("SELECT * FROM whitelist ORDER BY name ASC");
	}
}

?>
