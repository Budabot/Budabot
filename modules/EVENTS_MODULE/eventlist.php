<?
   /*
   ** Author: Legendadv (RK2)
   ** Description: Add/edit/delete in-game events to be stored by the bot
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://budabot.com)
   */

if(preg_match("/^eventlist ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT event_attendees FROM events_<myname>_<dim> WHERE `id` = '$arr[1]'");
	if($db->numrows() != 0) {
	$row = $db->fObject();
	$link = "<header>::::: Player Signed Up :::::<end>\n\n";
	
	$link .= bot::makeLink("Join this event", "/tell <myname> joinEvent $arr[1]", "chatcmd")."\n";
	$link .= bot::makeLink("Leave this event", "/tell <myname> leaveEvent $arr[1]", "chatcmd")."\n\n";
	
	
		$eventlist = explode(",", $row->event_attendees);
		sort($eventlist);
		if($row->event_attendees != "") {
			foreach ($eventlist as $key => $value) {
				$db->query("SELECT * FROM org_members_<myname> WHERE `name` = '$value'");
				if($db->numrows() != 0) {
					$row = $db->fObject();
					$level = $row->level;
					$prof = $row->profession;
					$info = ", level $level $prof";
				}
				
				$db->query("SELECT * FROM alts WHERE `alt` = '$value'");
				if($db->numrows() == 0)
					$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $value'>Alts</a>";
				else {
					$row1 = $db->fObject();
					$alt = "<highlight>::<end> <a href='chatcmd:///tell <myname> alts $value'>Alts of $row1->main</a>";
				}
				
				$link .= trim($value)."$info $alt\n";
			}
		}
		else
			$link .= "Eventlist is empty\n";
		$msg = bot::makeLink("Eventlist", $link);
	}
	else
		$msg = "That event doesn't exist";
}
if($msg) {
	bot::send($msg, $sendto);
}
?>