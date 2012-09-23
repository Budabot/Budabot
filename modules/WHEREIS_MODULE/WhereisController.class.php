<?php

/**
 * Authors: 
 *	- Jaqueme
 *  Database adapted from one originally compiled by Malosar for BeBot
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'whereis', 
 *		accessLevel = 'all', 
 *		description = 'Shows where places and NPCs are', 
 *		help        = 'whereis.txt'
 *	)
 */
class WhereisController {

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
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'whereis');
	}

	/**
	 * @HandlesCommand("whereis")
	 * @Matches("/^whereis (.+)$/i")
	 */
	public function whereisCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];
		$search = strtolower($search);
		$data = $this->db->query("SELECT * FROM whereis WHERE name LIKE ?", '%' . $search . '%');
		$count = count($data);

		if ($count > 1) {
			$blob = "Result of Whereis Search for '$search'\n\n";
			forEach ($data as $row) {
				$blob .= "<yellow>$row->name<end>\n<green>Can be found $row->answer<end>\n\n";
			}

			$msg = $this->text->make_blob("Whereis ($count)", $blob);
		} else if ($count == 1) {
			$row = $data[0];
			$msg = "<yellow>$row->name<end>\n<green>Can be found $row->answer<end>";
		} else {
			$msg = "There were no matches for your search.";
		}
		$sendto->reply($msg);
	}
}
