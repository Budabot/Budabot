<?php
/**
 * Authors: 
 *  - Lucier (RK1),
 *  - Tyrence (RK2)
 *
 * @Instance
 *
 * Commands this controller contains:
 *	@DefineCommand(
 *		command     = 'vote',
 *		accessLevel = 'all', 
 *		description = 'View/participate in votes and polls', 
 *		help        = 'vote.txt'
 *	)
 */
class VoteController {

	/**
	 * Name of the module.
	 * Set automatically by module loader.
	 */
	public $moduleName;

	/** @Inject */
	public $text;
	
	/** @Inject */
	public $db;
	
	/** @Inject */
	public $chatBot;
	
	/** @Inject */
	public $util;
	
	/** @Inject */
	public $settingManager;
	
	/** @Inject */
	public $accessManager;
	
	private $votes = array();
	private $delimiter = "|";
	private $table = "vote_<myname>";
	
	/**
	 * This handler is called on bot startup.
	 * @Setup
	 */
	public function setup() {
		$this->db->loadSQLFile($this->moduleName, 'vote');
		
		$this->settingManager->add($this->moduleName, "vote_channel_spam", "Showing Vote status messages in", "edit", "options", "2", "Private Channel;Guild;Private Channel and Guild;Neither", "0;1;2;3", "mod", "votesettings.txt");
		$this->settingManager->add($this->moduleName, "vote_add_new_choices", "Users can add in there own choices", "edit", "options", "1", "true;false", "1;0", "mod", "votesettings.txt");
		$this->settingManager->add($this->moduleName, "vote_create_min", "Minimum org level needed to create votes.", "edit", "options", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "votesettings.txt");
		$this->settingManager->add($this->moduleName, "vote_use_min", "Minimum org level needed to vote.", "edit", "options", "-1", "None;0;1;2;3;4;5;6", "-1;0;1;2;3;4;5;6", "mod", "votesettings.txt");
		
		$data = $this->db->query("SELECT * FROM vote_<myname> WHERE `status` < ? AND `duration` IS NOT NULL", 8);
		forEach ($data as $row) {
			$this->votes[$row->question] = $row;
		}
	}
	
