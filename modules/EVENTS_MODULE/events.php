<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Add/edit/delete in-game events to be stored by the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com)
   */

if (preg_match("/^events$/i", $message, $arr)) {
  	$db->query("SELECT * FROM events_<myname>_<dim> ORDER BY `event_date` DESC LIMIT 0,5");
	if ($db->numrows() != 0) {
		$upcoming_title = "<header>::::: Upcoming Events :::::<end>\n\n";
		$past_title = "<header>::::: Past Events :::::<end>\n\n";
		while ($row = $db->fObject()) {
			$row->event_name = stripslashes($row->event_name);
		  	$row->event_desc = stripslashes($row->event_desc);
			if ($row->event_attendees == '') {
				$attendance = 0;
			} else {
				$attendance = count(explode(",", $row->event_attendees));
			}
		  	if (!$updated) {
			  $updated = $row->time_submitted;
			}
			  
			if ($row->event_date > time()) {
				$upcoming = "<highlight>Date Submitted:<end> ".gmdate("dS M, H:i", $row->time_submitted)."\n";
				$upcoming .= "<highlight>Event Date:<end> ".gmdate("F d, Y H:i:s", $row->event_date)." GMT\n";
				$upcoming .= "<highlight>Event Name:<end> $row->event_name     [Event ID $row->id]\n";
				$upcoming .= "<highlight>Author:<end> $row->submitter_name\n";
				$upcoming .= "<highlight>Attendance:<end> ".Text::make_link("$attendance signed up", "/tell <myname> eventlist $row->id", "chatcmd")." [".Text::make_link("Join", "/tell <myname> joinEvent $row->id", "chatcmd")."/".Text::make_link("Leave", "/tell <myname> leaveEvent $row->id", "chatcmd")."]\n";
				$upcoming .= "<highlight>Description:<end> ".stripslashes($row->event_desc)."\n\n";
				$upcoming_events = $upcoming.$upcoming_events;
			} else {
				$past = "<highlight>Date Submitted:<end> ".gmdate("dS M, H:i", $row->time_submitted)."\n";
				$past .= "<highlight>Event Date:<end> ".gmdate("F d, Y H:i:s", $row->event_date)." GMT\n";
				$past .= "<highlight>Event Name:<end> $row->event_name     [Event ID $row->id]\n";
				$past .= "<highlight>Author:<end> $row->submitter_name\n";
				$past .= "<highlight>Attendance:<end> ".Text::make_link("$attendance signed up", "/tell <myname> eventlist $row->id", "chatcmd")."\n";
				$past .= "<highlight>Description:<end> ".stripslashes($row->event_desc)."\n\n";
				$past_events .= $past;
			}
		}
		if (!$upcoming_events) {
			$upcoming_events = "<i>More to come.  Check back soon!</i>\n\n";
		}
		$link = $upcoming_title.$upcoming_events.$past_title.$past_events;
		
		$msg = Text::make_link("Latest Events", $link)." [Last updated at ".gmdate("dS M, H:i", $updated)."]";
	} else {
		$msg = "No events entered yet.";
	}
} else if (preg_match("/^joinevent ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM events_<myname>_<dim> WHERE `id` = '$arr[1]'");
	$row = $db->fObject();
	if (time() < (($row->event_date)+(3600*3))) {
		// cannot join an event after 3 hours past its starttime
		if (strpos($row->event_attendees,$sender) !== false) {
			bot::send("<highlight>$sender<end> is already on the event list.",$sender);
			return;
		} else {
			$row->event_attendees = trim($row->event_attendees);
			if ($row->event_attendees == "") {
				$row->event_attendees = "$sender";
			} else {
				$row->event_attendees .= ",$sender";
			}
			$db->exec("UPDATE events_<myname>_<dim> SET `event_attendees`='".$row->event_attendees."' WHERE `id` = '$arr[1]'");
			$msg = "You have been added to the event.";
		}
	} else {
		$msg = "You cannot join an event once it has already passed!";
	}
} else if (preg_match("/^leaveevent ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM events_<myname>_<dim> WHERE `id` = '$arr[1]'");
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
			$db->exec("UPDATE events_<myname>_<dim> SET `event_attendees`='".$event."' WHERE `id` = '$arr[1]'");
			$msg = "You have been removed from the event.";
		} else {
			bot::send("<highlist>$sender<end> is not on the event list.",$sender);
			return;
		}
	} else {
		$msg = "You cannot leave an event once it has already passed!";
	}
}

if ($msg) {
	bot::send($msg, $sendto);
}
?>