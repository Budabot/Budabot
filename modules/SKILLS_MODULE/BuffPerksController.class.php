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
	public $playerManager;
	
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
				$msg = "Choose a valid profession.";
				$sendto->reply($msg);
				return;
			}
		}
		
		$blob = '';
		$perkInfo = $this->getPerkInfo();
		$buffs = array();
		$numPerks = 0;
		forEach ($perkInfo as $perk) {
			$maxPerkLevel = 0;
			$perkBuffs = array();
			$maxLevel = true;
			forEach ($perk->levels as $level) {
				if ($level->minLevel > $minLevel) {
					$maxLevel = false;
					break;
				}
				
				if (!in_array($profession, $level->professions)) {
					break;
				}
				
				$numPerks++;
				$maxPerkLevel = $level->perkLevel;

				forEach ($level->buffs as $buff => $amount) {
					$perkBuffs[$buff] += $amount;
					$buffs[$buff] += $amount;
				}
			}
			
			if ($maxPerkLevel > 0) {
				$maxLevelIndicator = '';
				if ($maxLevel) {
					$maxLevelIndicator = '*';
				}
				$blob .= "\n<header2>$perk->name {$maxPerkLevel}{$maxLevelIndicator}<end>\n";
				forEach ($perkBuffs as $buff => $amount) {
					$blob .= "$buff <highlight>$amount<end>\n";
				}
			}
		}
		
		if (empty($buffs)) {
			$msg = "Could not find any perks matching your criteria.";
		} else {
			$blob .= "\n------------------------------------\n\n<header2>Totals<end> - $numPerks perks\n\n";		
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
				list($skill, $amount) = explode(" ", trim($buff));
				if ($skill == 'Treat') {
					$name = 'Treatment';
				} else if ($skill == 'FirstAid') {
					$name = 'First Aid';
				} else {
					$name = $this->util->getAbility($skill, true);
				}
				if (empty($name)) {
					echo "Error parsing skill: '$name'\n";
				} else {
					$level->buffs[$name] = $amount;
				}
			}
		}
		
		return $perks;
	}
}