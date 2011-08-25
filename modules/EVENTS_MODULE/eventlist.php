<?php
   /*
   ** Author: Legendadv (RK2)
   ** Description: Add/edit/delete in-game events to be stored by the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com)
   */

if (preg_match("/^eventlist ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT event_attendees FROM events WHERE `id` = '$arr[1]'");
	if ($db->numrows() != 0) {
		$row = $db->fObject();
		$link = "<header>::::: Player Signed Up :::::<end>\n\n";
		
		$link .= Text::make_chatcmd("Join this event", "/tell <myname> joinEvent $arr[1]")."\n";
		$link .= Text::make_chatcmd("Leave this event", "/tell <myname> leaveEvent $arr[1]")."\n\n";

		$eventlist = explode(",", $row->event_attendees);
		sort($eventlist);
		if ($row->event_attendees != "") {
			forEach ($eventlist as $key => $name) {
				$db->query("SELECT * FROM org_members_<myname> o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `o.name` = '$name'");
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
} else {
	$syntax_error = true;
}

?>