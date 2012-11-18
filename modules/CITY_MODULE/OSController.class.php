<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 */
class OSController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $timerController;
	
	/**
	 * @Event("orgmsg")
	 * @Description("Sets a timer when an OS/AS is launched")
	 */
	public function osTimerEvent($eventObj) {
		// create a timer for 15m when an OS/AS is launched (so org knows when they can launch again)
		// [Org Msg] Blammo! Player has launched an orbital attack!

		if (preg_match("/^Blammo! (.+) has launched an orbital attack!$/i", $eventObj->message, $arr)) {
			$orgName = $this->chatBot->vars["my_guild"];

			$launcher = $arr[1];

			for ($i = 1; $i <= 10; $i++) {
				$name = "$orgName OS/AS $i";
				if ($this->timerController->get($name) == null) {
					$runTime = 15 * 60; // set timer for 15 minutes
					$msg = $this->timerController->addTimer($launcher, $name, $runTime, 'guild');
					$this->chatBot->sendGuild($msg);
					break;
				}
			}
		}
	}
}

?>
