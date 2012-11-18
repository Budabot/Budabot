<?php

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'countdown',
 *		accessLevel = 'guild',
 *		description = 'Start a 5 second countdown',
 *		help        = 'countdown.txt',
 *		alias		= 'cd'
 *	)
 */
class CountdownController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $db;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $accessManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	private $lastCountdown = 0;

	/**
	 * @HandlesCommand("countdown")
	 * @Matches("/^countdown$/i")
	 * @Matches("/^countdown (.+)$/i")
	 */
	public function countdownCommand($message, $channel, $sender, $sendto, $args) {
		$message = "GO GO GO";
		if (count($args) == 2) {
			$message = $args[1];
		}

		if ($this->lastCountdown >= (time() - 30)) {
			$msg = "You can only start a countdown every 30 seconds.";
			$sendto->reply($msg);
			return;
		}

		$this->lastCountdown = time();

		for ($i = 5; $i > 0; $i--) {
			if ($i == 5) {
				$color = "<red>";
			} else if ($i == 4) {
				$color = "<red>";
			} else if ($i == 3) {
				$color = "<orange>";
			} else if ($i == 2) {
				$color = "<orange>";
			} else if ($i == 1) {
				$color = "<orange>";
			}
			$msg = "$color-------&gt; $i &lt;-------<end>";
			$sendto->reply($msg);
			sleep(1);
		}

		$msg = "<green>------&gt; $message &lt;-------<end>";
		$sendto->reply($msg);
	}
}

?>
