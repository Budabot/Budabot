<?php
if (preg_match("/^joinRaffle/i", $message, $arr)) {
	//check inprog and check not already in raffle
	if (!$this->vars["Raffles"]["inprog"]) {
		$msg = "No raffle in progress.";
		bot::send($msg, $sendto);
	} else if (array_search($sender, $this->vars["Raffles"]["rafflees"]) !== false) {
		$msg = "You are already in the raffle.";
		bot::send($msg, $sendto);
	} else {
		$this->vars["Raffles"]["rafflees"][] = $sender;
		$msg = "$sender has entered the raffle.";
		bot::send($msg, "org");
	}
}
?>