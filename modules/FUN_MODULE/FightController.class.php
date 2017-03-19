<?php

namespace Budabot\User\Modules;

use stdClass;

/**
 * Author:
 *  - Tyrence (RK2)
 *  - Mdkdoc420 (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'fight',
 *		accessLevel = 'all',
 *		description = 'Let two people fight against each other',
 *		help        = 'fun_module.txt'
 *	)
 */
class FightController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $text;
	
	/** @Inject */
	public $util;
	
	/**
	 * @HandlesCommand("fight")
	 * @Matches("/^fight (.+) vs (.+)$/i")
	 * @Matches("/^fight (.+) (.+)$/i")
	 */
	public function fightCommand($message, $channel, $sender, $sendto, $args) {
		$player1 = $args[1];
		$player2 = $args[2];

		// Checks if user is trying to get Chuck Norris to fight another Chuck Norris
		if ($this->isChuckNorris($player1) && $this->isChuckNorris($player2)) {
			$msg = "Theres only enough room in this world for one Chuck Norris!";
			$sendto->reply($msg);
			return;
		}

		// This checks if the user is trying to get two of the same people fighting each other
		if (strcasecmp($player1, $player2) == 0) {
			$twin = array(
				"Dejavu?",
				"$player1 can't fight $player2, it may break the voids of space and time!",
				"As much as I'd love to see $player1 punching himself/herself in the face, it just isn't theoretical...");

			$sendto->reply($this->util->randomArrayValue($twin));
			return;
		}

		$fighter1 = $this->getFighter($player1);
		$fighter2 = $this->getFighter($player2);

		$list = "Fight <highlight>$player1<end> VS <highlight>$player2<end> \n\n";
		while ($fighter1->hp > 0 && $fighter2->hp > 0) {
			$list .= $this->doAttack($fighter1, $fighter2);
			$list .= $this->doAttack($fighter2, $fighter1);
			$list .= "\n";
		}

		if ($fighter1->hp > $fighter2->hp) {
			$list .= "\nAnd the winner is ..... <highlight>$player1!<end>";
			$msg = $this->text->makeBlob("$player1 vs $player2....$player1 wins!", $list);
		} else if ($fighter2->hp > $fighter1->hp) {
			$list .= "\nAnd the winner is ..... <highlight>$player2!<end>";
			$msg = $this->text->makeBlob("$player1 vs $player2....$player2 wins!", $list);
		} else {
			$list .= "\nIt's a tie!!";
			$msg = $this->text->makeBlob("$player1 vs $player2....It's a tie!", $list);
		}

		$sendto->reply($msg);
	}

	public function getFighter($name) {
		$fighter = new stdClass;
		$fighter->name = $name;
		if ($this->util->startsWith(strtolower($name), "tyrence")) {
			$fighter->weapon = "bot";
			$fighter->minDamage = 6001;
			$fighter->maxDamage = 7000;
			$fighter->hp = 20000;
		} else if ($this->isChuckNorris($name)) {
			$fighter->weapon = "round house kick";
			$fighter->minDamage = 4001;
			$fighter->maxDamage = 6000;
			$fighter->hp = 20000;
		} else {
			$fighter->weapon = "nerfstick";
			$fighter->minDamage = 1000;
			$fighter->maxDamage = 4000;
			$fighter->hp = 20000;
		}
		return $fighter;
	}

	public function doAttack($attacker, $defender) {
		$dmg = rand($attacker->minDamage, $attacker->maxDamage);
		if ($this->isCriticalHit($attacker, $dmg)) {
			$crit = " <red>Critical Hit!<end>";
		} else {
			$crit = "";
		}

		$defender->hp -= $dmg;
		return "<highlight>{$attacker->name}<end> hit <highlight>{$defender->name}<end> for $dmg of {$attacker->weapon} dmg.$crit\n";
	}

	public function isCriticalHit($fighter, $dmg) {
		return ($dmg / $fighter->maxDamage) > 0.9;
	}

	public function isChuckNorris($name) {
		$name = strtolower($name);
		return $name == "chuck" || $name == "chuck norris" || $name == "chucknorris";
	}
}
