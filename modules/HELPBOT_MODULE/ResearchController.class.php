<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *	- Jaqueme
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'research', 
 *		accessLevel = 'all', 
 *		description = 'Show info on Research', 
 *		help        = 'research.txt'
 *	)
 */
class ResearchController {

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
		$this->db->loadSQLFile($this->moduleName, 'research');
	}

	/**
	 * @HandlesCommand("research")
	 * @Matches("/^research ([0-9]+)$/i")
	 */
	public function researchSingleCommand($message, $channel, $sender, $sendto, $args) {
		$level = $args[1];
		if ($level < 1 || $level > 10) {
			return false;
		} else {
			$sql = "SELECT * FROM research WHERE level = ?";
			$row = $this->db->queryRow($sql, $level);

			$levelcap = $row->levelcap;
			$sk = $row->sk;
			$xp = $sk * 1000;
			$capxp = round($xp * .05);
			$capsk = round($sk * .05);
			$xp = number_format($xp);
			$sk = number_format($sk);

			$blob = "You must be <highlight>Level $levelcap<end> to reach <highlight>Research Level $level<end>.\n";
			$blob .= "You need <highlight>$sk SK<end> to reach <highlight>Research Level $level<end> per research line.\n\n";
			$blob .= "This equals <highlight>$xp XP<end>.\n\n";
			$blob .= "Your research will cap at <highlight>~$capxp XP<end> or <highlight>~$capsk SK<end>.";
			$msg = $this->text->make_blob("XP/SK Needed for Research Levels", $blob);
		}

		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("research")
	 * @Matches("/^research ([0-9]+) ([0-9]+)$/i")
	 */
	public function researchDoubleCommand($message, $channel, $sender, $sendto, $args) {
		$lolevel = $args[1];
		$hilevel = $args[2];
		if ($lolevel < 0 || $lolevel > 10 || $hilevel < 0 || $hilevel > 10) {
			return false;
		} else {
			$sql =
				"SELECT
					SUM(sk) totalsk,
					MAX(levelcap) levelcap
				FROM
					research
				WHERE
					level > ? AND level <= ?";
			$row = $this->db->queryRow($sql, $lolevel, $hilevel);

			$xp = number_format($row->totalsk * 1000);
			$sk = number_format($row->totalsk);

			$blob = "You must be <highlight>Level $row->levelcap<end> to reach Research Level <highlight>$hilevel.<end>\n";
			$blob .= "It takes <highlight>$sk SK<end> to go from Research Level <highlight>$lolevel<end> to Research Level <highlight>$hilevel<end> per research line.\n\n";
			$blob .= "This equals <highlight>$xp XP<end>.";
			$msg = $this->text->make_blob("XP/SK Needed for Research Levels", $blob);
		}

		$sendto->reply($msg);
	}
}
