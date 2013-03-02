<?php
/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'renametables',
 *		accessLevel = 'superadmin',
 *		description = "Rename tables",
 *		help        = 'renametables.txt'
 *	)
 */
class RenameTablesController extends AutoInject {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/**
	 * @Setup
	 */
	public function setup() {

	}

	/**
	 * @HandlesCommand("renametables")
	 * @Matches("/^renametables (.+) (.+)$/i")
	 */
	public function renametablesCommand($message, $channel, $sender, $sendto, $args) {
		$fromName = strtolower($args[1]);
		$toName = strtolower($args[2]);
		
		$sendto->reply("Restart your bot NOW!");
	
		$data = $this->db->query("SELECT * FROM sqlite_master WHERE type = 'table';");
		forEach ($data as $row) {
			if (preg_match("/(.+)_$fromName$/", $row->name, $arr)) {
				$prefix = $arr[1];
				$sql = "DROP TABLE IF EXISTS {$prefix}_{$toName}";
				echo $sql . "\n";
				$this->db->exec($sql);
				$sql = "ALTER TABLE {$row->name} RENAME TO {$prefix}_{$toName}";
				echo $sql . "\n";
				$this->db->exec($sql);
			}
		}
	}
}
