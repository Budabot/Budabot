<?php

/**
 * Authors: 
 *	- Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'axp', 
 *		accessLevel = 'all', 
 *		description = 'Show axp needed for specified level(s)', 
 *		help        = 'xp.txt'
 *	)
 */
class AXPController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	private $axp = array();
	
	public function __construct() {
		//Set the required axp for every alienlvl
		$this->axp[1] = 1500;
		$this->axp[2] = 9000;
		$this->axp[3] = 22500;
		$this->axp[4] = 42000;
		$this->axp[5] = 67500;
		$this->axp[6] = 99000;
		$this->axp[7] = 136500;
		$this->axp[8] = 180000;
		$this->axp[9] = 229500;
		$this->axp[10] = 285000;
		$this->axp[11] = 346500;
		$this->axp[12] = 414000;
		$this->axp[13] = 487500;
		$this->axp[14] = 567000;
		$this->axp[15] = 697410;
		$this->axp[16] = 857814;
		$this->axp[17] = 1055112;
		$this->axp[18] = 1297787;
		$this->axp[19] = 1596278;
		$this->axp[20] = 1931497;
		$this->axp[21] = 2298481;
		$this->axp[22] = 2689223;
		$this->axp[23] = 3092606;
		$this->axp[24] = 3494645;
		$this->axp[25] = 3879056;
		$this->axp[26] = 4228171;
		$this->axp[27] = 4608707;
		$this->axp[28] = 5023490;
		$this->axp[29] = 5475604;
		$this->axp[30] = 5968409;
		$this->axp[31] = 0;  // don't remove, needed
	}
	
	/**
	 * @HandlesCommand("axp")
	 * @Matches("/^axp$/i")
	 */
	public function axpListCommand($message, $channel, $sender, $sendto, $args) {
		$blob = "<u>AI Lvl - AXP   - Rank         - Lvl Req.</u>\n";
		$blob .= " 1 -      1.500 - Fledgling - 5\n";
		$blob .= " 2 -     9.000 - Amateur - 15\n";
		$blob .= " 3 -    22.500 - Beginner - 25\n";
		$blob .= " 4 -    42.000 - Starter - 35\n";
		$blob .= " 5 -    67.500 - Newcomer - 45\n";
		$blob .= " 6 -    99.000 - Student - 55\n";
		$blob .= " 7 -   136.500 - Common - 65\n";
		$blob .= " 8 -   180.000 - Intermediate - 75\n";
		$blob .= " 9 -   229.500 - Mediocre - 85\n";
		$blob .= "10 -   285.000 - Fair - 95\n";
		$blob .= "11 -   346.500 - Able - 105\n";
		$blob .= "12 -   414.000 - Accomplished - 110\n";
		$blob .= "13 -   487.500 - Adept - 115\n";
		$blob .= "14 -   567.000 - Qualified - 120\n";
		$blob .= "15 -   697.410 - Competent - 125\n";
		$blob .= "16 -   857.814 - Suited - 130\n";
		$blob .= "17 - 1.055.112 - Talented - 135\n";
		$blob .= "18 - 1.297.787 - Trustworthy - 140\n";
		$blob .= "19 - 1.596.278 - Supporter - 145\n";
		$blob .= "20 - 1.931.497 - Backer - 150\n";
		$blob .= "21 - 2.298.481 - Defender - 155\n";
		$blob .= "22 - 2.689.223 - Challenger - 160\n";
		$blob .= "23 - 3.092.606 - Patron - 165\n";
		$blob .= "24 - 3.494.645 - Protector - 170\n";
		$blob .= "25 - 3.879.056 - Medalist - 175\n";
		$blob .= "26 - 4.228.171 - Champ - 180\n";
		$blob .= "27 - 4.608.707 - Hero - 185\n";
		$blob .= "28 - 5.023.490 - Guardian - 190\n";
		$blob .= "29 - 5.475.604 - Vanquisher - 195\n";
		$blob .= "30 - 5.968.409 - Vindicator - 200\n";

		$msg = $this->text->make_blob("Alien Experience", $blob);

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("axp")
	 * @Matches("/^axp ([0-9]+)$/i")
	 */
	public function axpSingleCommand($message, $channel, $sender, $sendto, $args) {
		$level = $args[1];
		if ($level >= 0 && $level <= 30) {
			$msg = "At AI level <highlight>$level<end> you need <highlight>".number_format($this->axp[$level + 1])."<end> AXP to level up.";
		} else {
			$msg = "AI level must be between 0 and 30.";
		}

		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("axp")
	 * @Matches("/^axp ([0-9]+) ([0-9]+)$/i")
	 */
	public function axpDoubleCommand($message, $channel, $sender, $sendto, $args) {
		$startLevel = $args[1];
		$endLevel = $args[2];
		if ($startLevel >= 0 && $startLevel <= 30 && $endLevel >= 0 && $endLevel <= 30) {
			if ($startLevel <= $endLevel) {
				for ($i = $startLevel + 1; $i <= $endLevel; $i++) {
					$axp_comp += $this->axp[$i];
				}

				$msg = "From the beginning of AI level <highlight>$startLevel<end> to AI level <highlight>$endLevel<end> you need <highlight>".number_format($axp_comp)."<end> AXP to level up.";
			} else {
				$msg = "The start level cannot be higher than the end level.";
			}
		} else {
			$msg = "AI level must be between 0 and 30.";
		}

		$sendto->reply($msg);
	}
}

?>
