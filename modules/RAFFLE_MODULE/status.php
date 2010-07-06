<?php
if (preg_match("/^raffleStatus/i", $message, $arr)) {
	if ($this->vars["Raffles"]["inprog"]) {
		$msg = "<white>Current Members:<end>";
		forEach ($this->vars["Raffles"]["rafflees"] as $tempName) {
		   $msg .= "$tempName";
		}
		if (count($this->vars["Raffles"]["rafflees"]) == 0) {
		   $msg .= "No entrants yet.";
		}
		
		$msg .= "

Click <a href='chatcmd:///tell <myname> joinRaffle'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> leaveRaffle'>here</a> if you wish to leave the raffle.";

		$tleft = $this->vars["Raffles"]["time"] - time();
		$msg .= "\n\n Time left: $tleft seconds.";

		$link = $this->makeLink("Raffle Status", $msg);
		$this->send($link, $sendto);
	} else {
		$msg = "A raffle is not in progress.";
		$this->send($msg, $sendto);
	}
}
?>