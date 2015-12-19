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
 *		command     = 'playfields', 
 *		accessLevel = 'all', 
 *		description = 'Show playfield ids, long names, and short names', 
 *		help        = 'waypoint.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'waypoint', 
 *		accessLevel = 'all', 
 *		description = 'Create a waypoint link', 
 *		help        = 'waypoint.txt'
 *	)
 */
class PlayfieldController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $commandAlias;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'playfields');
		
		$this->commandAlias->register($this->moduleName, "playfields", "playfield");
	}

	/**
	 * @HandlesCommand("playfields")
	 * @Matches("/^playfields$/i")
	 */
	public function playfieldListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = '';

		$sql = "SELECT * FROM playfields ORDER BY long_name";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$blob .= "[<highlight>{$row->id}<end>] {$row->long_name} ({$row->short_name})\n";
		}

		$msg = $this->text->make_blob("Playfields", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("playfields")
	 * @Matches("/^playfields (.+)$/i")
	 */
	public function playfieldShowCommand($message, $channel, $sender, $sendto, $args) {
		$search = strtolower(trim($args[1]));
		
		list($longQuery, $longParams) = $this->util->generateQueryFromParams(explode(' ', $search), 'long_name');
		list($shortQuery, $shortParams) = $this->util->generateQueryFromParams(explode(' ', $search), 'short_name');
		
		$params = array_merge($longParams, $shortParams);
		
		$data = $this->db->query("SELECT * FROM playfields WHERE ($longQuery) OR ($shortQuery)", $params);

		$count = count($data);

		if ($count > 1) {
			$blob = "Result of Playfield Search for '$search'\n\n";
			forEach ($data as $row) {
				$blob .= "[<highlight>$row->id<end>] $row->long_name\n\n";
			}

			$msg = $this->text->make_blob("Playfields ($count)", $blob);
		} else if ($count == 1) {
			$row = $data[0];
			$msg = "[<highlight>$row->id<end>] $row->long_name";
		} else {
			$msg = "There were no matches for your search.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint Pos: ([0-9\\.]+), ([0-9\\.]+), ([0-9\\.]+), Area: ([a-zA-Z ]+)/i")
	 */
	public function waypoint1Command($message, $channel, $sender, $sendto, $args)
	{
		//Pos: ([0-9\\.]+), ([0-9\\.]+), ([0-9\\.]+), Area: (.+)
		$x_coords = $args[1];
		$y_coords = $args[2];
		
		$pf_name = $args[4];
		
		$playfield = $this->get_playfield_by_name($pf_name);
		$sendto->reply($this->processWaypointCommand($x_coords, $y_coords, $playfield->short_name, $playfield->id));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint \(?([0-9.]+) ([0-9.]+) y ([0-9.]+) ([0-9]+)\)?$/i")
	 */
	public function waypoint2Command($message, $channel, $sender, $sendto, $args) {
		$x_coords = $args[1];
		$y_coords = $args[2];
		$playfield_id = $args[4];

		$playfield = $this->get_playfield_by_id($playfield_id);
		if ($playfield === null) {
			$playfield_name = $playfield_id;
		} else {
			$playfield_name = $playfield->short_name;
		}
		
		$sendto->reply($this->processWaypointCommand($x_coords, $y_coords, $playfield_name, $playfield_id));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint ([0-9.]+)([x,. ]+)([0-9.]+)([x,. ]+)([0-9]+)$/i")
	 */
	public function waypoint3Command($message, $channel, $sender, $sendto, $args) {
		$x_coords = $args[1];
		$y_coords = $args[3];
		$playfield_id = $args[5];

		$playfield = $this->get_playfield_by_id($playfield_id);
		if ($playfield === null) {
			$playfield_name = $playfield_id;
		} else {
			$playfield_name = $playfield->short_name;
		}
		
		$sendto->reply($this->processWaypointCommand($x_coords, $y_coords, $playfield_name, $playfield_id));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)(.+)$/i")
	 */
	public function waypoint4Command($message, $channel, $sender, $sendto, $args) {
		$x_coords = $args[1];
		$y_coords = $args[3];
		$playfield_name = $args[5];

		$playfield = $this->get_playfield_by_name($playfield_name);
		if ($playfield === null) {
			$sendto->reply("Could not find playfield '$playfield_name'.");
		} else {
			$playfield_id = $playfield->id;
			$playfield_name = $playfield->short_name;
			$sendto->reply($this->processWaypointCommand($x_coords, $y_coords, $playfield_name, $playfield_id));
		}
	}
	
	private function processWaypointCommand($x_coords, $y_coords, $playfield_name, $playfield_id) {
		$link = $this->text->make_chatcmd("waypoint: {$x_coords}x{$y_coords} {$playfield_name}", "/waypoint {$x_coords} {$y_coords} {$playfield_id}");
		$blob = "Click here to use waypoint: $link";
		return $this->text->make_blob("waypoint: {$x_coords}x{$y_coords} {$playfield_name}", $blob);
	}
	
	public function get_playfield_by_name($playfield_name) {
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE ? OR `short_name` LIKE ? LIMIT 1";

		return $this->db->queryRow($sql, $playfield_name, $playfield_name);
	}

	public function get_playfield_by_id($playfield_id) {
		$sql = "SELECT * FROM playfields WHERE `id` = ?";

		return $this->db->queryRow($sql, $playfield_id);
	}
}
