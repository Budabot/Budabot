<?php

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
			$cityLink = $this->text->make_chatcmd($row->short_name, "/tell <myname> whompah {$row->short_name}");
			$blob .= "{$row->city_name} ({$cityLink})\n";
		}
		$blob .= "\nWritten By Tyrence (RK2)\nDatabase from a Bebot module written by POD13";

		$msg = $this->text->make_blob('Whompah Cities', $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("whompah")
	 * @Matches("/^whompah (.+) (.+)$/i")
	 */
	public function whompahTravelCommand($message, $channel, $sender, $sendto, $args) {
		$startCity = $this->find_city($args[1]);
		$endCity = $this->find_city($args[2]);

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

		$whompahs = $this->build_whompah_network();

		$whompah = new stdClass;
		$whompah->id = $endCity->id;
		$whompah->city_name = $whompahs[$endCity->id]->city_name;
		$whompah->previous = null;
		$whompah->visited = true;
		$obj = $this->find_whompah_path($q = array($whompah), $whompahs, $startCity->id);

		if ($obj === false) {
			$msg = "There was an error while trying to find the whompah path.";
		} else {
			while ($obj->previous !== null) {
				$msg .= "$obj->city_name -> ";
				$obj = &$obj->previous;
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
		$city = $this->find_city($args[1]);

		if ($city === null) {
			$msg = "Error! Could not find city '$args[1]'!";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT w2.* FROM whompah_cities_rel w1 JOIN whompah_cities w2 ON w1.city2_id = w2.id WHERE w1.city1_id = ?";
		$data = $this->db->query($sql, $city->id);

		$msg = "From {$city->city_name} you can get to: ";
		forEach ($data as $row) {
			$msg .= "<highlight>{$row->city_name}<end> ({$row->short_name}), ";
		}

		$sendto->reply($msg);
	}

	public function find_whompah_path($queue, $whompahs, &$endCity) {
		$current_whompah = array_shift($queue);

		if ($current_whompah == false) {
			return false;
		}

		if ($current_whompah->id == $endCity) {
			return $current_whompah;
		}

		forEach ($whompahs[$current_whompah->id]->connections as $city2_id) {
			if ($whompahs[$city2_id]->visited !== true) {
				$whompahs[$city2_id]->visited = true;
				$next_whompah = new stdClass;
				$next_whompah->id = $city2_id;
				$next_whompah->city_name = $whompahs[$city2_id]->city_name;
				$next_whompah->previous = &$current_whompah;
				$queue []= $next_whompah;
			}
		}

		return $this->find_whompah_path($queue, $whompahs, $endCity);
	}

	public function find_city($search) {
		$sql = "SELECT * FROM whompah_cities WHERE city_name LIKE ? OR short_name LIKE ?";
		return $this->db->queryRow($sql, $search, $search);
	}

	public function build_whompah_network() {
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
