<?php

   /*
   ** Author: Jayanti/Nagahiro (RK2)
   ** Description: Sets 17h07m timer when Viza spawns in pande and lets users check time to next spawn.
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 2011-11-21
   ** Date(last modified): 2011-11-22
   **
   ** Special Thanks To: Tyrence, the Budabot community, and most of the players on RK2 :)
   */

// Check message came from right source
 if ($packet_type == AOCP_MSG_SYSTEM) {
	$message = $args[0];

	// Check content		
	if (preg_match("/Vizaresh has appeared in Pandemonium - The Gauntlet will be opened soon!/i", $message)) {
		// Set Variables
		$timerName = "Gauntlet";
		$timer = time() + Util::parseString('17h6m');
		$name = $chatBot->vars['name'];

		// Reset the timer
		Timer::remove($timerName);
		Timer::add($timerName, $name, 'guild', $timer);
	}
}

?>
