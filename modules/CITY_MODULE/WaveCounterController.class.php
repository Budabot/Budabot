<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'startwave',
 *		accessLevel = 'guild',
 *		description = 'Manually start wave counter',
 *		help        = 'wavecounter.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'stopwave',
 *		accessLevel = 'guild',
 *		description = 'Manually stops wave counter',
 *		help        = 'wavecounter.txt'
 *	)
 *	@DefineCommand(
 *		command     = 'citywave',
 *		accessLevel = 'guild',
 *		description = 'Show the current city wave',
 *		help        = 'wavecounter.txt'
 *	)
 */
class WaveCounterController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	private $wave = null;
	
	/**
	 * @HandlesCommand("startwave")
	 * @Matches("/^startwave$/i")
	 */
	public function startwaveCommand($message, $channel, $sender, $sendto, $args) {
		if (isset($this->wave)) {
			$this->chatBot->sendGuild("A raid is already in progress.");
		} else {
			$this->chatBot->sendGuild("Wave counter started by $sender.");
			$this->wave['time'] = time();
			$this->wave['wave'] = 1;
		}
	}

	/**
	 * @HandlesCommand("stopwave")
	 * @Matches("/^stopwave$/i")
	 */
	public function stopwaveCommand($message, $channel, $sender, $sendto, $args) {
		unset($this->wave);
		$this->chatBot->sendGuild("Wave counter stopped by $sender.");
	}
	
	/**
	 * @HandlesCommand("citywave")
	 * @Matches("/^citywave$/i")
	 */
	public function citywaveCommand($message, $channel, $sender, $sendto, $args) {
		if (!isset($this->wave)) {
			$msg = "There is no raid in progress at this time.";
		} else if ($this->wave['wave'] == 1) {
			$msg = "Waiting for the first wave.";
		} else {
			$msg = "Current wave: " . ($this->wave['wave'] - 1);
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @Event("guild")
	 * @Description("Starts a wave counter when cloak is lowered")
	 */
	public function autoStartWaveCounterEvent($eventObj) {
		if (preg_match("/^Your city in (.+) has been targeted by hostile forces.$/i", $eventObj->message)) {
			$this->chatBot->sendGuild("Wave counter started.");
			$this->wave['time'] = time();
			$this->wave['wave'] = 1;
		}
	}
	
	/**
	 * @Event("2sec")
	 * @Description("Checks timer to see when next wave should come")
	 */
	public function checkWaveCounterTimerEvent($eventObj) {
		if (isset($this->wave)) {
			$stime = $this->wave['time'];
			$now = time();
			$wave = $this->wave['wave'];
			if ($wave != 2) {
				if ($stime >= $now + 13 - $wave * 120 && $stime <= $now + 17 - $wave * 120) {
					if ($wave != 9) {
						$this->chatBot->sendGuild("Wave $wave Incoming.");
					} else {
						$this->chatBot->sendGuild("General Incoming.");
					}
					$wave++;
					$this->wave['wave'] = $wave;
					if ($wave == 10) {
						// if raid is over, delete wave data
						unset($this->wave);
					}
				}
			} else if ($stime >= $now + 13 - 270 && $stime <= $now + 17 - 270) {
				$this->chatBot->sendGuild("Wave $wave Incoming.");
				$wave++;
				$this->wave['wave'] = $wave;
			}
			if ($stime < $now - 10 * 120) {
				unset($this->wave);
			}
		}
	}
}

?>
