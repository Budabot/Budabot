<?php

namespace Budabot\User\Modules;

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
 *		help        = 'nanolines.txt',
 *		alias		= 'nl'
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
		$this->db->loadSQLFile($this->moduleName, 'nanos_nanolines_ref');
		
		$this->settingManager->add($this->moduleName, 'maxnano', 'Number of Nanos shown on the list', 'edit', "number", '40', '30;40;50;60', "", "mod");
		$this->settingManager->add($this->moduleName, "shownanolineicons", "Show icons for the nanolines", "edit", "options", "0", "true;false", "1;0");
	}

	/**
	 * @HandlesCommand("nano")
	 * @Matches("/^nano (.+)$/i")
	 */
	public function nanoCommand($message, $channel, $sender, $sendto, $args) {
		$search = $args[1];

		$search = htmlspecialchars_decode($search);
		$tmp = explode(" ", $search);
		list($query, $params) = $this->util->generateQueryFromParams($tmp, 'n1.`name`');
		array_push($params, intval($this->settingManager->get("maxnano")));

		$sql =
			"SELECT
				n1.lowid,
				n1.lowql,
				n1.name,
				n1.location,
				n1.profession,
				n3.id AS nanoline_id,
				n3.name AS nanoline_name
			FROM
				nanos n1
				LEFT JOIN nanos_nanolines_ref n2 ON n1.lowid = n2.lowid
				LEFT JOIN nanolines n3 ON n2.nanolines_id = n3.id
			WHERE
				$query
			ORDER BY
				n1.profession, n3.name, n1.lowql DESC, n1.name ASC
			LIMIT
				?";

		$data = $this->db->query($sql, $params);

		$count = count($data);
		if ($count == 0) {
			$msg = "No nanos found.";
		} else {
			$blob = '';
			$currentNanoline = -1;
			forEach ($data as $row) {
				if ($currentNanoline != $row->nanoline_id) {
					if (!empty($row->nanoline_name)) {
						$nanolineLink = $this->text->makeChatcmd($row->nanoline_name, "/tell <myname> nanolines $row->nanoline_id");
						$blob .= "\n<header2>$row->profession<end> - $nanolineLink\n";
					} else {
						$blob .= "\n<header2>Unknown/General<end>\n";
					}
					$currentNanoline = $row->nanoline_id;
				}
				$blob .= $this->text->makeItem($row->lowid, $row->lowid, $row->lowql, $row->name);
				$blob .= " [$row->lowql] $row->location";
				$blob .= "\n";
			}
			$blob .= $this->getFooter();
			$msg = $this->text->makeBlob("Nano Search Results ($count)", $blob);
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
			$blob .= $this->text->makeChatcmd($row->profession, "/tell <myname> nanolines $row->profession");
			$blob .= "\n";
		}
		$blob .= $this->getFooter();
		$msg = $this->text->makeBlob('Nanolines', $blob);

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
					JOIN nanos_nanolines_ref n2
						ON (n1.lowid = n2.lowid)
				WHERE
					n2.nanolines_id = ?
				ORDER BY
					lowql DESC, name ASC";
			$data = $this->db->query($sql, $nanolineId);

			forEach ($data as $nano) {
				$blob .= $this->text->makeItem($nano->lowid, $nano->lowid, $nano->lowql, $nano->name);
				$blob .= " [$nano->lowql] $nano->location\n";
			}
			$blob .= $this->getFooter();
			$msg = $this->text->makeBlob("$nanoline->profession $nanoline->name Nanos", $blob);
		} else {
			$msg = "No nanoline found.";
		}

		$sendto->reply($msg);
	}

	private function nanolinesList($profession, $sendto) {
		$profession = $this->util->getProfessionName($profession);
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
				$blob .= $this->text->makeImage($row->image_id) . "\n";
			}
			$blob .= $this->text->makeChatcmd($row->name, "/tell <myname> nanolines $row->id");
			$blob .= "\n";
		}
		$blob .= $this->getFooter();
		$msg = $this->text->makeBlob("$profession Nanolines", $blob);

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
			$blob .= $this->text->makeChatcmd($row->location, "/tell <myname> nanoloc $row->location") . " ($row->count) \n";
		}
		$blob .= $this->getFooter();
		$msg = $this->text->makeBlob("Nano Locations", $blob);
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
				LEFT JOIN nanos_nanolines_ref n2 ON n1.lowid = n2.lowid
				LEFT JOIN nanolines n3 ON n2.nanolines_id = n3.id
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
				$blob .= $this->text->makeItem($row->lowid, $row->lowid, $row->lowql, $row->name);
				$blob .= " [$row->lowql] $row->location";
				if ($row->profession) {
					$blob .= " - <highlight>$row->profession<end>";
				}
				$blob .= "\n";
			}

			$msg = $this->text->makeBlob("Nanos for Location '$location' ($count)", $blob);
		}

		$sendto->reply($msg);
	}

	private function getFooter() {
		return "\n\nNanos DB provided by Saavick & Lucier";
	}
}
