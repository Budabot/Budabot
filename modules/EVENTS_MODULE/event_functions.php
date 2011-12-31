<?php

function getEvents() {
	$chatBot = Registry::getInstance('chatBot');
	$db = Registry::getInstance('db');

	$data = $db->query("SELECT * FROM events ORDER BY `event_date` DESC LIMIT 0,5");
	if (count($data) > 0) {
		$upcoming_title = "<header> :::::: Upcoming Events :::::: <end>\n\n";
		$past_title = "<header> :::::: Past Events :::::: <end>\n\n";
		$updated = 0;
		forEach ($data as $row) {
			$row->event_name = stripslashes($row->event_name);
			$row->event_desc = stripslashes($row->event_desc);
			if ($row->event_attendees == '') {
				$attendance = 0;
			} else {
				$attendance = count(explode(",", $row->event_attendees));
			}
			if ($updated < $row->time_submitted) {
				$updated = $row->time_submitted;
			}
			  
			if ($row->event_date > time()) {
				$upcoming = "<highlight>Event Date:<end> ".date("F d, Y H:i:s", $row->event_date)." GMT\n";
				$upcoming .= "<highlight>Event Name:<end> $row->event_name     [Event ID $row->id]\n";
				$upcoming .= "<highlight>Author:<end> $row->submitter_name\n";
				$upcoming .= "<highlight>Attendance:<end> ".Text::make_chatcmd("$attendance signed up", "/tell <myname> events list $row->id")." [".Text::make_chatcmd("Join", "/tell <myname> events join $row->id")."/".Text::make_chatcmd("Leave", "/tell <myname> events leave $row->id")."]\n";
				$upcoming .= "<highlight>Description:<end> ".stripslashes($row->event_desc)."\n";
				$upcoming .= "<highlight>Date Submitted:<end> ".date("F d, Y H:i:s", $row->time_submitted)."\n\n";
				$upcoming_events = $upcoming.$upcoming_events;
			} else {
				$past = "<highlight>Event Date:<end> ".date("F d, Y H:i:s", $row->event_date)." GMT\n";
				$past .= "<highlight>Event Name:<end> $row->event_name     [Event ID $row->id]\n";
				$past .= "<highlight>Author:<end> $row->submitter_name\n";
				$past .= "<highlight>Attendance:<end> ".Text::make_chatcmd("$attendance signed up", "/tell <myname> events list $row->id")."\n";
				$past .= "<highlight>Description:<end> ".stripslashes($row->event_desc)."\n";
				$past .= "<highlight>Date Submitted:<end> ".date("F d, Y H:i:s", $row->time_submitted)."\n\n";
				$past_events .= $past;
			}
		}
		if (!$upcoming_events) {
			$upcoming_events = "<i>More to come.  Check back soon!</i>\n\n";
		}
		if (!$past_events) {
			$link = $upcoming_title.$upcoming_events;
		} else {
			$link = $upcoming_title.$upcoming_events.$past_title.$past_events;
		}
		
		return Text::make_blob("Latest Events", $link)." [Last updated ".date("F d, Y H:i:s", $updated)."]";
	} else {
		return "";
	}
}

?>