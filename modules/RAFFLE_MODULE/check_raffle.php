<?php


{
	if (!$chatBot->data["Raffles"]["running"]) {
		// no raffle running, do nothing
		return;
	}

	// there is a raffle running
	$tleft = $chatBot->data["Raffles"]["time"] - time();
	$time_string = Util::unixtime_to_readable($tleft);
	$owner = $chatBot->data["Raffles"]["owner"];
	$item = $chatBot->data["Raffles"]["item"];
	$count = $chatBot->data["Raffles"]["count"];

	$timesincelastmsg = time() - $chatBot->data["Raffles"]["lastmsgtime"];

	// if there is no time left or we even skipped over the time, end raffle
	if (0 >= $tleft) {
		endraffle($this);
		return;
	}

	// at least 15 seconds should be between messages
	if (15 >= $timesincelastmsg) {
		return;
	}

	// generate an info window
	$blob="<white>Current Members:<end>";
	forEach (array_keys($chatBot->data["Raffles"]["rafflees"]) as $tempName) {
		$blob .= "\n$tempName";
	}
	if (count($chatBot->data["Raffles"]["rafflees"]) == 0) {
		$blob .= "No entrants yet.";
	}

	$blob .= "

	Click <a href='chatcmd:///tell <myname> <symbol>raffle join'>here</a> to join the raffle!
	Click <a href='chatcmd:///tell <myname> <symbol>raffle leave'>here</a> if you wish to leave the raffle.";

	$blob .= "\n\n Time left: $time_string.";

	$link = Text::make_link("here", $blob);
	if (1 < $count) {
		$msg = "<yellow>Reminder:<end> Raffle for $item (count: $count) has $time_string left. Click $link to join.";
	} else {
		$msg = "<yellow>Reminder:<end> Raffle for $item has $time_string left. Click $link to join.";
	}
	// raffle is running quite some time, post only once a minute
	if ((60 <= $tleft) && (60 <= $timesincelastmsg)) {
		$chatBot->data["Raffles"]["lastmsgtime"] = time();
		$chatBot->send($msg, $chatBot->data["Raffles"]["sendto"]);
	} else if ((60 > $tleft) && (15 <= $timesincelastmsg)) {
		$chatBot->data["Raffles"]["lastmsgtime"] = time();
		$chatBot->send($msg, $chatBot->data["Raffles"]["sendto"]);
	}

}

?>