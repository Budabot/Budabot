<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'trickle', 
 *		accessLevel = 'all', 
 *		description = 'Shows how much skills you will gain by increasing an ability', 
 *		help        = 'trickle.txt'
 *	)
 */
class TrickleController {

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
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'trickle');
	}

	/**
	 * View trickle skills
	 *
	 * @HandlesCommand("trickle")
	 * @Matches("/^trickle( ([a-zA-Z]+) ([0-9]+)){1,6}$/i")
	 */
	public function trickle1Command($message, $channel, $sender, $sendto, $args) {
		$abilities = array('agi' => 0, 'int' => 0, 'psy' => 0, 'sta' => 0, 'str' => 0, 'sen' => 0);

		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = $this->util->get_ability($array[$i]);
			if ($ability == null) {
				return false;
			}

			$abilities[$ability] = $array[1 + $i];
		}

		$this->processAbilities($abilities, $sendto);
	}
	
	/**
	 * View trickle skills
	 *
	 * @HandlesCommand("trickle")
	 * @Matches("/^trickle( ([0-9]+) ([a-zA-Z]+)){1,6}$/i")
	 */
	public function trickle2Command($message, $channel, $sender, $sendto, $args) {
		$abilities = array('agi' => 0, 'int' => 0, 'psy' => 0, 'sta' => 0, 'str' => 0, 'sen' => 0);

		$array = explode(" ", $message);
		array_shift($array);
		for ($i = 0; isset($array[$i]); $i += 2) {
			$ability = $this->util->get_ability($array[1 + $i]);
			if ($ability == null) {
				return false;
			}

			$abilities[$ability] = $array[$i];
		}

		$this->processAbilities($abilities, $sendto);
	}
	
	private function processAbilities($abilities, $sendto) {
		$msg = "";

		$blob = '';
		forEach ($abilities as $ability => $value) {
			if ($value != 0) {
				$blob .= ucfirst($ability) . " <highlight>" . $value . "<end>, ";
			}
		}
		$blob .= "\n";

		$results = $this->getTrickleResults($abilities);
		$blob .= $this->formatOutput($results, $amount, $abilities);
		$blob .= "\nBy Tyrence (RK2), inspired by the Bebot command of the same name";
		$msg = $this->text->make_blob("Trickle Results", $blob);
		$sendto->reply($msg);
	}
	
	function getTrickleResults($abilities) {
		$sql = "
			SELECT
				groupName,
				name,
				amountAgi,
				amountInt,
				amountPsy,
				amountSta,
				amountStr,
				amountSen,
				(amountAgi * {$abilities['agi']}
					+ amountInt * {$abilities['int']}
					+ amountPsy * {$abilities['psy']}
					+ amountSta * {$abilities['sta']}
					+ amountStr * {$abilities['str']}
					+ amountSen * {$abilities['sen']}) AS amount
			FROM
				trickle
			GROUP BY
				groupName,
				name,
				amountAgi,
				amountInt,
				amountPsy,
				amountSta,
				amountStr,
				amountSen
			HAVING
				amount > 0
			ORDER BY
				groupName,
				name";

		return $this->db->query($sql);
	}
	
	function formatOutput($results, $amount, &$abilities) {
		$msg = "";
		$groupName = "";
		forEach($results as $result) {

			if ($result->groupName != $groupName) {
				$groupName = $result->groupName;
				$msg .= "\n<tab><header2>::: $groupName :::<end>\n";
			}

			$amount = $result->amount / 4;
			$msg .= "$result->name <highlight>$amount<end>";

			/*
			forEach ($abilities as $ability => $value) {
				$ability = ucfirst($ability);
				$abilityField = "amount" . $ability;
				$abilityAmount = $result->$abilityField * 100;
				if ($abilityAmount != 0) {
					$msg .= " (" . $ability . " " . $abilityAmount . "%)";
				}
			}
			*/

			$msg .= "\n";
		}

		return $msg;
	}
}
