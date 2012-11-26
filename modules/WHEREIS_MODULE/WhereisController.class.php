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
		$sql = "SELECT * FROM whereis w LEFT JOIN playfields p ON w.playfield_id = p.id WHERE name LIKE ?";
		$data = $this->db->query($sql, '%' . $search . '%');
		$count = count($data);

		if ($count > 0) {
			$blob = "Result of Whereis Search for '$search'\n\n";
			forEach ($data as $row) {
				$blob .= "<header2>$row->name<end>\n$row->answer";
				if ($row->playfield_id != 0) {
					$blob .= " " . $this->text->make_chatcmd("waypoint: {$row->xcoord}x{$row->ycoord} {$row->short_name}", "/waypoint {$row->xcoord} {$row->ycoord} {$row->playfield_id}");
				}
				$blob .= "\n\n";
			}

			$msg = $this->text->make_blob("Whereis ($count)", $blob);
		} else {
			$msg = "There were no matches for your search.";
		}
		$sendto->reply($msg);
	}
}
