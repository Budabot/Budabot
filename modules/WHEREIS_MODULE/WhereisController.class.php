<?php

namespace Budabot\User\Modules;

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
		$words = explode(' ', $search);
		list($query1, $params1) = $this->util->generateQueryFromParams($words, 'name');
		list($query2, $params2) = $this->util->generateQueryFromParams($words, 'keywords');
		$params = array_merge($params1, $params2);
		
		$sql = "SELECT * FROM whereis w LEFT JOIN playfields p ON w.playfield_id = p.id WHERE ($query1) OR ($query2)";
		$data = $this->db->query($sql, $params);
		$count = count($data);

		if ($count > 0) {
			$blob = "Result of Whereis Search for '$search'\n\n";
			forEach ($data as $row) {
				$blob .= "<header2>$row->name<end>\n$row->answer";
				if ($row->playfield_id != 0 && $row->xcoord != 0 && $row->ycoord != 0) {
					$blob .= " " . $this->text->makeChatcmd("waypoint: {$row->xcoord}x{$row->ycoord} {$row->short_name}", "/waypoint {$row->xcoord} {$row->ycoord} {$row->playfield_id}");
				}
				$blob .= "\n\n";
			}

			$msg = $this->text->makeBlob("Whereis ($count)", $blob);
		} else {
			$msg = "There were no matches for your search.";
		}
		$sendto->reply($msg);
	}
}
