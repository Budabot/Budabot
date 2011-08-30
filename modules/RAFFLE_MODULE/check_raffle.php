<?php

if (!$chatBot->data["Raffles"]["running"]) {
	// no raffle running, do nothing
	return;
} else if (time() < $chatBot->data["Raffles"]["nextmsgtime"]) {
	// not time to display another reminder yet
	return;
} else if ($chatBot->data["Raffles"]["time"] == $chatBot->data["Raffles"]["nextmsgtime"]) {
	// if there is no time left or we even skipped over the time, end raffle
	endraffle();
	return;
} else {
	show_raffle_reminder();
}

?>