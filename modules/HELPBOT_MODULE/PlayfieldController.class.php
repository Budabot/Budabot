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

		$msg = $this->text->makeBlob("Playfields", $blob);
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

			$msg = $this->text->makeBlob("Playfields ($count)", $blob);
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
	public function waypoint1Command($message, $channel, $sender, $sendto, $args) {
		//Pos: ([0-9\\.]+), ([0-9\\.]+), ([0-9\\.]+), Area: (.+)
		$xCoords = $args[1];
		$yCoords = $args[2];
		
		$playfieldName = $args[4];
		
		$playfield = $this->getPlayfieldByName($playfieldName);
		$sendto->reply($this->processWaypointCommand($xCoords, $yCoords, $playfield->short_name, $playfield->id));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint \(?([0-9.]+) ([0-9.]+) y ([0-9.]+) ([0-9]+)\)?$/i")
	 */
	public function waypoint2Command($message, $channel, $sender, $sendto, $args) {
		$xCoords = $args[1];
		$yCoords = $args[2];
		$playfieldId = $args[4];

		$playfield = $this->getPlayfieldById($playfieldId);
		if ($playfield === null) {
			$playfieldName = $playfieldId;
		} else {
			$playfieldName = $playfield->short_name;
		}
		
		$sendto->reply($this->processWaypointCommand($xCoords, $yCoords, $playfieldName, $playfieldId));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint ([0-9.]+)([x,. ]+)([0-9.]+)([x,. ]+)([0-9]+)$/i")
	 */
	public function waypoint3Command($message, $channel, $sender, $sendto, $args) {
		$xCoords = $args[1];
		$yCoords = $args[3];
		$playfieldId = $args[5];

		$playfield = $this->getPlayfieldById($playfieldId);
		if ($playfield === null) {
			$playfieldName = $playfieldId;
		} else {
			$playfieldName = $playfield->short_name;
		}
		
		$sendto->reply($this->processWaypointCommand($xCoords, $yCoords, $playfieldName, $playfieldId));
	}
	
	/**
	 * @HandlesCommand("waypoint")
	 * @Matches("/^waypoint ([0-9\\.]+)([x,. ]+)([0-9\\.]+)([x,. ]+)(.+)$/i")
	 */
	public function waypoint4Command($message, $channel, $sender, $sendto, $args) {
		$xCoords = $args[1];
		$yCoords = $args[3];
		$playfieldName = $args[5];

		$playfield = $this->getPlayfieldByName($playfieldName);
		if ($playfield === null) {
			$sendto->reply("Could not find playfield '$playfieldName'.");
		} else {
			$playfieldId = $playfield->id;
			$playfieldName = $playfield->short_name;
			$sendto->reply($this->processWaypointCommand($xCoords, $yCoords, $playfieldName, $playfieldId));
		}
	}
	
	private function processWaypointCommand($xCoords, $yCoords, $playfieldName, $playfieldId) {
		$link = $this->text->makeChatcmd("waypoint: {$xCoords}x{$yCoords} {$playfieldName}", "/waypoint {$xCoords} {$yCoords} {$playfieldId}");
		$blob = "Click here to use waypoint: $link";
		return $this->text->makeBlob("waypoint: {$xCoords}x{$yCoords} {$playfieldName}", $blob);
	}
	
	public function getPlayfieldByName($playfieldName) {
		$sql = "SELECT * FROM playfields WHERE `long_name` LIKE ? OR `short_name` LIKE ? LIMIT 1";

		return $this->db->queryRow($sql, $playfieldName, $playfieldName);
	}

	public function getPlayfieldById($playfieldId) {
		$sql = "SELECT * FROM playfields WHERE `id` = ?";

		return $this->db->queryRow($sql, $playfieldId);
	}
}
