<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'kos', 
 *		accessLevel = 'guild', 
 *		description = 'Shows the kill-on-sight list', 
 *		help        = 'kos.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'kos add .+', 
 *		accessLevel = 'guild', 
 *		description = 'Adds a character to the kill-on-sight list', 
 *		help        = 'kos.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'kos rem .+', 
 *		accessLevel = 'mod', 
 *		description = 'Removes a character from the kill-on-sight list', 
 *		help        = 'kos.txt'
 *	)
 */
class KillOnSightController {

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
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'kos');
	}

	/**
	 * @HandlesCommand("kos")
	 * @Matches("/^kos$/i")
	 */
	public function kosListCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT * FROM kos";

		$data = $this->db->query($sql);
		$count = count($data);

		if ($count == 0) {
			$msg = "There are no characters on the KOS list.";
		} else {
			$blob = '';
			forEach ($data as $row) {
				$comment = "";
				if (!empty($row->comment)) {
					$comment = " - $row->comment";
				}
				
				$blob .= "<highlight>$row->name<end>$comment (added by $row->submitter <highlight>" . $this->util->unixtimeToReadable(time() - $row->dt) . "<end> ago)\n";
			}
			$msg = $this->text->makeBlob("Kill-On-Sight List ($count)", $blob);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("kos add .+")
	 * @Matches("/^kos add ([a-z0-9-]+)$/i")
	 * @Matches("/^kos add ([a-z0-9-]+) (.+)$/i")
	 */
	public function kosAddCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$charid = $this->chatBot->get_uid($name);
		
		if ($charid == false) {
			$sendto->reply("Character <highlight>$name<end> does not exist.");
			return;
		}

		$sql = "SELECT * FROM kos WHERE name = ?";
		$row = $this->db->queryRow($sql, $name);

		if ($row !== null) {
			$msg = "Character <highlight>$name<end> is already on the Kill-On-Sight list.";
		} else {
			$comment = "";
			if (isset($args[2])) {
				$comment = trim($args[2]);
			}
			
			$sql = "INSERT INTO kos (name, comment, submitter, dt) VALUES (?, ?, ?, ?)";
			$this->db->exec($sql, $name, $comment, $sender, time());
			$msg = "Character <highlight>$name<end> has been added to the Kill-On-Sight list.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("kos rem .+")
	 * @Matches("/^kos rem (.+)$/i")
	 */
	public function kosRemCommand($message, $channel, $sender, $sendto, $args) {
		$name = ucfirst(strtolower($args[1]));
		$sql = "SELECT * FROM kos WHERE name = ?";

		$row = $this->db->queryRow($sql, $name);

		if ($row === null) {
			$msg = "Character <highlight>$name<end> is not on the Kill-On-Sight list.";
		} else {
			$sql = "DELETE FROM kos WHERE name = ?";
			$this->db->exec($sql, $name);
			$msg = "Character <highlight>$name<end> has been removed from the Kill-On-Sight list.";
		}
		$sendto->reply($msg);
	}
}