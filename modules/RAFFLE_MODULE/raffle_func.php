<?php

function endraffle() {
	$chatBot = Registry::getInstance('chatBot');

    // just to make sure there is a raffle to end
    if (!$chatBot->data["Raffles"]["running"]) {
        return;
    }
    // indicate that the raffle is over
    $chatBot->data["Raffles"]["running"] = false;

    $item = $chatBot->data["Raffles"]["item"];
    $count = $chatBot->data["Raffles"]["count"];
    $rafflees = array_keys($chatBot->data["Raffles"]["rafflees"]);
    $rafflees_num = count($rafflees);

    if (0 == $rafflees_num) {
        $msg = "<highlight>No one joined the raffle, $item is free for all.";
        $chatBot->data["Raffles"]["lastresult"] = $msg;

        $chatBot->send($msg, $chatBot->data["Raffles"]["sendto"]);
        return;
    }

    // first shuffle the names
    for ($i = 0; $i < 5; $i++) {
        shuffle($rafflees);
    }
    // roll multiple times to generate a list of winners
    $rollcount = 1000 * $rafflees_num;

    for ($i = 0; $i < $rollcount; $i++) {
        // roll a name out of the rafflees and add a rollcount
        $random_name = $rafflees[mt_rand(0, $rafflees_num - 1)];
        $chatBot->data["Raffles"]["rafflees"][$random_name] ++;
    }

    // sort the list depending on roll results
    arsort($chatBot->data["Raffles"]["rafflees"]);

    $blob = "<header>Raffle results<end>\n";
    if (1 == $count) {
        $blob .= "Rolled $rollcount times for $item.\n \n Winner:";
    } else {
        $blob .= "Rolled $rollcount times for $item (count: $count).\n \n Winners:";
    }

    $i = 0;
    forEach ($chatBot->data["Raffles"]["rafflees"] as $char => $rolls) {
        $i++;
        $blob .= "\n$i. $char got $rolls rolls.";
        if ($i == $count) {
            $blob .= "\n-------------------------\n Unlucky:";
        }
    }
    $results = Text::make_blob("Detailed results", $blob);

    if (1 == $count) {
        $msg = "The raffle for $item is over. Winner: ";
    } else {
       $msg = "The raffle for $item (count: $count) is over. Winners: ";
    }

    $i = 0;
    forEach ($chatBot->data["Raffles"]["rafflees"] as $char => $rolls) {
        $i++;
        $msg .= "{$char}!";
        if ($i != $count) {
            $msg .= ", ";
        } else {
            break;
        }
    }
    $msg .= " Congratulations. $results";
    $chatBot->data["Raffles"]["lastresult"] = $msg;
    $chatBot->send($msg, $chatBot->data["Raffles"]["sendto"]);
}

function get_next_time($endtime) {
	$tleft = $endtime - time();
	if ($tleft <= 0) {
		$ret = false;
	} else if ($tleft <= 30) {
		$ret = $endtime;
	} else if ($tleft <= 60) {
		$ret = $endtime - 30;
	} else if ($tleft <= 120) {
		$ret = $endtime - 60;
	} else {
		$ret = $endtime - floor(($tleft - 30) / 60) * 60;
	}
	return $ret;
}

function show_raffle_reminder() {
	$chatBot = Registry::getInstance('chatBot');

	// there is a raffle running
	$time_string = Util::unixtime_to_readable($chatBot->data["Raffles"]["time"] - $chatBot->data["Raffles"]["nextmsgtime"]);
	$item = $chatBot->data["Raffles"]["item"];
	$count = $chatBot->data["Raffles"]["count"];

	// generate an info window
	$blob = "<white>Current Members:<end>";
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

	$link = Text::make_blob("here", $blob);
	if (1 < $count) {
		$msg = "<yellow>Reminder:<end> Raffle for $item (count: $count) has $time_string left. Click $link to join.";
	} else {
		$msg = "<yellow>Reminder:<end> Raffle for $item has $time_string left. Click $link to join.";
	}

	$chatBot->send($msg, $chatBot->data["Raffles"]["sendto"]);
	$chatBot->data["Raffles"]["nextmsgtime"] = get_next_time($chatBot->data["Raffles"]["time"]);
}

?>
