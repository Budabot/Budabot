<?php

namespace Budabot\User\Modules;

use stdClass;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'perks',
 *		accessLevel = 'all',
 *		description = 'Show buff perks',
 *		help        = 'perks.txt'
 *	)
 */
class BuffPerksController {
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
	
	/** @Inject */
	public $playerManager;
	
	/** @Setup */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, "perks");
		
		$perkInfo = $this->getPerkInfo();
		
		$this->db->exec("DELETE FROM perk");
		$this->db->exec("DELETE FROM perk_level");
		$this->db->exec("DELETE FROM perk_level_prof");
		$this->db->exec("DELETE FROM perk_level_buffs");

		$perkId = 1;
		$perkLevelId = 1;
		forEach ($perkInfo as $perk) {
			$this->db->exec("INSERT INTO perk (id, name) VALUES (?, ?)", $perkId, $perk->name);
			
			forEach ($perk->levels as $level) {
				$this->db->exec("INSERT INTO perk_level (id, perk_id, number, min_level) VALUES (?, ?, ?, ?)", $perkLevelId, $perkId, $level->perkLevel, $level->minLevel);
				
				forEach ($level->professions as $profession) {
					$this->db->exec("INSERT INTO perk_level_prof (perk_level_id, profession) VALUES (?, ?)", $perkLevelId, $profession);
				}

				forEach ($level->buffs as $buff => $amount) {
					$this->db->exec("INSERT INTO perk_level_buffs (perk_level_id, skill, amount) VALUES (?, ?, ?)", $perkLevelId, $buff, $amount);
				}
				
				$perkLevelId++;
			}

			$perkId++;
		}
	}
	
	/**
	 * @HandlesCommand("perks")
	 * @Matches("/^perks$/i")
	 * @Matches("/^perks (.*) (\d+)$/i")
	 * @Matches("/^perks (\d+) (.*)$/i")
	 */
	public function buffPerksCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 1) {
			$whois = $this->playerManager->get_by_name($sender);
			if (empty($whois)) {
				$msg = "Could not retrieve whois info for you.";
				$sendto->reply($msg);
				return;
			} else {
				$profession = $whois->profession;
				$minLevel = $whois->level;
			}
		} else {
			$first = $this->util->getProfessionName($args[1]);
			$second = $this->util->getProfessionName($args[2]);
			if (!empty($first)) {
				$profession = $first;
				$minLevel = $args[2];
			} else if (!empty($second)) {
				$profession = $second;
				$minLevel = $args[1];
			} else {
				return false;
			}
		}
		
		$sql = "SELECT
				p.name AS perk_name,
				MAX(pl.number) AS max_perk_level,
				SUM(plb.amount) AS buff_amount,
				plb.skill
			FROM
				perk_level_prof plp
				JOIN perk_level pl ON plp.perk_level_id = pl.id
				JOIN perk_level_buffs plb ON pl.id = plb.perk_level_id
				JOIN perk p ON pl.perk_id = p.id
			WHERE
				plp.profession = ?
				AND pl.min_level <= ?
			GROUP BY
				p.name,
				plb.skill
			ORDER BY
				p.name";
		
		$data = $this->db->query($sql, $profession, $minLevel);
		
		if (empty($data)) {
			$msg = "Could not find any perks matching your criteria.";
		} else {
			$currentPerk = '';
			$buffs = array();
			$blob = '';
			$numPerks = 0;
			forEach ($data as $row) {
				if ($row->perk_name != $currentPerk) {
					$blob .= "\n<header2>$row->perk_name {$row->max_perk_level}<end>\n";
					$currentPerk = $row->perk_name;
					$numPerks += $row->max_perk_level;
				}
				
				$blob .= "$row->skill <highlight>$row->buff_amount<end>\n";
				$buffs[$row->skill] += $row->buff_amount;
			}

			$blob .= "\n------------------------------------\n\n<header2>Total<end> - $numPerks perks\n\n";
			ksort($buffs);
			forEach ($buffs as $skill => $amount) {
				$blob .= "$skill <highlight>$amount<end>\n";
			}
			
			$msg = $this->text->make_blob("Buff Perks for $minLevel $profession", $blob);
		}
		$sendto->reply($msg);
	}
	
	public function getPerkInfo() {
		$path = getcwd() . "/modules/" . $this->moduleName . "/perks.csv";
		$lines = explode("\n", file_get_contents($path));
		$perks = array();
		forEach ($lines as $line) {
			$line = trim($line);
			
			if (empty($line)) {
				continue;
			}
			
			list($name, $perkLevel, $minLevel, $profs, $buffs) = explode("|", $line);
			$perk = $perks[$name];
			if (empty($perk)) {
				$perk = new stdClass;
				$perks[$name] = $perk;
				$perk->name = $name;
			}
			
			$level = new stdClass;
			$perk->levels[$perkLevel] = $level;

			$level->perkLevel = $perkLevel;
			$level->minLevel = $minLevel;
			
			$level->professions = array();
			$professions = explode(",", $profs);
			forEach ($professions as $prof) {
				$profession = $this->util->getProfessionName(trim($prof));
				if (empty($profession)) {
					echo "Error parsing profession: '$prof'\n";
				} else {
					$level->professions []= $profession;
				}
			}
			
			$level->buffs = array();
			$buffs = explode(",", $buffs);
			forEach ($buffs as $buff) {
				$buff = trim($buff);
				$pos = strrpos($buff, " ");

				$skill = substr($buff, 0, $pos + 1);
				$amount = substr($buff, $pos + 1);

				$level->buffs[$skill] = $amount;
			}
		}
		
		return $perks;
	}
}