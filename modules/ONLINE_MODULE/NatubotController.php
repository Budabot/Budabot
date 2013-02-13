<?php

namespace Budabot\User\Modules;

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
 *		command     = 'play', 
 *		accessLevel = 'all', 
 *		description = 'Shows online players and their currently logged alts', 
 *		help        = ''
 *	)
 *	@DefineCommand(
 *		command     = 'players', 
 *		accessLevel = 'all', 
 *		description = 'Shows online players and their currently logged alts in a long format', 
 *		help        = ''
 *	)
 *	@DefineCommand(
 *		command     = 'findprof', 
 *		accessLevel = 'all', 
 *		description = 'Shows online players with alts of a specific profession / level', 
 *		help        = ''
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
	 * @HandlesCommand("play")
	 * @Matches("/^play$/i")
	 */
	public function playCommand($message, $channel, $sender, $sendto, $args) {

		$mains = $this->db->query("SELECT DISTINCT IFNULL(a.main, o.name) as main FROM online o LEFT JOIN alts a on o.name = a.alt");
		$count = count($mains);

		if ($count > 0) {
			forEach ($mains as $row) {
				$blob .= "<highlight>$row->main<end> on ";

				// First check for mains...
				$onlineMains = $this->db->query("SELECT name FROM online WHERE name = ?", $row->main);
				if (count($onlineMains) > 0) {
					foreach($onlineMains as $row2) {
						$playerInfo = $this->playerManager->get_by_name($row2->name);
						if ($playerInfo === null) {
							$blob .= "($row2->name) ";
						} else {
							$prof = $this->util->get_profession_abbreviation($playerInfo->profession);
							$blob.= "($row2->name - $playerInfo->level/<green>$playerInfo->ai_level<end> $prof) ";
						}
					}
				}

				// ... then get alts
				$onlineAlts = $this->db->query("SELECT a.alt FROM online o JOIN alts a ON a.alt = o.name WHERE a.main = ?", $row->main);
				if (count($onlineAlts) > 0) {
					foreach ($onlineAlts as $row3) {
						$playerInfo = $this->playerManager->get_by_name($row3->alt);
						if ($playerInfo === null) {
							$blob .= "($row3->alt) ";
						} else {
							$prof = $this->util->get_profession_abbreviation($playerInfo->profession);
							$blob.= "($row3->alt - $playerInfo->level/<green>$playerInfo->ai_level<end> $prof) ";
						}
					}
				}

				$blob .= "\n";
			}
		}

		$msg = $this->text->make_blob("$count Players Currently Online", $blob);
		$sendto->reply($msg);
	} 
	
	/**
	 * @HandlesCommand("players")
	 * @Matches("/^players$/i")
	 */
	public function playersCommand($message, $channel, $sender, $sendto, $args) {
		$mains = $this->db->query("SELECT DISTINCT IFNULL(a.main, o.name) as main FROM online o LEFT JOIN alts a on o.name = a.alt");
		$count = count($mains);

		if ($count > 0) {
			forEach ($mains as $row) {
				$blob .= "<highlight>$row->main<end> on\n";

				// First check for mains...
				$onlineMains = $this->db->query("SELECT name FROM online WHERE name = ?", $row->main);
				if (count($onlineMains) > 0) {
					forEach ($onlineMains as $row2) {
						$playerInfo = $this->playerManager->get_by_name($row2->name);
						if ($playerInfo === null) {
							$blob .= "| ($row2->name)\n";
						} else {
							$blob.= "| $row2->name - $playerInfo->level/<green>$playerInfo->ai_level<end> $playerInfo->breed $playerInfo->profession\n";
						}
					}
				}

				// ... then get alts
				$onlineAlts = $this->db->query("SELECT a.alt FROM online o JOIN alts a ON a.alt = o.name WHERE a.main = ?", $row->main);
				if (count($onlineAlts) > 0) {
					foreach ($onlineAlts as $row3) {
						$playerInfo = $this->playerManager->get_by_name($row3->alt);
						if ($playerInfo === null) {
							$blob .= "| ($row3->alt)\n";
						} else {
							$blob.= "| $row3->alt - $playerInfo->level/<green>$playerInfo->ai_level<end> $playerInfo->breed $playerInfo->profession\n";
						}
					}
				}

				$blob .= "\n";
			}
		}

		$msg = $this->text->make_blob("$count Players Currently Online", $blob);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("findprof")
	 * @Matches("/^findprof (.+)$/i")
	 */
	public function findProfCommand($message, $channel, $sender, $sendto, $args) {
		// Normalize profession information and exit on bad
		$prof = $this->util->get_profession_name($args[1]);
		if ($prof === null) {
			return false;
		}

		// Search for alts of online players that fit the criteria
		$onlineMains = $this->db->query("SELECT DISTINCT IFNULL(a.main, o.name) as name FROM online o LEFT JOIN alts a ON a.alt = o.name");
		forEach ($onlineMains as $row) {
			// Determine if $row has any alts of the appropriate profession
			$sql = "SELECT name FROM alts a JOIN players p ON a.alt = p.name WHERE p.profession = ? AND a.main = ? UNION SELECT p.name FROM players p WHERE p.profession = ? AND p.name = ?";
			$profCheck = $this->db->query($sql, $prof, $row->name, $prof, $row->name);
			if (count($profCheck) > 0) {
				$blob .= "<highlight>$row->name<end> has\n";
				forEach ($profCheck as $row2) {
					$playerInfo = $this->playerManager->get_by_name($row2->name);
					if ($playerInfo === null) {
						$blob .= "| $row2->name\n";
					} else {
						$blob .= "| $row2->name - $playerInfo->level/<green>$playerInfo->ai_level<end> $playerInfo->breed $playerInfo->profession\n";
					}
				}
				$blob .= "\n";
			}
		}

		$msg = $this->text->make_blob("$prof Search Results", $blob);
		$sendto->reply($msg);
	}

}