<?php
   /*
   ** Author: Lucier (RK1)
   ** Description: Voting System
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 02.05.2007
   ** Date(last modified): 02.06.2007
   */
   
   
$table = "vote_<myname>";

$delimiter = "|";

// Listing of all votes
if (preg_match("/^vote$/i", $message)) {
	
	$db->query("SELECT * FROM $table WHERE `duration` IS NOT NULL ORDER BY `started`");
	$data = $db->fObject('all');
	if (count($data) > 0) {
		forEach ($data as $row) {
			$question = $row->question; $started = $row->started; $duration = $row->duration;
			$line = "<tab>" . Text::make_chatcmd($question, "/tell <myname> vote $question");
			
			$timeleft = $started+$duration-time();
			if ($timeleft>0) {
				$running .= $line."\n(".Util::unixtime_to_readable($timeleft)." left)\n";
			} else {
				$over .= $line."\n";
			}
		}
		if ($running) {
			$msg .= " <green>Running:<end>\n".$running;
		}
		if ($running && $over) {
			$msg .= "\n";
		}
		if ($over) {
			$msg .= " <red>Finshed:<end>\n".$over;
		}

		$msg = Text::make_blob("Vote Listing", $msg);
	} else {
		$msg = "There are currently no votes to view.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^vote kill (.+)$/i", $message, $arr)) {
	$topic = $arr[1];
	if (AccessLevel::check_access($sender, "moderator")) {
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."'");
	} else {
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."' AND `author` = '$sender' AND `duration` IS NOT NULL");
	}
	
	if ($db->numrows() > 0) {
		$db->exec("DELETE FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."'");
		unset($chatBot->data["Vote"][$topic]);
		$msg = "'$topic' has been removed.";
	} else {
		$msg = "Either this vote doesn't exist, or you didn't create it.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^vote remove (.+)$/i", $message, $arr)) {
	$topic = $arr[1];
	if (!isset($chatBot->data["Vote"][$topic])) {
		$msg = "There is no such topic available.";
	} else {
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."' AND `author` = '$sender' AND `duration` IS NULL");
		if ($db->numrows() > 0) {
			$db->exec("DELETE FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."' AND `author` = '$sender' AND `duration` IS NULL");
			$msg = "Your vote has been removed.";
		} else {
			$msg = "You have not voted on this topic.";
		}
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^vote end (.+)$/i", $message, $arr)) {
	$topic = $arr[1];
	$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $topic)."' AND `author` = '$sender' AND `duration` IS NOT NULL");
	
	if ($db->numrows() == 0) {
		$msg = "Either this vote doesn't exist, or you didn't create it.";
	} else {
		$row = $db->fObject();
		$question = $row->question; $author = $row->author; $started = $row->started;
		$duration = $row->duration; $status = $row->status;
		$timeleft = $started+$duration-time();		
	
		if ($timeleft > 60) {
			$duration = (time()-$started)+61;
			$db->exec("UPDATE $table SET `duration` = '$duration' WHERE `author` = '$sender' AND `duration` IS NOT NULL AND `question` = '".str_replace("'", "''", $topic)."'");
			$chatBot->data["Vote"][$topic]["duration"] = $duration;
			$msg = "Vote duration reduced to 60 seconds.";
		} else if ($timeleft <= 0) {
			$msg = "This vote has already finished.";
		} else {
			$msg = "There is only $timeleft seconds left.";
		}
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^vote (.+)$/i", $message, $arr)) {
	$sect = explode($delimiter, $arr[1],3);
	
	//////////////////////////////////////
	if (count($sect) == 1) { // Show vote
		
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[0])."'");
		$data = $db->fObject('all');
		if (count($data)<= 0) {
			$msg = "Couldn't find any votes with this topic.";
		} else {
			$results = array();
			forEach ($data as $row) {
				if ($row->duration) {
					$question = $row->question; $author = $row->author; $started = $row->started;
					$duration = $row->duration; $status = $row->status;
					$timeleft = $started+$duration-time();
					
				}
				if ($sender == $author) {
					$didvote = 1;
				}
				$answer = $row->answer;

				if (strpos($answer, $delimiter) === false) { // A Vote: $answer = "yes";
					$results[$answer]++;
					$totalresults++;
				} else {				     // Main topic: $answer = "yes;no";
					
					$ans = explode($delimiter, $answer);
					forEach ($ans as $value) {
						if (!isset($results[$value])) {
							$results[$value] = 0;
						}
					}
				}
			}
			
			$msg = "$author's Vote: <highlight>".$question."<end>\n";
			if ($timeleft > 0) {
				$msg .= Util::unixtime_to_readable($timeleft)." till this vote closes!\n\n";
			} else {
				$msg .= "<red>This vote has ended ".Util::unixtime_to_readable(time()-($started+$duration),1)." ago.<end>\n\n";
			}
			
			forEach ($results as $key => $value) {
				$val = number_format(100*($value/$totalresults),0);
				if ($val < 10) {
					$msg .= "<black>__<end>$val% ";
				} else if (
					$val < 100) {$msg .= "<black>_<end>$val% ";
				} else {
					$msg .= "$val% ";
				}
				
				if ($timeleft > 0) {
					$msg .= Text::make_chatcmd($key, "/tell <myname> vote $question$delimiter$key") . "(Votes: $value)\n";
				} else {
					$msg .= "<highlight>$key<end> (Votes: $value)\n";
				}
			}
			
			//if ($didvote && $timeleft > 0) {
			if ($timeleft > 0) { // Want this option avaiable for everyone if its run from org/priv chat.
				$msg .= "\n<black>___%<end> ";
				$msg .= Text::make_chatcmd('Remove yourself from this vote', "/tell <myname> vote remove $question") . "\n";
			}
			
			if ($timeleft > 0 && Setting::get("vote_add_new_choices") == 1 && $status == 0) {
				$msg .="\n<highlight>Don't like these choices?  Add your own:<end>\n<tab>/tell <myname> <symbol>vote $question$delimiter"."<highlight>your choice<end>\n"; 
			}
			
			$msg .="\n<highlight>If you started this vote, you can:<end>\n";
			$msg .="<tab>" . Text::make_chatcmd('Kill the vote completely', "/tell <myname> vote kill $question") . "\n";
			if ($timeleft > 0) {
				$msg .="<tab>" . Text::make_chatcmd('End the vote early', "/tell <myname> vote end $question");
			}
			
			$db->query("SELECT * FROM $table WHERE `author` = '$sender' AND `question` = '$question' AND `duration` IS NULL");
			$row = $db->fObject();
			if ($row->answer && $timeleft > 0) {
				$privmsg = "On this vote, you already selected: <highlight>(".$row->answer.")<end>.";
			} else if ($timeleft > 0) {
				$privmsg = "You haven't voted on this one yet.";
			}
			
			$msg = Text::make_blob("Vote: $question", $msg);
			if ($privmsg) {
				$chatBot->send($privmsg, $sender);
			}
		}
	////////////////////////////////////////////////////////////////////////////////////
	} else if (count($sect) == 2) {		  			     // Adding vote

		$requirement = Setting::get("vote_use_min");
		if ($requirement >= 0) {
			if (!$chatBot->guildmembers[$sender]) {
				$chatBot->send("Only org members can start a new vote.", $sender);
				return;
			} else if ($requirement < $chatBot->guildmembers[$sender]) {
				$rankdiff = $chatBot->guildmembers[$sender]-$requirement;
				$chatBot->send("You need $rankdiff promotion(s) in order to vote.", $sender);
				return;
			}
		}

		
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[0])."' AND `duration` IS NOT NULL");
		$row = $db->fObject();
		$question = $row->question; $author = $row->author; $started = $row->started;
		$duration = $row->duration; $status = $row->status; $answer = $row->answer;
		$timeleft = $started+$duration-time();	
		
		if (!$duration) {
			$msg = "Couldn't find any votes with this topic.";
		} else if ($timeleft <= 0) {
			$msg = "No longer accepting votes for this topic.";
		} else if ((Setting::get("vote_add_new_choices") == 0 || (Setting::get("vote_add_new_choices") == 1 && $status == 1)) && strpos($delimiter.$answer.$delimiter, $delimiter.$sect[1].$delimiter) === false) {
			$msg = "Cannot accept this choice.  Please choose one from the menu.";
		} else {
			$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[0])."' AND `duration` IS NULL AND `author` = '$sender'");
			if ($db->numrows() > 0) {
				$db->exec("UPDATE $table SET `answer` = '".str_replace("'", "''", $sect[1])."' WHERE `author` = '$sender' AND `duration` IS NULL AND `question` = '".str_replace("'", "''", $sect[0])."'");
				$msg = "You have altered your choice to <highlight>$sect[1]<end> for: <highlight>$sect[0]<end>.";
			} else {
				$db->exec("INSERT INTO $table (`author`, `answer`, `question`) VALUES ('$sender', '".str_replace("'", "''", $sect[1])."', '".str_replace("'", "''", $sect[0])."')");
				$msg = "You have selected choice <highlight>$sect[1]<end> for: <highlight>$sect[0]<end>.";
			}
			
		}
		
	//////////////////////////////////////////////////////////////////////////////////////
	} else if (count($sect) > 2) {					     // Creating vote
		// !vote 16m|Does this module work?|yes|no
		
		$settime=trim($sect[0]);
		$question = trim($sect[1]);
		$answers = trim($sect[2]);
		
		$requirement = Setting::get("vote_create_min");
		if ($requirement >= 0) {
			if (!$chatBot->guildmembers[$sender]) {
				$chatBot->send("Only org members can start a new vote.", $sender);
				return;
			} else if ($requirement < $chatBot->guildmembers[$sender]) {
				$rankdiff = $chatBot->guildmembers[$sender]-$requirement;
				$chatBot->send("You need $rankdiff promotion(s) in order to start a new vote.", $sender);
				return;
			}
		}

		$newtime = Util::parseTime($settime);
		
		if ($newtime == 0) {
			$msg = "Found an invalid character for duration or the time you entered was 0 seconds. Time format should be: 1d2h3m4s";
		} else if ($newtime < 30) {
			$msg = "Need to have at least a 30 second span for duration of votes.";
		} else {
			$answer = explode($delimiter,$answers);
			if (count($answer) < 2) {
				$msg = "Need to have at least 2 options for this vote.";
			} else if (!$question) {
				$msg = "What are we voting on?";
			} else {
				if (substr($question,0,1) == "@") {
					$question = substr($question,1);
					$status = 1;
				} else {
					$status = 0;
				}
				$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $question)."'");
				if ($db->numrows() == 0) {

					$db->exec("INSERT INTO $table (`question`, `author`, `started`, `duration`, `answer`, `status`) VALUES ( '".str_replace("'", "''", $question)."', '$sender', '".time()."', '$newtime', '".str_replace("'", "''", $answers)."', '$status')");
					$chatBot->data["Vote"][$question] = array("author" => $sender,  "started" => time(), "duration" => $newtime, "answer" => $answers, "status" => "0", "lockout" => $status);
					$msg = "Vote has been added.";

				} else {
					$msg = "There's already a vote with this topic.";
				}
			}
		}
	}

	/////////////////////////////////////////////////
	// we have a message after all that? post it
	/////////////////////////////////////////////////
	if ($msg) {
		$chatBot->send($msg, $sendto);
	}
}

?>
