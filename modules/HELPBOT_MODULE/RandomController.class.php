<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'random', 
 *		accessLevel = 'all', 
 *		description = 'Randomize a list of names/items', 
 *		help        = 'random.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'roll', 
 *		accessLevel = 'all', 
 *		description = 'Roll a random number', 
 *		help        = 'roll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'flip', 
 *		accessLevel = 'all', 
 *		description = 'Flip a coin', 
 *		help        = 'roll.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'verify', 
 *		accessLevel = 'all', 
 *		description = 'Verifies a flip/roll', 
 *		help        = 'roll.txt'
 *	)
 */
class RandomController {

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
	public $util;
	
	/** @Inject */
	public $settingManager;
	
	/**
	 * @Setting("time_between_rolls")
	 * @Description("How much time is required between rolls from the same person")
	 * @Visibility("edit")
	 * @Type("time")
	 * @Options("10s;30s;60s;90s")
	 */
	public $defaultTimeBetweenRolls = "30s";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'roll');
	}
	
	/**
	 * @HandlesCommand("random")
	 * @Matches("/^random (.+)$/i")
	 */
	public function randomCommand($message, $channel, $sender, $sendto, $args) {
		$text = explode(" ", trim($args[1]));
		$low = 0;
		$high = count($text) - 1;
		while (true) {
			$random = rand($low, $high);
			if (!isset($marked[$random])) {
				$count++;
				$list []= $text[$random];
				$marked[$random] = 1;
				if (count($marked) == count($text)) {
					break;
				}
			}
			$i = $low;
			while (true) {
				if ($marked[$i] != 1) {
					$low = $i;
					break;
				} else {
					$i++;
				}
			}
			$i = $high;
			while (true) {
				if ($marked[$i] != 1) {
					$high = $i;
					break;
				} else {
					$i--;
				}
			}
		}
		
		$sendto->reply(implode(" ", $list));
	}

	/**
	 * @HandlesCommand("roll")
	 * @Matches("/^roll ([0-9]+)$/i")
	 * @Matches("/^roll ([0-9]+) ([0-9]+)$/i")
	 */
	public function rollNumericCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 3) {
			$min = $args[1];
			$max = $args[2];
		} else {
			$min = 1;
			$max = $args[1];
		}
		
		if ($min >= $max) {
			$msg = "The first number cannot be higher than or equal to the second number.";
		} else {
			$timeBetweenRolls = $this->settingManager->get('time_between_rolls');
			$row = $this->db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - $timeBetweenRolls);
			if ($row === null) {
				$options = array();
				for ($i = $min; $i <= $max; $i++) {
					$options []= $i;
				}
				list($ver_num, $result) = $this->roll($sender, $options);
				$msg = "The roll is <highlight>$result<end> between $min and $max. To verify do /tell <myname> verify $ver_num";
			} else {
				$msg = "You can only flip or roll once every $timeBetweenRolls seconds.";
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("roll")
	 * @Matches("/^roll (.+)$/i")
	 */
	public function rollNamesCommand($message, $channel, $sender, $sendto, $args) {
		$names = $args[1];
		$timeBetweenRolls = $this->settingManager->get('time_between_rolls');
		$row = $this->db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - $timeBetweenRolls);
		if ($row === null) {
			$options = explode(' ', $names);
			list($ver_num, $result) = $this->roll($sender, $options);
			$msg = "The roll is <highlight>$result<end> out of possible options: $names. To verify do /tell <myname> verify $ver_num";
		} else {
			$msg = "You can only flip or roll once every $timeBetweenRolls seconds.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("flip")
	 * @Matches("/^flip$/i")
	 */
	public function flipCommand($message, $channel, $sender, $sendto, $args) {
		$timeBetweenRolls = $this->settingManager->get('time_between_rolls');
		$row = $this->db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - $timeBetweenRolls);
		if ($row === null) {
			$options = array('heads', 'tails');
			list($ver_num, $result) = $this->roll($sender, $options);
			$msg = "The coin landed <highlight>$result<end>. To verify do /tell <myname> verify $ver_num";
		} else {
			$msg = "You can only flip or roll once every $timeBetweenRolls seconds.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("verify")
	 * @Matches("/^verify ([0-9]+)$/i")
	 */
	public function verifyCommand($message, $channel, $sender, $sendto, $args) {
		$id = $args[1];
		$row = $this->db->queryRow("SELECT * FROM roll WHERE `id` = ?", $id);
		if ($row === null) {
			$msg = "Verify number <highlight>$id<end> does not exist.";
		} else {
			$time = $this->util->unixtime_to_readable(time() - $row->time);
			$msg = "<highlight>$row->result<end> rolled by <highlight>$row->name<end> $time ago. Possible options: ";
			$msg .= str_replace("|", " ", $row->options) . ".";
		}

		$sendto->reply($msg);
	}
	
	public function roll($sender, $options) {
		$result = $this->util->rand_array_value($options);
		$this->db->exec("INSERT INTO roll (`time`, `name`, `options`, `result`) VALUES (?, ?, ?, ?)", time(), $sender, implode($options, "|"), $result);
		return array($this->db->lastInsertId(), $result);
	}
}
