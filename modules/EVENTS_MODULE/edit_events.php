<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Add/edit/delete in-game events to be stored by the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com)
   */

if (preg_match("/^events add (.+)$/i", $message, $arr)) {
	$db->exec("INSERT INTO events (`time_submitted`, `submitter_name`, `event_name`) VALUES (?, ?, ?)", time(), $sender, $arr[1]);
	$event_id = $db->lastInsertId();
	$msg = "Event: '$arr[1]' was added [Event ID $event_id].";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events rem ([0-9]+)$/i", $message, $arr)) {
	$db->exec("DELETE FROM events WHERE `id` = ?", $arr[1]);
	$msg = "Event Deleted.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events setdesc ([0-9]+) (.+)$/i", $message, $arr)) {
	$db->exec("UPDATE events SET `event_desc` = ? WHERE `id` = ?", $arr[2], $arr[1]);
	$msg = "Description Updated.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^events setdate ([0-9]+) ([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01]) ([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$/i", $message, $arr)) {
	// yyyy-dd-mm hh:mm:ss GMT
	$eventDate = mktime($arr[5], $arr[6], 0, $arr[3], $arr[4], $arr[2]);
	$db->exec("UPDATE events SET `event_date` = ? WHERE `id` = ?", $eventDate, $arr[1]);
	$msg = "Date/Time Updated.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>