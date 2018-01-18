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
 *		command     = 'players2', 
 *		accessLevel = 'member', 
 *		description = 'Shows online players and their currently logged alts', 
 *		help        = 'players.txt'
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
	 * @HandlesCommand("players2")
	 * @Matches("/^players2$/i")
	 */
	public function players2Command($message, $channel, $sender, $sendto, $args) {
		$orgData = $this->getPlayers('guild');
		list($orgCount, $orgMain, $orgBlob) = $this->formatData($orgData);

		$privData = $this->getPlayers('priv');
		list($privCount, $privMain, $privBlob) = $this->formatData($privData);

		$totalCount = $orgCount + $privCount;
		$totalMain = $orgMain + $privMain;
		
		$blob = "\n";
		if ($orgCount > 0) {
			$blob .= "<header2>Org Channel ($orgMain)<end>\n";
			$blob .= $orgBlob;
			$blob .= "\n\n";
		}
		if ($privCount > 0) {
			$blob .= "<header2>Private Channel ($privMain)<end>\n";
			$blob .= $privBlob;
			$blob .= "\n\n";
		}

		if ($totalCount > 0) {
			$blob .= "Written by Naturarum (RK2)";
			$msg = $this->text->makeBlob("Players Online ($totalMain)", $blob);
		} else {
			$msg = "Players Online (0)";
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("players")
	 * @Matches("/^players$/i")
	 */
	public function playersCommand($message, $channel, $sender, $sendto, $args) {
		$sql = "
			SELECT p.*, o.channel_type, COALESCE(a.main, o.name) AS pmain
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN players p ON o.name = p.name
			ORDER BY COALESCE(a.main, o.name) ASC";
		$data = $this->db->query($sql);
		$count = count($data);
		$mainCount = 0;
		
		$blob = "";
		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$mainCount++;
					$blob .= "\n<highlight>$row->pmain<end> on\n";
					$currentMain = $row->pmain;
				}

				$priv = "";
				if ($row->channel_type == 'priv') {
					$priv = " <highlight>&lt;Guest&gt;<end>";
				}

				if ($row->profession === null) {
					$blob .= "| $row->name $priv\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof $priv\n";
				}
			}
			$blob .= "\nWritten by Naturarum (RK2)";
			$msg = $this->text->makeBlob("Players Online ($mainCount)", $blob);
		} else {
			$msg = "Players Online (0)";
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("players")
	 * @Matches("/^players (.+)$/i")
	 */
	public function findProfCommand($message, $channel, $sender, $sendto, $args) {
		// Normalize profession information and exit on bad
		$profession = $this->util->getProfessionName($args[1]);
		if (empty($profession)) {
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
		$data = $this->db->query($sql, $profession);
		$count = count($data);
		$mainCount = 0;
		$currentMain = "";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$mainCount++;
					$blob .= "\n<highlight>$row->pmain<end> has\n";
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
			$msg = $this->text->makeBlob("$profession Search Results ($mainCount)", $blob);
		} else {
			$msg = "$profession Search Results (0)";
		}

		$sendto->reply($msg);
	}

	public function formatData($data) {
		$count = count($data);
		$mainCount = 0;
		$currentMain = "";
		$blob = "";

		if ($count > 0) {
			forEach ($data as $row) {
				if ($currentMain != $row->pmain) {
					$mainCount++;
					$blob .= "\n<highlight>$row->pmain<end> on\n";
					$currentMain = $row->pmain;
				}

				if ($row->profession === null) {
					$blob .= "| ($row->name)\n";
				} else {
					$prof = $this->util->getProfessionAbbreviation($row->profession);
					$blob.= "| $row->name - $row->level/<green>$row->ai_level<end> $prof\n";
				}
			}
		}
		
		return [$count, $mainCount, $blob];
	}

	public function getPlayers($channelType) {
		$sql = "
			SELECT p.*, COALESCE(a.main, o.name) AS pmain
			FROM online o
			LEFT JOIN alts a ON o.name = a.alt
			LEFT JOIN players p ON o.name = p.name
			WHERE o.channel_type = ?
			ORDER BY COALESCE(a.main, o.name) ASC";
		return $this->db->query($sql, $channelType);
	}
}