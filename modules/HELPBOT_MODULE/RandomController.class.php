<?php

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
				$newtext .= " $count: ".$text[$random];
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
		
		$sendto->reply($newtext);
	}

	/**
	 * @HandlesCommand("roll")
	 * @Matches("/^roll ([0-9]+)$/i")
	 * @Matches("/^roll ([0-9]+) ([0-9]+)$/i")
	 */
	public function rollSingleCommand($message, $channel, $sender, $sendto, $args) {
		if (count($args) == 3) {
			$min = $args[1];
			$max = $args[2];
		} else {
			$min = 1;
			$max = $args[1];
		}
		
		if ($max >= getrandmax()) {
			$msg = "The maximum number that the roll number can be is <highlight>".getrandmax()."<end>.";
		} else if ($min >= $max) {
			$msg = "The first number cannot be higher than or equal to the second number.";
		} else {
			$row = $this->db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - 30);
			if ($row === null) {
				$num = rand($min, $max);
				$this->db->exec("INSERT INTO roll (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (?, ?, ?, ?, ?, ?)", time(), $sender, 1, $min, $max, $num);
				$ver_num = $this->db->lastInsertId();
				$msg = "Between $min and $max I rolled a <highlight>$num<end>, to verify do /tell <myname> verify $ver_num";
			} else {
				$msg = "You can only flip or roll once every 30 seconds.";
			}
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("flip")
	 * @Matches("/^flip$/i")
	 */
	public function flipCommand($message, $channel, $sender, $sendto, $args) {
		$row = $this->db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - 30);
		if ($row === null) {
			$flip = rand(1, 2);
			$this->db->exec("INSERT INTO roll (`time`, `name`, `type`, `result`) VALUES (?, ?, ?, ?)", time(), $sender, 0, $flip);
			$ver_num = $this->db->lastInsertId();
			if ($flip == 1) {
				$msg = "The coin landed <highlight>heads<end>, to verify do /tell <myname> verify $ver_num";
			} else {
				$msg = "The coin landed <highlight>tails<end>, to verify do /tell <myname> verify $ver_num";
			}
		} else {
			$msg = "You can only flip or roll once every 30 seconds.";
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
			$msg = "That verify number does not exist.";
		} else {
			$time = time() - $row->time;
			$msg = "$time seconds ago I told <highlight>$row->name<end>: ";
			if ($row->type == 0) {
				if ($row->result == 1) {
					$msg .= "The coin landed <highlight>heads<end>.";
				} else {
					$msg .= "The coin landed <highlight>tails<end>.";
				}
			} else {
				$msg .= "Between $row->start and $row->end I rolled a <highlight>$row->result<end>.";
			}
		}

		$sendto->reply($msg);
	}
}
