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

// I hate seeing a function in a module/plugin. 
// But this is just temporary until 0.7.0.
if (!function_exists(timeLeft)) {
	function timeLeft($origtime, $showbiggest=4) {
		// deal with negative values?
		if ($origtime < 0) {$origtime = 0;}
		//week = day * 7, month = day*365/12, year = day * 365
		$set = array( array("label" => "year", 'length' => 31536000), array("label" => "month", 'length' => 2628000), 
		array("label" => "week", 'length' => 604800), array("label" => "day", 'length' => 86400), 
		array("label" => "hour", 'length' => 3600), array("label" => "minute", 'length' => 60), 
		array("label" => "second", 'length' => 0));
			
		$thisset=0;	
		while($thisset<=6){
			if ($thisset < 6) {$val = floor($origtime/$set[$thisset]['length']);}
			elseif ($thisset == 6) {$val = $origtime;}
				
			if ($val && $showbiggest > 0) {
				$retval .= "$val ".$set[$thisset]['label'];
				$retval .= ($val > 1) ? 's, ' : ', ';
				$showbiggest--;
				$origtime -= $val*$set[$thisset]['length'];
			}
			$thisset++;
		}

		if ($retval) {$retval = substr($retval,0,strlen($retval)-2);}
		return $retval;
	}
}



