<?php

if (preg_match("/^(rtimer add|rtimer) ([a-z0-9]+) ([a-z0-9]+) (.+)$/i", $message, $arr)) {
	$initialTimeString = $arr[2];
	$timeString = $arr[3];
	$timerName = $arr[4];
	
	$timer = Timer::get($timerName);
	if ($timer != null) {
		$msg = "A Timer with the name <highlight>$timerName<end> is already running.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$initialRunTime = Util::parseTime($initialTimeString);
	$runTime = Util::parseTime($timeString);

	if ($runTime < 1) {
		$msg = "You must enter a valid time parameter for the run time.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	if ($initialRunTime < 1) {
		$msg = "You must enter a valid time parameter for the initial run time.";
		$chatBot->send($msg, $sendto);
		return;
	}

    $timer = time() + $initialRunTime;

	Timer::add($timerName, $sender, $type, $timer, "repeating", $runTime);

	$initialTimerSet = Util::unixtime_to_readable($initialRunTime);
	$timerSet = Util::unixtime_to_readable($runTime);
	$msg = "Repeating timer <highlight>$timerName<end> will go off in $initialTimerSet and repeat every $timerSet.";
		
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>