<?php
if (preg_match("/^leaveRaffle/i", $message, $arr)) {
	//check inprog and check if already in raffle
	if ($this->vars["Raffles"]["inprog"]) {
		$index = array_search($sender, $this->vars["Raffles"]["rafflees"]);
		if ($index === false) {
			$msg = "You are not currently signed up for the raffle.";
			$this->send($msg, $sendto);
		} else {
			array_splice($this->vars["Raffles"]["rafflees"], $index, 1);
			$msg = "$sender has left the raffle.";
			$this->send($msg, "org");
			$this->send($msg, "prv");
		}
	} else {
		$msg = "A raffle is not in progress.";
		$this->send($msg, $sendto);
	}
}
?>