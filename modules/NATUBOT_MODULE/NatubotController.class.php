<?php

namespace Natubot\Modules;

/**
 * Written By Naturarum (Paradise, RK2)
 * For Budabot 3.0
 * Written: 12/28/2012
 *
 * NOTE: this module is 100% dependent on !alts being setup.  If a person does not have !alts setup, they will not appear in this list
 * due to the way the Budabot DB tables are joined together to determine mains.
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'players', 
 *		accessLevel = 'member', 
 *		description = 'Shows online players and their currently logged alts in a long format', 
 *		help        = 'players.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'findprof', 
 *		accessLevel = 'member', 
 *		description = 'Shows online players with alts of a specific profession / level', 
 *		help        = 'findprof.txt'
 *	)
 */

class NatubotController {

	/**
	* Name of the module.
	* Set automatically by module loader.
	*/
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $playerManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;

	/** @Inject */
	public $chatBot;

	/**
	 * @HandlesCommand("players")
	 * @Matches("/^players$/i")
	 */
	public function playersCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "
			SELECT p.*, COALESCE(a.main, o.name) AS pmain
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN players p ON o.name = p.name
			ORDER BY COALESCE(a.main, o.name) ASC";
		$data = $this->db->query($sql);
		$count = count($data);
		$currentMain = "";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$blob .= "<highlight>$row->pmain<end> on\n";
					$currentMain = $row->pmain;
				}

				if ($row->profession === null) {
					$blob .= "| ($row->name)\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof\n";
				}
			}
			$blob .= "\nWritten by Naturarum (RK2)";
			$msg = $this->text->makeBlob("Players Online ($count)", $blob);
		} else {
			$msg = "Players Online (0)";
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("findprof")
	 * @Matches("/^findprof (.+)$/i")
	 */
	public function findProfCommand($message, $channel, $sender, $sendto, $args) {
		// Normalize profession information and exit on bad
		$prof = $this->util->getProfessionName($args[1]);
		if (empty($prof)) {
			return false;
		}

		$sql = "
			SELECT DISTINCT p.*, COALESCE(a.main, o.name) AS pmain, (CASE WHEN o2.name IS NULL THEN 0 ELSE 1 END) AS online
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN alts a2 ON a2.main = COALESCE(a.main, o.name)
			LEFT JOIN players p ON a2.alt = p.name OR COALESCE(a.main, o.name) = p.name
			LEFT JOIN online o2 ON p.name = o2.name
			WHERE p.profession = ?
			ORDER BY COALESCE(a.main, o.name) ASC";
		$data = $this->db->query($sql, $prof);
		$count = count($data);
		$currentMain = "";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$blob .= "<highlight>$row->pmain<end> has\n";
					$currentMain = $row->pmain;
				}

				if ($row->profession === null) {
					$blob .= "| ($row->name)\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof";
				}
				if ($row->online == 1) {
					$blob .= " <green>Online<end>";
				}
				$blob .= "\n";
			}
			$blob .= "\nWritten by Naturarum (RK2)";
			$msg = $this->text->makeBlob("$prof Search Results ($count)", $blob);
		} else {
			$msg = "$prof Search Results (0)";
		}

		$sendto->reply($msg);
	}
}