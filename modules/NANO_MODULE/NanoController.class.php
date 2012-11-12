<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *	- Healnjoo (RK2)
 *	- Mdkdoc420 (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'nano', 
 *		accessLevel = 'all', 
 *		description = 'Searches for a nano and tells you were to get it', 
 *		help        = 'nano.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'nanolines', 
 *		accessLevel = 'all', 
 *		description = 'Shows nanos based on nanoline', 
 *		help        = 'nanolines.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'nanoloc', 
 *		accessLevel = 'all', 
 *		description = 'Browse nanos by location', 
 *		help        = 'nano.txt'
 *	)
 */
class NanoController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'nanos');
		$this->db->loadSQLFile($this->moduleName, 'nanolines');
		$this->db->loadSQLFile($this->moduleName, 'nanolines_ref');
		
		$this->settingManager->add($this->moduleName, 'maxnano', 'Number of Nanos shown on the list', 'edit', "number", '40', '30;40;50;60', "", "mod");
		$this->settingManager->add($this->moduleName, "shownanolineicons", "Show icons for the nanolines", "edit", "options", "0", "true;false", "1;0");
	}

	/**
	 * @HandlesCommand("nano")
	 * @Matches("/^nano (.+)$/i")
	 */
	public function nanoCommand($message, $channel, $sender, $sendto, $args) {
		$name = $args[1];

		$name = htmlspecialchars_decode($name);
		$name = str_replace("'", "''", $name);

		$tmp = explode(" ", $name);
		forEach ($tmp as $key => $value) {
			$query .= " AND n1.`name` LIKE '%$value%'";
		}

		$sql =
			"SELECT
				n1.lowid,
				n1.lowql,
				n1.name,
				n1.location,
				n3.profession
			FROM
				nanos n1
				LEFT JOIN nano_nanolines_ref n2 ON n1.lowid = n2.lowid
				LEFT JOIN nanolines n3 ON n2.nanolineid = n3.id
			WHERE
				1=1 $query
			ORDER BY
				n1.lowql DESC, n1.name ASC
			LIMIT
				" . $this->settingManager->get("maxnano");

		$data = $this->db->query($sql);

		$count = count($data);
		if ($count == 0) {
			$msg = "No nanos found.";
		} else if ($count == 1) {
			$row = $data[0];
			$msg = $this->text->make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
			$msg .= " [$row->lowql] $row->location";
			if ($row->profession) {
				$msg .= " - <highlight>$row->profession<end>";
			}
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
				$blob .= " [$row->lowql] $row->location";
				if ($row->profession) {
					$blob .= " - <highlight>$row->profession<end>";
				}
				$blob .= "\n";
			}

			$msg = $this->text->make_blob("Nano Search Results ($count)", $blob);
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("nanolines")
	 * @Matches("/^nanolines$/i")
	 */
	public function nanolinesListProfsCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "SELECT DISTINCT profession FROM nanolines ORDER BY profession ASC";
		$data = $this->db->query($sql);

		$blob = '';
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->profession, "/tell <myname> nanolines $row->profession");
			$blob .= "\n";
		}
		$blob .= "\n\nAO Nanos by Voriuste";
		$blob .= "\nModule created by Tyrence (RK2)";

		$msg = $this->text->make_blob('Nanolines', $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("nanolines")
	 * @Matches("/^nanolines (.*)$/i")
	 */
	public function nanolinesListCommand($message, $channel, $sender, $sendto, $args) {
		if (preg_match("/^[0-9]+$/", $args[1])) {
			$this->nanolinesShow($args[1], $sendto);
		} else {
			$this->nanolinesList($args[1], $sendto);
		}
	}	
	
	private function nanolinesShow($nanolineId, $sendto) {
		$sql = "SELECT * FROM nanolines WHERE id = ?";
		$nanoline = $this->db->queryRow($sql, $nanolineId);

		$msg = '';
		if ($nanoline !== null) {
			$blob = '';

			$sql = "
				SELECT
					n1.lowid,
					lowql,
					n1.name,
					location
				FROM
					nanos n1
					JOIN nano_nanolines_ref n2
						ON (n1.lowid = n2.lowid)
				WHERE
					n2.nanolineid = ?
				ORDER BY
					lowql DESC, name ASC";
			$data = $this->db->query($sql, $nanolineId);

			forEach ($data as $nano) {
				$blob .= $this->text->make_item($nano->lowid, $nano->lowid, $nano->lowql, $nano->name);
				$blob .= " [$nano->lowql] $nano->location\n";
			}

			$blob .= "\n\nAO Nanos by Voriuste";
			$blob .= "\nModule created by Tyrence (RK2)";

			$msg = $this->text->make_blob("$nanoline->profession $nanoline->name Nanos", $blob);
		} else {
			$msg = "No nanoline found.";
		}

		$sendto->reply($msg);
	}

	private function nanolinesList($profession, $sendto) {
		$profession = $this->util->get_profession_name($profession);
		if ($profession == '') {
			$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
			$sendto->reply($msg);
			return;
		}

		$sql = "SELECT * FROM nanolines WHERE profession LIKE ? ORDER BY name ASC";
		$data = $this->db->query($sql, $profession);

		$blob = '';
		forEach ($data as $row) {
			if ($this->settingManager->get("shownanolineicons") == "1") {
				$blob .= $this->text->make_image($row->image_id) . "\n";
			}
			$blob .= $this->text->make_chatcmd("$row->name", "/tell <myname> nanolines $row->id");
			$blob .= "\n";
		}
		$blob .= "\n\nAO Nanos by Voriuste";
		$blob .= "\nModule created by Tyrence (RK2)";
		$msg = $this->text->make_blob("$profession Nanolines", $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("nanoloc")
	 * @Matches("/^nanoloc$/i")
	 */
	public function nanolocListCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT location, count(location) AS count FROM nanos GROUP BY location ORDER BY location ASC");

		$blob = '';
		forEach ($data as $row) {
			$blob .= $this->text->make_chatcmd($row->location, "/tell <myname> nanoloc $row->location") . " ($row->count) \n";
		}

		$msg = $this->text->make_blob("Nano Locations", $blob);
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("nanoloc")
	 * @Matches("/^nanoloc (.+)$/i")
	 */
	public function nanolocViewCommand($message, $channel, $sender, $sendto, $args) {
		$location = $args[1];

		$sql =
			"SELECT
				n1.lowid,
				n1.lowql,
				n1.name,
				n1.location,
				n3.profession
			FROM
				nanos n1
				LEFT JOIN nano_nanolines_ref n2 ON n1.lowid = n2.lowid
				LEFT JOIN nanolines n3 ON n2.nanolineid = n3.id
			WHERE
				n1.location LIKE ?
			ORDER BY
				n1.profession ASC,
				n1.name ASC";

		$data = $this->db->query($sql, $location);

		$count = count($data);
		if ($count == 0) {
			$msg = "No nanos found.";
		} else {
			$blob = '';
			forEach ($data as $row) {
				$blob .= $this->text->make_item($row->lowid, $row->lowid, $row->lowql, $row->name);
				$blob .= " [$row->lowql] $row->location";
				if ($row->profession) {
					$blob .= " - <highlight>$row->profession<end>";
				}
				$blob .= "\n";
			}

			$msg = $this->text->make_blob("Nanos for Location '$location' ($count)", $blob);
		}

		$sendto->reply($msg);
	}
}