	/**
	 * This event handler checks for votes ending.
	 *
	 * @Event("2sec")
	 * @Description("Checks votes and periodically updates chat with time left")
	 */
	public function checkVote($eventObj) {
		if (count($this->votes) == 0) {
			return;
		}

		forEach ($this->votes as $key => $row) {
			$author = $row->author;
			$question = $row->question;
			$started = $row->started;
			$duration = $row->duration;
			$answer = $row->answer;
			$status = $row->status;
			// status = 0, just started, 1 = > 60 minutes left, 2 = 60 minutes left, 3 = 15 minutes left, 4 = 60 seconds, 9 = vote over

			$timeleft = $started + $duration;
			$timeleft -= time();

			if ($timeleft <= 0) {
				$title = "Finished Vote: $question";
				$this->db->exec("UPDATE $this->table SET `status` = '9' WHERE `duration` = ? AND `question` = ?", $duration, $question);
				unset($this->votes[$key]);
			} else if ($status == 0) {
				$title = "Vote: $question";

				if ($timeleft > 3600) {
					$mstatus = 1;
				} else if ($timeleft > 900) {
					$mstatus = 2;
				} else if ($timeleft > 60) {
					$mstatus = 3;
				} else {
					$mstatus = 4;
				}
				$this->votes[$key]->status = $mstatus;

			} else if ($timeleft <= 60 && $timeleft > 0 && $status != 4) {
				$title = "60 seconds left: $question";
				$this->votes[$key]->status = 4;
			} else if ($timeleft <= 900 && $timeleft > 60 && $status != 3) {
				$title = "15 minutes left: $question";
				$this->votes[$key]->status = 3;
			} else if ($timeleft <= 3600 && $timeleft > 900 && $status != 2) {
				$title = "60 minutes left: $question";
				$this->votes[$key]->status = 2;
			} else {
				$title = "";
			}

			if ($title != "") { // Send current results to guest + org chat.

				$data = $this->db->query("SELECT * FROM $this->table WHERE `question` = ?", $question);

				$results = array();
				forEach ($data as $row2) {
					if ($row2->duration) {
						$question = $row2->question;
						$author = $row2->author;
						$started = $row2->started;
						$duration = $row2->duration;
						$status = $row2->status;
						$timeleft = $started + $duration - time();
					}
					$answer = $row2->answer;

					if (strpos($answer, $this->delimiter) === false) { // A Vote: $answer = "yes";
						$results[$answer]++;
						$totalresults++;
					} else {				     // Main topic: $answer = "yes|no";
						$ans = explode($this->delimiter, $answer);
						forEach ($ans as $value) {
							if (!isset($results[$value])) {
								$results[$value] = 0;
							}
						}
					}
				}

				$msg = "$author's Vote: <highlight>".$question."<end>\n";
				if ($timeleft > 0) {
					$msg .= $this->util->unixtime_to_readable($timeleft)." until this vote closes!\n\n";
				} else {
					$msg .= "<red>This vote has ended.<end>\n\n";
				}

				forEach ($results as $key2 => $value) {
					$val = number_format(100 * ($value / $totalresults), 0);
					if ($val < 10) {
						$msg .= "<black>__<end>$val% ";
					} else if ($val < 100) {
						$msg .= "<black>_<end>$val% ";
					} else {
						$msg .= "$val% ";
					}

					if ($timeleft > 0) {
						$msg .= $this->text->make_chatcmd($key2, "/tell <myname> vote show ".$question.$this->delimiter.$key2);
						$msg .= " (Votes: $value)\n";
					} else {
						$msg .= "<highlight>$key2<end> (Votes: $value)\n";
					}
				}

				if ($timeleft > 0) {
					$removeLink = $this->text->make_chatcmd("Remove yourself from this vote", "/tell <myname> vote remove $question");
					$msg .= "\n<black>___%<end> $removeLink.\n";
				}
				if ($timeleft > 0 && $this->settingManager->get("vote_add_new_choices") == 1 && $status == 0) {
					$msg .="\nDon't like these choices?  Add your own:\n<tab>/tell <myname> vote $question{$this->delimiter}<highlight>your choice<end>\n";
				}

				$msg .="\nIf you started this vote, you can:\n";
				$msg .= "<tab>" . $this->text->make_chatcmd("Kill", "/tell <myname> vote kill $question") . " the vote completely.\n";
				if ($timeleft > 0) {
					$msg .= "<tab>" . $this->text->make_chatcmd("End", "/tell <myname> vote end $question") . " the vote early.";
				}

				$msg = $this->text->make_blob($title, $msg);

				if ($this->settingManager->get("vote_channel_spam") == 0 || $this->settingManager->get("vote_channel_spam") == 2) {
					$this->chatBot->sendGuild($msg, true);
				}
				if ($this->settingManager->get("vote_channel_spam") == 1 || $this->settingManager->get("vote_channel_spam") == 2) {
					$this->chatBot->sendPrivate($msg, true);
				}
			}
		}
	}