// Listing of all votes
if (preg_match("/^vote$/i", $message)) {
	
	$db->query("SELECT * FROM $table WHERE `duration` IS NOT NULL ORDER BY `started`");
	
	if ($db->numrows() > 0) {
		while ($row = $db->fObject()) {
			$question = $row->question; $started = $row->started; $duration = $row->duration;
			$line = "<tab>" . Text::make_link($question, "/tell <myname> vote $question", 'chatcmd');
			
			$timeleft = $started+$duration-time();
			if ($timeleft>0) {$running .= $line."\n(".timeLeft($timeleft)." left)\n";}
			else {$over .= $line."\n";}
		}
		if ($running) {$msg .= " <green>Running:<end>\n".$running;}
		if ($running && $over) $msg .= "\n";
		if ($over) {$msg .= " <red>Finshed:<end>\n".$over;}

		$msg = Text::make_link("Vote Listing", $msg);
	} else {
		$msg = "There are currently no votes to view.";
	}



} else if (preg_match("/^vote (.+)$/i", $message, $arr)) {
	$sect = explode($delimiter, $arr[1],3);
	
	//////////////////////////////////////
	if (count($sect) == 1) { // Show vote
		
		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[0])."'");
		
		if ($db->numrows() <= 0) { $msg = "Couldn't find any votes with this topic.";} 
		
		else {
			$results = array();
			while ($row = $db->fObject()) {
				if ($row->duration) {
					$question = $row->question; $author = $row->author; $started = $row->started;
					$duration = $row->duration; $status = $row->status;
					$timeleft = $started+$duration-time();
					
				}
				if ($sender == $author) {$didvote = 1;}
				$answer = $row->answer;

				if (strpos($answer, $delimiter) === false) { // A Vote: $answer = "yes";
					$results[$answer]++;
					$totalresults++;
				} else {				     // Main topic: $answer = "yes;no";
					
					$ans = explode($delimiter, $answer);
					foreach ($ans as $value) {
						if (!isset($results[$value])) {$results[$value] = 0;}
					}
				}
			}
			
			$msg = "$author's Vote: <highlight>".$question."<end>\n";
			if ($timeleft > 0) {
				$msg .= timeLeft($timeleft)." till this vote closes!\n\n";
			} else {
				$msg .= "<red>This vote has ended ".timeLeft(time()-($started+$duration),1)." ago.<end>\n\n";
			}
			
			foreach ($results as $key => $value) {

				$val = number_format(100*($value/$totalresults),0);
				if ($val < 10) {$msg .= "<black>__<end>$val% ";}
				else if ($val < 100) {$msg .= "<black>_<end>$val% ";}
				else {$msg .= "$val% ";}
				
				if ($timeleft > 0) {
					$msg .= Text::make_link($key, "/tell <myname> vote $question$delimiter$key", 'chatcmd') . "(Votes: $value)\n";
				} else {
					$msg .= "<highlight>$key<end> (Votes: $value)\n";
				}
			}
			
			//if ($didvote && $timeleft > 0) {
			if ($timeleft > 0) { // Want this option avaiable for everyone if its run from org/priv chat.
				$msg .= "\n<black>___%<end> ";
				$msg .= Text::make_link('Remove yourself from this vote', "/tell <myname> vote remove$delimiter$question", 'chatcmd') . "\n";
			}
			
			if ($timeleft > 0 && $chatBot->settings["vote_add_new_choices"] == 1 && $status == 0) {
				$msg .="\n<highlight>Don't like these choices?  Add your own:<end>\n<tab>/tell <myname> <symbol>vote $question$delimiter"."<highlight>your choice<end>\n"; 
			}
			
			$msg .="\n<highlight>If you started this vote, you can:<end>\n";
			$msg .="<tab>" . Text::make_link('Kill the vote completely', "/tell <myname> vote kill$delimiter$question", 'chatcmd') . "\n";
			if ($timeleft > 0) {
				$msg .="<tab>" . Text::make_link('End the vote early', "/tell <myname> vote end$delimiter$question" , 'chatcmd');
			}
			
			$db->query("SELECT * FROM $table WHERE `author` = '$sender' AND `question` = '$question' AND `duration` IS NULL");
			$row = $db->fObject();
			if ($row->answer && $timeleft > 0) {$privmsg = "On this vote, you already selected: <highlight>(".$row->answer.")<end>.";}
			elseif ($timeleft > 0){$privmsg = "You haven't voted on this one yet.";}
			
			$msg = Text::make_link("Vote: $question",$msg);
			if ($privmsg) {$chatBot->send($privmsg, $sender);}			
		}
		
		
	////////////////////////////////////////////////////////////////////////////////////
	} elseif (count($sect) == 2 && strtolower($sect[0]) == "remove") {   // Remove vote
		
		if (!isset($chatBot->data["Vote"][$sect[1]])) {
			$msg = "There is no such topic available.";
		} else {
			$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."' AND `author` = '$sender' AND `duration` IS NULL");
			if ($db->numrows() > 0) {
				$db->exec("DELETE FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."' AND `author` = '$sender' AND `duration` IS NULL");
				$msg = "Your vote has been removed.";
			} else {
				$msg = "I don't see your vote to delete.";
			}
		}
	//////////////////////////////////////////////////////////////////////////////////
	} elseif (count($sect) == 2 && strtolower($sect[0]) == "kill") {     // Kill vote
		
		if ($chatBot->admins[$sender]["level"] >= 4) {
			$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."'");
		} else {
			$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."' AND `author` = '$sender' AND `duration` IS NOT NULL");
		}
		
		if ($db->numrows() > 0) {
			$db->exec("DELETE FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."'");
			unset($chatBot->data["Vote"][$sect[1]]);
			$msg = "'$sect[1]' has been removed.";
		} else {
			$msg = "Either this vote doesn't exist, or you didn't create it.";
		}
		
	/////////////////////////////////////////////////////////////////////////////////
	} elseif (count($sect) == 2 && strtolower($sect[0]) == "end") {      // End vote

		$db->query("SELECT * FROM $table WHERE `question` = '".str_replace("'", "''", $sect[1])."' AND `author` = '$sender' AND `duration` IS NOT NULL");
		
		if ($db->numrows() == 0) {
			$msg = "Either this vote doesn't exist, or you didn't create it.";
		} else {
			$row = $db->fObject();
			$question = $row->question; $author = $row->author; $started = $row->started;
			$duration = $row->duration; $status = $row->status;
			$timeleft = $started+$duration-time();		
		
			if ($timeleft > 60) {
				$duration = (time()-$started)+61;
				$db->exec("UPDATE $table SET `duration` = '$duration' WHERE `author` = '$sender' AND `duration` IS NOT NULL AND `question` = '".str_replace("'", "''", $sect[1])."'");
				$chatBot->data["Vote"][$sect[1]]["duration"] = $duration;
			} else {
				$msg = "There is only $timeleft seconds left.";
			}
		}
	////////////////////////////////////////////////////////////////////////////////////
	} elseif (count($sect) == 2) {		  			     // Adding vote

		$requirement = $chatBot->settings["vote_use_min"];
		if ($requirement >= 0) {
			if (!$chatBot->guildmembers[$sender]) {
				$chatBot->send("Only org members can start a new vote.", $sender);
				return;
			}elseif ($requirement < $chatBot->guildmembers[$sender]) {
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
		} else if (($chatBot->settings["vote_add_new_choices"] == 0 || ($chatBot->settings["vote_add_new_choices"] == 1 && $status == 1)) && strpos($delimiter.$answer.$delimiter, $delimiter.$sect[1].$delimiter) === false) {
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
	} elseif (count($sect) > 2) {					     // Creating vote
		// !vote 16m|Does this module work?|yes|no
		
		$settime=trim($sect[0]); $question = trim($sect[1]); $answers = trim($sect[2]);
		
		$requirement = $chatBot->settings["vote_create_min"];
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


		while ($settime) { // checking how long the vote will last.
			if (!ctype_digit(substr($settime,$pos,1))) {
				$val = substr($settime, 0,$pos);
				if     (strtolower(substr($settime,$pos,1)) == "s") {$newtime += $val;}
				elseif (strtolower(substr($settime,$pos,1)) == "m") {$newtime += $val*60;}
				elseif (strtolower(substr($settime,$pos,1)) == "h") {$newtime += $val*60*60;}
				elseif (strtolower(substr($settime,$pos,1)) == "d") {$newtime += $val*60*60*24;}
				else { $newtime = -1; break; } // caught an invalid character.
				$settime = substr($settime,$pos+1);
				$pos=-1;
			}
			$pos++;
		}
		
		if ($newtime == -1) {
			$msg = "Found an invalid character for duration. eg: 4s3m2h1d";
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

				} else {
					$msg = "There's already a vote with this topic.";
				}
			}
		}
	}

}
/////////////////////////////////////////////////
// we have a message after all that? post it
/////////////////////////////////////////////////
if ($msg){	// Send info back
	$chatBot->send($msg, $sendto);
}
?>
