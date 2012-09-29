<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *	- Derroylo (RK2)
 *	- Legendadv (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'level', 
 *		accessLevel = 'all', 
 *		description = 'Show level ranges', 
 *		help        = 'level.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'missions', 
 *		accessLevel = 'all', 
 *		description = 'Shows what ql missions a character can roll', 
 *		help        = 'missions.txt',
 *		alias       = 'mission'
 *	)
 *	@DefineCommand(
 *		command     = 'xp', 
 *		accessLevel = 'all', 
 *		description = 'Show xp/sk needed for specified level(s)', 
 *		help        = 'xp.txt',
 *		alias       = 'sk'
 *	)
 *	@DefineCommand(
 *		command     = 'capxp', 
 *		accessLevel = 'all', 
 *		description = 'Show how much xp you need to cap', 
 *		help        = 'capxp.txt',
 *		alias       = 'capsk'
 *	)
 */
class LevelController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;
	
	/** @Inject */
	public $commandAlias;
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'levels');
		
		$this->commandAlias->register($this->moduleName, "level", "pvp");
		$this->commandAlias->register($this->moduleName, "level", "lvl");
	}
	
	/**
	 * @HandlesCommand("capxp")
	 * @Matches("/^capxp ([0-9]+) ([0-9]+)$/i")
	 */
	public function capxpCommand($message, $channel, $sender, $sendto, $args) {
		$reward = $args[1];
		$level = $args[2];

		if ($level > 219 || $level < 1) {
			$sendto->reply("Level cannot be greater than 219 or less than 1.");
			return;
		}

		$row = $this->get_level_info($level);

		if ($level < 200) {
			$xp = $row->xpsk;
			$research = (1-(($xp*.2)/$reward))*100;
		} else {
			$sk = $row->xpsk;
			$research = (1-(($sk*.2)/$reward))*100;
		}
		if ($research < 0) {
			$research = 0;
		}

		if ($level < 200) {
			$msg = "At level <highlight>".number_format($level)."<end> you need <highlight>".number_format($xp)."<end> XP to level. With a mission reward of <highlight>".number_format($reward)."<end> XP, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum XP from this mission reward.";
		} else {
			$msg = "At level <highlight>".number_format($level)."<end> you need <highlight>".number_format($sk)."<end> SK to level. With a mission reward of <highlight>".number_format($reward)."<end> SK, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum SK from this mission reward.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("level")
	 * @Matches("/^level ([0-9]+)$/i")
	 */
	public function levelCommand($message, $channel, $sender, $sendto, $args) {
		$level = $args[1];
		if (($row = $this->get_level_info($level)) != false) {
			$msg = "<white>L $row->level: team {$row->teamMin}-{$row->teamMax}<end><highlight> | <end><cyan>PvP {$row->pvpMin}-{$row->pvpMax}<end><highlight> | <end><yellow>".number_format($row->xpsk)." XP/SK<end><highlight> | <end><orange>Missions {$row->missions}<end><highlight> | <end><blue>{$row->tokens} token(s)<end>";
		} else {
			$msg = "Level must be between <highlight>1<end> and <highlight>220<end>.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("missions")
	 * @Matches("/^missions ([0-9]+)$/i")
	 */
	public function missionsCommand($message, $channel, $sender, $sendto, $args) {
		$missionQl = $args[1];

		if ($missionQl > 0 && $missionQl <= 250) {
			$msg = "QL{$missionQl} missions can be rolled from players who are these levels:";

			forEach ($this->find_all_levels() as $row) {
				$array = explode(",", $row->missions);
				if (in_array($missionQl, $array)) {
					$msg .= " " . $row->level;
				}
			}
		} else {
			$msg = "Missions are only available between QL1 and QL250.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("xp")
	 * @Matches("/^xp ([0-9]+)$/i")
	 */
	public function xpSingleCommand($message, $channel, $sender, $sendto, $args) {
		$level = $args[1];
		if (($row = $this->get_level_info($level)) != false) {
			if ($level >= 200) {
				$msg = "At level <highlight>{$row->level}<end> you need <highlight>".number_format($row->xpsk)."<end> SK to level up.";
			} else {
				$msg = "At level <highlight>{$row->level}<end> you need <highlight>".number_format($row->xpsk)."<end> XP to level up.";
			}
		} else {
			$msg = "Level must be between 1 and 219.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("xp")
	 * @Matches("/^xp ([0-9]+) ([0-9]+)$/i")
	 */
	public function xpDoubleCommand($message, $channel, $sender, $sendto, $args) {
		$minLevel = $args[1];
		$maxLevel = $args[2];
		if ($minLevel >= 1 && $minLevel <= 220 && $maxLevel >= 1 && $maxLevel <= 220) {
			if ($minLevel < $maxLevel) {
				$data = $this->db->query("SELECT * FROM levels WHERE level >= ? AND level < ?", $minLevel, $maxLevel);
				$xp = 0;
				$sk = 0;
				forEach ($data as $row) {
					if ($row->level < 200) {
						$xp += $row->xpsk;
					} else {
						$sk += $row->xpsk;
					}
				}
				if ($sk > 0 && $xp > 0) {
					$msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>".number_format($xp)."<end> XP and <highlight>".number_format($sk)."<end> SK.";
				} else if ($sk > 0) {
					$msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>" .number_format($sk)."<end> SK.";
				} else if ($xp > 0) {
					$msg = "From the beginning of <highlight>$minLevel<end> to <highlight>$maxLevel<end>, you need <highlight>".number_format($xp)."<end> XP.";
				}
			} else {
				$msg = "The start level cannot be higher than the end level.";
			}
		} else {
			$msg = "Level must be between 1 and 220.";
		}

		$sendto->reply($msg);
	}

	public function get_level_info($level) {
		$sql = "SELECT * FROM levels WHERE level = ?";
		return $this->db->queryRow($sql, $level);
	}

	public function find_all_levels() {
		$sql = "SELECT * FROM levels ORDER BY level";
		return $this->db->query($sql);
	}
}

?>