	/**
	 * This command handler shows votes.
	 *
	 * @HandlesCommand("vote")
	 * @Matches("/^vote$/i")
	 */
	public function voteCommand($message, $channel, $sender, $sendto, $args) {
		$data = $this->db->query("SELECT * FROM $this->table WHERE `duration` IS NOT NULL ORDER BY `started`");
		if (count($data) > 0) {
			forEach ($data as $row) {
				$question = $row->question;
				$started = $row->started;
				$duration = $row->duration;
				$line = "<tab>" . $this->text->make_chatcmd($question, "/tell <myname> vote show $question");

				$timeleft = $started + $duration - time();
				if ($timeleft>0) {
					$running .= $line . "\n(" . $this->util->unixtime_to_readable($timeleft) . " left)\n";
				} else {
					$over .= $line . "\n";
				}
			}
			if ($running) {
				$blob .= " <green>Running:<end>\n" . $running;
			}
			if ($running && $over) {
				$blob .= "\n";
			}
			if ($over) {
				$blob .= " <red>Finshed:<end>\n" . $over;
			}

			$msg = $this->text->make_blob("Vote Listing", $blob);
		} else {
			$msg = "There are currently no votes to view.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * This command handler deletes votes.
	 *
	 * @HandlesCommand("vote")
	 * @Matches("/^vote kill (.+)$/i")
	 */
	public function voteKillCommand($message, $channel, $sender, $sendto, $args) {
		$question = $args[1];
		if ($this->accessManager->checkAccess($sender, "moderator")) {
			$row = $this->db->queryRow("SELECT * FROM $this->table WHERE `question` = ?", $question);
		} else {
			$row = $this->db->queryRow("SELECT * FROM $this->table WHERE `question` = ? AND `author` = ? AND `duration` IS NOT NULL", $question, $sender);
		}

		if ($row !== null) {
			$this->db->exec("DELETE FROM $this->table WHERE `question` = ?", $row->question);
			unset($this->votes[$row->question]);
			$msg = "'$row->question' has been removed.";
		} else {
			$msg = "Either this vote does not exist, or you did not create it.";
		}
		$sendto->reply($msg);
	}
	
	/**
	 * This command handler removes someones vote from a running vote.
	 *
	 * @HandlesCommand("vote")
	 * @Matches("/^vote remove (.+)$/i")
	 */
	public function voteRemoveCommand($message, $channel, $sender, $sendto, $args) {
		$question = $args[1];
		if (!isset($this->votes[$question])) {
			$msg = "Vote <highlight>$question<end> could not be found.";
		} else {
			$data = $this->db->query("SELECT * FROM $this->table WHERE `question` = ? AND `author` = ? AND `duration` IS NULL", $question, $sender);
			if (count($data) > 0) {
				// this needs to be fixed, should not remove the entire vote
				$this->db->exec("DELETE FROM $this->table WHERE `question` = ? AND `author` = ? AND `duration` IS NULL", $question, $sender);
				$msg = "Your vote has been removed.";
			} else {
				$msg = "You have not voted on this topic.";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * This command handler ends a running vote.
	 *
	 * @HandlesCommand("vote")
	 * @Matches("/^vote end (.*)$/i")
	 */
	public function voteEndCommand($message, $channel, $sender, $sendto, $args) {
		$question = $args[1];
		$row = $this->db->queryRow("SELECT * FROM $this->table WHERE `question` = ? AND `author` = ? AND `duration` IS NOT NULL", $question, $sender);

		if ($row === null) {
			$msg = "Either this vote does not exist, or you did not create it.";
		} else {
			$started = $row->started;
			$duration = $row->duration;
			$timeleft = $started + $duration - time();

			if ($timeleft > 60) {
				$duration = (time() - $started) + 61;
				$this->db->exec("UPDATE $this->table SET `duration` = ? WHERE `question` = ?", $duration, $question);
				$this->votes[$question]->duration = $duration;
				$msg = "Vote duration reduced to 60 seconds.";
			} else if ($timeleft <= 0) {
				$msg = "This vote has already finished.";
			} else {
				$msg = "There is only <highlight>$timeleft<end> seconds left.";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("vote")
	 * @Matches("/^vote show (.*)$/i")
	 */
	public function voteShowCommand($message, $channel, $sender, $sendto, $args) {
		$question = $args[1];
	
		$data = $this->db->query("SELECT * FROM $this->table WHERE `question` = ?", $question);
		if (count($data) == 0) {
			$msg = "Could not find any votes with this topic.";
		} else {
			$results = array();
			forEach ($data as $row) {
				if ($row->duration) {
					$question = $row->question;
					$author = $row->author;
					$started = $row->started;
					$duration = $row->duration;
					$status = $row->status;
					$timeleft = $started + $duration - time();
				}
				if ($sender == $author) {
					$didvote = 1;
				}
				$answer = $row->answer;

				if (strpos($answer, $this->delimiter) === false) { // A Vote: $answer = "yes";
					$results[$answer]++;
					$totalresults++;
				} else {
					// Main topic: $answer = "yes;no";
					$ans = explode($this->delimiter, $answer);
					forEach ($ans as $value) {
						if (!isset($results[$value])) {
							$results[$value] = 0;
						}
					}
				}
			}

			$blob = "$author's Vote: <highlight>".$question."<end>\n";
			if ($timeleft > 0) {
				$blob .= $this->util->unixtime_to_readable($timeleft)." till this vote closes!\n\n";
			} else {
				$blob .= "<red>This vote has ended " . $this->util->unixtime_to_readable(time() - ($started + $duration), 1) . " ago.<end>\n\n";
			}

			forEach ($results as $key => $value) {
				$val = number_format(100 * ($value / $totalresults), 0);
				if ($val < 10) {
					$blob .= "<black>__<end>$val% ";
				} else if ($val < 100) {
					$blob .= "<black>_<end>$val% ";
				} else {
					$blob .= "$val% ";
				}

				if ($timeleft > 0) {
					$blob .= $this->text->make_chatcmd($key, "/tell <myname> vote choose $question{$this->delimiter}$key") . "(Votes: $value)\n";
				} else {
					$blob .= "<highlight>$key<end> (Votes: $value)\n";
				}
			}

			//if ($didvote && $timeleft > 0) {
			if ($timeleft > 0) { // Want this option avaiable for everyone if its run from org/priv chat.
				$blob .= "\n<black>___%<end> ";
				$blob .= $this->text->make_chatcmd('Remove yourself from this vote', "/tell <myname> vote remove $question") . "\n";
			}

			if ($timeleft > 0 && $this->settingManager->get("vote_add_new_choices") == 1 && $status == 0) {
				$blob .="\nDon't like these choices?  Add your own:\n<tab>/tell <myname> vote $question{$this->delimiter}<highlight>your choice<end>\n";
			}

			$blob .="\nIf you started this vote, you can:\n";
			$blob .="<tab>" . $this->text->make_chatcmd('Kill the vote completely', "/tell <myname> vote kill $question") . "\n";
			if ($timeleft > 0) {
				$blob .="<tab>" . $this->text->make_chatcmd('End the vote early', "/tell <myname> vote end $question");
			}

			$row = $this->db->queryRow("SELECT * FROM $this->table WHERE `author` = ? AND `question` = ? AND `duration` IS NULL", $sender, $question);
			if ($row->answer && $timeleft > 0) {
				$privmsg = "On this vote, you already selected: <highlight>(".$row->answer.")<end>.";
			} else if ($timeleft > 0) {
				$privmsg = "You haven't voted on this one yet.";
			}

			$msg = $this->text->make_blob("Vote: $question", $blob);
			if ($privmsg) {
				$sendto->reply($privmsg);
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("vote")
	 * @Matches("/^vote choose (.+)$/i")
	 */
	public function voteChooseCommand($message, $channel, $sender, $sendto, $args) {
		list($question, $choice) = explode($this->delimiter, $args[1], 2);
		
		$requirement = $this->settingManager->get("vote_use_min");
		if ($requirement >= 0) {
			if (!$this->chatBot->guildmembers[$sender]) {
				$sendto->reply("Only org members can vote.");
				return;
			} else if ($requirement < $this->chatBot->guildmembers[$sender]) {
				$rankdiff = $this->chatBot->guildmembers[$sender] - $requirement;
				$sendto->reply("You need $rankdiff promotion(s) in order to vote.");
				return;
			}
		}

		$row = $this->db->queryRow("SELECT * FROM $this->table WHERE `question` = ? AND `duration` IS NOT NULL", $question);
		$question = $row->question;
		$author = $row->author;
		$started = $row->started;
		$duration = $row->duration;
		$status = $row->status;
		$answer = $row->answer;
		$timeleft = $started + $duration - time();

		if (!$duration) {
			$msg = "Couldn't find any votes with this topic.";
		} else if ($timeleft <= 0) {
			$msg = "No longer accepting votes for this topic.";
		} else if (($this->settingManager->get("vote_add_new_choices") == 0 || ($this->settingManager->get("vote_add_new_choices") == 1 && $status == 1)) &&
				strpos($this->delimiter.$answer.$this->delimiter, $this->delimiter.$choice.$this->delimiter) === false) {

			$msg = "Cannot accept this choice.  Please choose one from the menu.";
		} else {
			$data = $this->db->query("SELECT * FROM $this->table WHERE `question` = ? AND `duration` IS NULL AND `author` = ?", $question, $sender);
			if (count($data) > 0) {
				$this->db->exec("UPDATE $this->table SET `answer` = ? WHERE `author` = ? AND `duration` IS NULL AND `question` = ?", $choice, $sender, $question);
				$msg = "You have altered your choice to <highlight>$choice<end> for: $question.";
			} else {
				$this->db->exec("INSERT INTO $this->table (`author`, `answer`, `question`) VALUES (?, ?, ?)", $sender, $choice, $question);
				$msg = "You have selected choice <highlight>$choice<end> for: $question.";
			}
		}
		$sendto->reply($msg);
	}
	
	/**
	 * @HandlesCommand("vote")
	 * @Matches("/^vote add (.+)$/i")
	 */
	public function voteAddCommand($message, $channel, $sender, $sendto, $args) {
		list($settime, $question, $answers) = explode($this->delimiter, $args[1], 3);

		// !vote 16m|Does this module work?|yes|no

		$requirement = $this->settingManager->get("vote_create_min");
		if ($requirement >= 0) {
			if (!$this->chatBot->guildmembers[$sender]) {
				$sendto->reply("Only org members can start a new vote.");
				return;
			} else if ($requirement < $this->chatBot->guildmembers[$sender]) {
				$rankdiff = $this->chatBot->guildmembers[$sender]-$requirement;
				$sendto->reply("You need $rankdiff promotion(s) in order to start a new vote.");
				return;
			}
		}

		$newtime = $this->util->parseTime($settime);

		if ($newtime == 0) {
			$msg = "Invalid time entered. Time format should be: 1d2h3m4s";
		} else {
			$answer = explode($this->delimiter, $answers);
			if (count($answer) < 2) {
				$msg = "You must have at least two options for this vote.";
			} else if (!$question) {
				$msg = "You must specify a question to vote on.";
			} else {
				if (substr($question, 0, 1) == "@") {
					$question = substr($question, 1);
					$status = 1;
				} else {
					$status = 0;
				}
				$data = $this->db->query("SELECT * FROM $this->table WHERE `question` = ?", $question);
				if (count($data) == 0) {
					$this->db->exec("INSERT INTO $this->table (`question`, `author`, `started`, `duration`, `answer`, `status`) VALUES (?, ?, ?, ?, ?, ?)", $question, $sender, time(), $newtime, $answers, $status);
					$obj = new stdClass;
					$obj->question = $question;
					$obj->author = $sender;
					$obj->started = time();
					$obj->duration = $newtime;
					$obj->answer = $answers;
					$obj->status = $status;
					$this->votes[$question] = $obj;
					$msg = "Vote has been added.";
				} else {
					$msg = "There is already a vote with this topic.";
				}
			}
		}

		$sendto->reply($msg);
	}
}
