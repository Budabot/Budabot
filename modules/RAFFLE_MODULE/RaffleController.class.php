<?php

/**
 * Authors: 
 *	- Mindrila (RK1)
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this class contains:
 *	@DefineCommand(
 *		command     = 'raffle',
 *		accessLevel = 'all',
 *		description = 'Raffle off items to players',
 *		help        = 'raffle.txt'
 *	)
 */
class RaffleController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $accessManager;

	/** @Inject */
	public $text;

	/** @Inject */
	public $util;
	
	/**
	 * @Setup
	 */
	public function setup() {
		if (!isset($this->raffles)) {
			$this->raffles = array(
				"running" => false,
				"owner" => null,
				"item" => null,
				"count" => null,
				"time" => null,
				"rafflees" => null,
				"lastresult" => null,
				"nextmsgtime" => null,
				"sendto" => null
			);
		}
	
		$this->settingManager->add($this->moduleName, "defaultraffletime", "How long the raffle should go for", "edit", "time", '3m', '1m;2m;3m;4m;5m', '', 'mod', "raffle.txt");
	}
	
	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle start (\d+) (.+)$/i")
	 * @Matches("/^raffle start (.+)$/i")
	 */
	public function raffleStartCommand($message, $channel, $sender, $sendto, $args) {
		if ("msg" == $channel) {
			$msg = "You can't start a raffle in tells, please use org-chat or private channel.";
			$sendto->reply($msg);
			return;
		}

		if ($this->raffles["running"]) {
			$msg = "There is already a raffle in progress.";
			$sendto->reply($msg);
			return;
		}

		if (count($args) == 3) {
			$item = $args[2];
			$count = $args[1];
		} else {
			$item = $args[1];
			$count = 1;
		}
		$seconds = $this->settingManager->get("defaultraffletime");
		$timeString = $this->util->unixtime_to_readable($seconds);

		$this->raffles = array(
			"running" => true,
			"owner" => $sender,
			"item" => $item,
			"count" => $count,
			"time" => time() +  $seconds,
			"rafflees" => array(),
			"lastresult" => null,
			"sendto" => $sendto
		);
		
		$joinLink = $this->text->make_chatcmd("here", "/tell <myname> raffle join");
		$leaveLink = $this->text->make_chatcmd("here", "/tell <myname> raffle leave");

		$jnRflMsg = "<white>A raffle for $item (count: $count) has been started by $sender!<end>

	Click $joinLink to join the raffle!
	Click $leaveLink if you wish to leave the raffle.";
		$link = $this->text->make_blob("here", $jnRflMsg, 'Raffle');
		$msg = "
	-----------------------------------------------------------------------
	A raffle for $item (count: $count) has been started by $sender!
	Click $link to join the raffle. Raffle will end in $timeString.
	-----------------------------------------------------------------------";

		$this->raffles["nextmsgtime"] = $this->get_next_time($this->raffles["time"]);
		$sendto->reply($msg);
	}

	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle cancel$/i")
	 */
	public function raffleCancelCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->raffles["running"]) {
			$msg = "There is no active raffle.";
			$sendto->reply($msg);
			return;
		}

		if (($this->raffles["owner"] != $sender) && !$this->accessManager->checkAccess($sender, "mod")) {
			$msg = "Only the owner or a moderator may cancel the raffle.";
			$sendto->reply($msg);
			return;
		}
		$sendtobuffer = $this->raffles["sendto"];
		$this->raffles = array(
			"running" => false,
			"owner" => null,
			"item" => null,
			"count" => null,
			"time" => null,
			"rafflees" => null,
			"lastresult" => "The last raffle was cancelled.",
			"sendto" => $sendtobuffer
		);

		$msg = "The raffle was cancelled.";
		$this->raffles["sendto"]->reply($msg);
	}
	
	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle end$/i")
	 */
	public function raffleEndCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->raffles["running"]) {
			$msg = "There is no active raffle.";
			$sendto->reply($msg);
			return;
		}

		if (($this->raffles["owner"] != $sender) && !$this->accessManager->checkAccess($sender, "mod")) {
			$msg = "Only the owner or a moderator may end the raffle.";
			$sendto->reply($msg);
			return;
		}

		$this->endraffle();
	}
	
	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle result$/i")
	 */
	public function raffleResultCommand($message, $channel, $sender, $sendto, $args) {
		if (!isset ($this->raffles["lastresult"])) {
			$msg = "Last raffles result could not be retrieved.";
			$sendto->reply($msg);
			return;
		}

		$sendto->reply("Last raffle result: ".$this->raffles["lastresult"]);
	}
	
	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle join$/i")
	 */
	public function raffleJoinCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->raffles["running"]) {
			$msg = "There is no active raffle.";
			$sendto->reply($msg);
			return;
		}

		if (isset($this->raffles["rafflees"][$sender])) {
			$msg = "You are already in the raffle.";
			$sendto->reply($msg);
			return;
		}

		$this->raffles["rafflees"][$sender] = 0;
		$msg = "$sender has entered the raffle.";
		$this->raffles["sendto"]->reply($msg);
	}
	
	/**
	 * @HandlesCommand("raffle")
	 * @Matches("/^raffle leave$/i")
	 */
	public function raffleLeaveCommand($message, $channel, $sender, $sendto, $args) {
		if (!$this->raffles["running"]) {
			$msg = "There is no active raffle.";
			$sendto->reply($msg);
			return;
		}

		if (!isset( $this->raffles["rafflees"][$sender])) {
			$msg = "You are not currently signed up for the raffle.";
			$sendto->reply($msg);
			return;
		}

		unset($this->raffles["rafflees"][$sender]);
		$msg = "$sender has left the raffle.";
		$this->raffles["sendto"]->reply($msg);
	}
	
	/**
	 * @Event("2sec")
	 * @Description("Checks to see if raffle is over")
	 */
	public function checkRaffleEvent($eventObj) {
		if (!$this->raffles["running"]) {
			// no raffle running, do nothing
		} else if (time() < $this->raffles["nextmsgtime"]) {
			// not time to display another reminder yet
		} else if ($this->raffles["time"] == $this->raffles["nextmsgtime"]) {
			// if there is no time left or we even skipped over the time, end raffle
			$this->endraffle();
		} else {
			$this->show_raffle_reminder();
		}
	}
	
	function endraffle() {
		// just to make sure there is a raffle to end
		if (!$this->raffles["running"]) {
			return;
		}
		// indicate that the raffle is over
		$this->raffles["running"] = false;

		$item = $this->raffles["item"];
		$count = $this->raffles["count"];
		$rafflees = array_keys($this->raffles["rafflees"]);
		$rafflees_num = count($rafflees);

		if (0 == $rafflees_num) {
			$msg = "No one joined the raffle, $item is free for all.";
			$this->raffles["lastresult"] = $msg;

			$this->raffles["sendto"]->reply($msg);
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
			$this->raffles["rafflees"][$random_name] ++;
		}

		// sort the list depending on roll results
		arsort($this->raffles["rafflees"]);

		$blob = '';
		if (1 == $count) {
			$blob .= "Rolled $rollcount times for $item.\n \n Winner:";
		} else {
			$blob .= "Rolled $rollcount times for $item (count: $count).\n \n Winners:";
		}

		$i = 0;
		forEach ($this->raffles["rafflees"] as $char => $rolls) {
			$i++;
			$blob .= "\n$i. $char got $rolls rolls.";
			if ($i == $count) {
				$blob .= "\n-------------------------\n Unlucky:";
			}
		}
		$results = $this->text->make_blob("Detailed results", $blob);

		if (1 == $count) {
			$msg = "The raffle for $item is over. Winner: ";
		} else {
			$msg = "The raffle for $item (count: $count) is over. Winners: ";
		}

		$i = 0;
		forEach ($this->raffles["rafflees"] as $char => $rolls) {
			$i++;
			$msg .= "{$char}!";
			if ($i != $count) {
				$msg .= ", ";
			} else {
				break;
			}
		}
		$msg .= " Congratulations. $results";
		$this->raffles["lastresult"] = $msg;
		$this->raffles["sendto"]->reply($msg);
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
		// there is a raffle running
		$time_string = $this->util->unixtime_to_readable($this->raffles["time"] - $this->raffles["nextmsgtime"]);
		$item = $this->raffles["item"];
		$count = $this->raffles["count"];

		// generate an info window
		$blob = "<header2>Current Members:<end>";
		forEach (array_keys($this->raffles["rafflees"]) as $tempName) {
			$blob .= "\n$tempName";
		}
		if (count($this->raffles["rafflees"]) == 0) {
			$blob .= "No entrants yet.";
		}
		
		$joinLink = $this->text->make_chatcmd("here", "/tell <myname> raffle join");
		$leaveLink = $this->text->make_chatcmd("here", "/tell <myname> raffle leave");

		$blob .= "\n\nClick $joinLink to join the raffle!";
		$blob .= "\nClick $leaveLink if you wish to leave the raffle.";
		$blob .= "\n\n Time left: $time_string.";

		$link = $this->text->make_blob("Raffle Info", $blob);
		if (1 < $count) {
			$msg = "Reminder: Raffle for $item (count: $count) has $time_string left. $link";
		} else {
			$msg = "Reminder: Raffle for $item has $time_string left. $link";
		}

		$this->raffles["sendto"]->reply($msg);
		$this->raffles["nextmsgtime"] = $this->get_next_time($this->raffles["time"]);
	}
}

?>
