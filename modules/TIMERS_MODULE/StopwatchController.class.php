<?php

namespace Budabot\User\Modules;

/**
 * Authors: 
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'stopwatch',
 *		accessLevel = 'guild',
 *		description = 'Adds a repeating timer',
 *		help        = 'stopwatch.txt'
 *	)
 */
class StopwatchController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $chatBot;

	/** @Inject */
	public $util;

	private $time = 0;

	/**
	 * @HandlesCommand("stopwatch")
	 * @Matches("/^stopwatch start$/i")
	 */
	public function stopwatchStartCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->time != 0) {
			$msg = "The stopwatch is already running.";
		} else {
			$this->time = time();
			$msg = "Stopwatch has been started.";
		}
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("stopwatch")
	 * @Matches("/^stopwatch stop$/i")
	 */
	public function stopwatchStopCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->time == 0) {
			$msg = "The stopwatch is not running.";
		} else {
			$time = time() - $this->time;
			$this->time = 0;

			$timeString = $this->util->unixtimeToReadable($time);

			$msg = "Stopwatch has been stopped. Duration: <highlight>$timeString<end>.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("stopwatch")
	 * @Matches("/^stopwatch view$/i")
	 */
	public function stopwatchViewCommand($message, $channel, $sender, $sendto, $args) {
		if ($this->time == 0) {
			$msg = "The stopwatch is not running.";
		} else {
			$time = time() - $this->time;

			$timeString = $this->util->unixtimeToReadable($time);

			$msg = "Elapsed time: <highlight>$timeString<end>.";
		}
		$sendto->reply($msg);
	}
}
