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
	$sendto->reply($msg);
} else if (preg_match("/^events join ([0-9]+)$/i", $message, $arr)) {
	$row = $db->queryRow("SELECT * FROM events WHERE `id` = ?", $arr[1]);
	if (time() < (($row->event_date)+(3600*3))) {
		// cannot join an event after 3 hours past its starttime
		if (strpos($row->event_attendees, $sender) !== false) {
			$sendto->reply("You are already on the event list.");
			return;
		} else {
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
	$sendto->reply($msg);
} else if (preg_match("/^events leave ([0-9]+)$/i", $message, $arr)) {
	$row = $db->queryRow("SELECT * FROM events WHERE `id` = ?", $arr[1]);
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
			$db->exec("UPDATE events SET `event_attendees`='".$event."' WHERE `id` = '$arr[1]'");
			$msg = "You have been removed from the event.";
		} else {
			$sendto->reply("You are not on the event list.");
			return;
		}
	} else {
		$msg = "You cannot leave an event once it has already passed!";
	}
	$sendto->reply($msg);
} else if (preg_match("/^events list ([0-9]+)$/i", $message, $arr)) {
	$id = $arr[1];
	$row = $db->queryRow("SELECT event_attendees FROM events WHERE `id` = ?", $id);
	if ($row !== null) {
		$link = Text::make_chatcmd("Join this event", "/tell <myname> event join $id")."\n";
		$link .= Text::make_chatcmd("Leave this event", "/tell <myname> event leave $id")."\n\n";

		if ($row->event_attendees != "") {
			$eventlist = explode(",", $row->event_attendees);
			sort($eventlist);
			forEach ($eventlist as $key => $name) {
				$row = $db->queryRow("SELECT * FROM players WHERE name = ? AND dimension = '<dim>'", $name);
				$info = '';
				if ($row !== null) {
					$info = " <white>Lvl $row->level $row->profession<end>\n";
				}

				$alts = Registry::getInstance('alts');
				$altInfo = $alts->get_alt_info($name);
				$alt = '';
				if (count($altInfo->alts) > 0) {
					if ($altInfo->main == $name) {
						$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts {$name}'>Alts</a>";
					} else {
						$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts {$name}'>Alts of {$altInfo->main}</a>";
					}
				}

				$link .= trim($name)."$info $alt\n";
			}
			$msg = Text::make_blob("Players Attending Event $id", $link);
		} else {
			$msg = "No one has signed up to attend this event!";
		}
	} else {
		$msg = "That event doesn't exist!";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
