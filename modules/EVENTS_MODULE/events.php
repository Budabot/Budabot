<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Add/edit/delete in-game events to be stored by the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com)
   */

if (preg_match("/^events$/i", $message, $arr)) {
	$msg = getEvents();
  	if ($msg == '') {
		$msg = "No events entered yet.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events join ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM events WHERE `id` = '$arr[1]'");
	$row = $db->fObject();
	if (time() < (($row->event_date)+(3600*3))) {
		// cannot join an event after 3 hours past its starttime
		if (strpos($row->event_attendees,$sender) !== false) {
			$chatBot->send("You are already on the event list.",$sender);
			return;
		} else {
			$row->event_attendees = trim($row->event_attendees);
			if ($row->event_attendees == "") {
				$row->event_attendees = "$sender";
			} else {
				$row->event_attendees .= ",$sender";
			}
			$db->exec("UPDATE events SET `event_attendees`='".$row->event_attendees."' WHERE `id` = '$arr[1]'");
			$msg = "You have been added to the event.";
		}
	} else {
		$msg = "You cannot join an event once it has already passed!";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events leave ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM events WHERE `id` = '$arr[1]'");
	$row = $db->fObject();
	if (time() < (($row->event_date)+(3600*3))) { // cannot leave an event after 3 hours past its starttime
		if (strpos($row->event_attendees,$sender) !== false) {
			$event = explode(",", $row->event_attendees);
			forEach ($event as $i => $value) {
				if ($value == $sender) {
					unset($event[$i]);
					$event = array_values($event);
				}
			}
			$event = implode(",", $event);
			$event = substr($event,1);
			$db->exec("UPDATE events SET `event_attendees`='".$event."' WHERE `id` = '$arr[1]'");
			$msg = "You have been removed from the event.";
		} else {
			$chatBot->send("You are not on the event list.",$sender);
			return;
		}
	} else {
		$msg = "You cannot leave an event once it has already passed!";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events list ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	$db->query("SELECT event_attendees FROM events WHERE `id` = '$id'");
	if ($db->numrows() != 0) {
		$row = $db->fObject();
		$link = "<header> :::::: Players Attending Event $id :::::: <end>\n\n";
		
		$link .= Text::make_chatcmd("Join this event", "/tell <myname> event join $id")."\n";
		$link .= Text::make_chatcmd("Leave this event", "/tell <myname> event leave $id")."\n\n";

		$eventlist = explode(",", $row->event_attendees);
		sort($eventlist);
		if ($row->event_attendees != "") {
			forEach ($eventlist as $key => $name) {
				$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE o.name = '$name'");
				if ($db->numrows() != 0) {
					$row = $db->fObject();
					$level = $row->level;
					$prof = $row->profession;
					$info = ", level $level $prof";
				}
				
				$altInfo = Alts::get_alt_info($name);
				if (count($altInfo->alts) > 0) {
					if ($altInfo->main == $name) {
						$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts {$name}'>Alts</a>";
					} else {
						$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts {$name}'>Alts of {$altInfo->main}</a>";
					}
				}
				
				$link .= trim($name)."$info $alt\n";
			}
			$msg = Text::make_blob("Eventlist", $link);
		} else {
			$msg = "Eventlist is empty\n";
		}
	} else {
		$msg = "That event doesn't exist";
	}
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events add (.+)$/i", $message, $arr)) {
	$db->exec("INSERT INTO events (`time_submitted`, `submitter_name`, `event_name`) VALUES (".time().", '".$sender."', '".addslashes($arr[1])."')");
	$event_id = $db->lastInsertId();
	$msg = "Event: '$arr[1]' was submitted [Event ID $event_id].";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events (rem|remove|del|delete) ([0-9]+)$/i", $message, $arr)) {
	$db->exec("DELETE FROM events WHERE `id` = '$arr[2]'");
	$msg = "Event Deleted.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events setdesc ([0-9]+) (.+)$/i", $message, $arr)) {
	$db->exec("UPDATE events SET `event_desc` = '".addslashes($arr[2])."' WHERE `id` = '$arr[1]'");
	$msg = "Description Updated.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events setdate ([0-9]+) ([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/i", $message, $arr)) {
	// yyyy-dd-mm hh:mm:ss GMT
	$eventDate = gmmktime($arr[5], $arr[6], 0, $arr[3], $arr[4], $arr[2]);
	$db->exec("UPDATE events SET `event_date` = '$eventDate' WHERE `id` = '$arr[1]'");
	$msg = "Date/Time Updated.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>