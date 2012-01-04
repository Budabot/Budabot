<?php

class Stopwatch {

	/** @Inject */
	public $chatBot;
	
	private $time = 0;

	/**
	 * @Command("stopwatch")
	 * @AccessLevel("guild")
	 * @Description("Add a repeating timer")
	 */
	public function stopwatchCommand($message, $channel, $sender, $sendto) {
		if (preg_match("/^stopwatch start$/i", $message)) {
			$msg = $this->start();
		} else if (preg_match("/^stopwatch stop$/i", $message)) {
			$msg = $this->stop();
		} else {
			return false;
		}
		
		$this->chatBot->send($msg, $sendto);
	}
	
	public function start() {
		if ($this->time != 0) {
			return "The stopwatch is already running.";
		}
	
		$this->time = time();
		return "Stopwatch has been started.";
	}
	
	public function stop() {
		if ($this->time == 0) {
			return "The stopwatch is not running.";
		}
	
		$time = time() - $this->time;
		$this->time = 0;
		
		$timeString = Util::unixtime_to_readable($time);
		
		return "Stopwatch has been stopped. Duration: $timeString";
	}
}

?>
