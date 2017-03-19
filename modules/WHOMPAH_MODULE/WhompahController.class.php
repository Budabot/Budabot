<?php

namespace Budabot\User\Modules;

use stdClass;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'whompah',
 *		accessLevel = 'all', 
 *		description = 'Shows the whompah route from one city to another', 
 *		help        = 'whompah.txt'
 *	)
 */
class WhompahController {

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
	public $commandAlias;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'whompah_cities');
		
		$this->commandAlias->register($this->moduleName, 'whompah', 'whompahs');
		$this->commandAlias->register($this->moduleName, 'whompah', 'whompa');
		$this->commandAlias->register($this->moduleName, 'whompah', 'whompas');
	}
	
	/**
	 * @HandlesCommand("whompah")
	 * @Matches("/^whompah$/i")
	 */
	public function whompahListCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT * FROM `whompah_cities` ORDER BY city_name ASC";
		$data = $this->db->query($sql);

		$blob = '';
		forEach ($data as $row) {
			$cityLink = $this->text->makeChatcmd($row->short_name, "/tell <myname> whompah {$row->short_name}");
			$blob .= "{$row->city_name} ({$cityLink})\n";
		}
		$blob .= "\nWritten By Tyrence (RK2)\nDatabase from a Bebot module written by POD13";

		$msg = $this->text->makeBlob('Whompah Cities', $blob);

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("whompah")
	 * @Matches("/^whompah (.+) (.+)$/i")
	 */
	public function whompahTravelCommand($message, $channel, $sender, $sendto, $args) {
		$startCity = $this->findCity($args[1]);
		$endCity = $this->findCity($args[2]);

		if ($startCity === null) {
			$msg = "Error! Could not find city '$args[1]'!";
			$sendto->reply($msg);
			return;
		}
		if ($endCity === null) {
			$msg = "Error! Could not find city '$args[2]'!";
			$sendto->reply($msg);
			return;
		}

		$whompahs = $this->buildWhompahNetwork();

		$whompah = new stdClass;
		$whompah->id = $endCity->id;
		$whompah->city_name = $whompahs[$endCity->id]->city_name;
		$whompah->previous = null;
		$whompah->visited = true;
		$obj = $this->findWhompahPath($q = array($whompah), $whompahs, $startCity->id);

		if ($obj === false) {
			$msg = "There was an error while trying to find the whompah path.";
		} else {
			while ($obj->previous !== null) {
				$msg .= "$obj->city_name -> ";
				$obj = $obj->previous;
			}
			$msg .= "$obj->city_name";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whompah")
	 * @Matches("/^whompah (.+)$/i")
	 */
	public function whompahDestinationsCommand($message, $channel, $sender, $sendto, $args) {
		$city = $this->findCity($args[1]);

		if ($city === null) {
			$msg = "Error! Could not find city '$args[1]'!";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT w2.* FROM whompah_cities_rel w1 JOIN whompah_cities w2 ON w1.city2_id = w2.id WHERE w1.city1_id = ?";
		$data = $this->db->query($sql, $city->id);

		$msg = "From {$city->city_name} you can get to: " .
			implode(", ", array_map(function($row) { return "<highlight>{$row->city_name}<end> ({$row->short_name})"; }, $data));

		$sendto->reply($msg);
	}

	public function findWhompahPath($queue, $whompahs, $endCity) {
		$currentWhompah = array_shift($queue);

		if ($currentWhompah == false) {
			return false;
		}

		if ($currentWhompah->id == $endCity) {
			return $currentWhompah;
		}

		forEach ($whompahs[$currentWhompah->id]->connections as $city2Id) {
			if ($whompahs[$city2Id]->visited !== true) {
				$whompahs[$city2Id]->visited = true;
				$nextWhompah = new stdClass;
				$nextWhompah->id = $city2Id;
				$nextWhompah->city_name = $whompahs[$city2Id]->city_name;
				$nextWhompah->previous = $currentWhompah;
				$queue []= $nextWhompah;
			}
		}

		return $this->findWhompahPath($queue, $whompahs, $endCity);
	}

	public function findCity($search) {
		$sql = "SELECT * FROM whompah_cities WHERE city_name LIKE ? OR short_name LIKE ?";
		return $this->db->queryRow($sql, $search, $search);
	}

	public function buildWhompahNetwork() {
		$whompahs = array();

		$sql = "SELECT * FROM `whompah_cities`";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$whompahs[$row->id] = $row;
			$whompahs[$row->id]->connections = array();
			$whompahs[$row->id]->visited = false;
		}

		$sql = "SELECT city1_id, city2_id FROM whompah_cities_rel";
		$data = $this->db->query($sql);
		forEach ($data as $row) {
			$whompahs[$row->city1_id]->connections[] = $row->city2_id;
		}

		return $whompahs;
	}
}
