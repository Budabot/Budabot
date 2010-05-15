<?php
   /*
   ** Author: Legendadv (RK2)
   ** IRC RELAY MODULE
   **
   ** Developed for: Budabot(http://aodevs.com/index.php/topic,512.0.html)
   **
   */
   
global $socket;
if($this->settings['irc_status'] = "1") {
	$whois = new whois($sender);
	if($whois->org == "")
		$whois->org = "Not in a guild";
	$msg = "$sender ({$whois->level}/{$whois->ai_level}, {$whois->prof}, {$whois->org})";
	
	if($type == "joinPriv") {
		$msg .= " has joined {$this->vars["name"]}.";
	}
	else {
		$msg .= " has logged on.";
	}

	// Alternative Characters Part
	$main = false;
	// Check if $sender is hisself the main
	$db->query("SELECT * FROM alts WHERE `main` = '$sender'");
	if($db->numrows() == 0){
		// Check if $sender is an alt
		$db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
		if($db->numrows() != 0) {
			$row = $db->fObject();
			$main = $row->main;
		}
	} else
		$main = $sender;

	if($main != $sender && $main != false) {
		$msg .= " Main: $main";
	} elseif($main != false) {
		$msg .= " Alt of $main";
	}


	if(($row->logon_msg != '') && ($row->logon_msg != '0')) {
		$msg .= " - " . $row->logon_msg;
	}
	
	if($type == "joinPriv") {
		fputs($socket, "PRIVMSG ".$this->settings['irc_channel']." :$msg\n");
		if($this->settings['irc_debug_messages'] == 1) {
			echo("[".date('H:i')."] [Out. IRC Msg.] $sender has joined the private chat\n");
		}
	}
	elseif($type == "logOn" && isset($this->guildmembers[$sender])) {
		fputs($socket, "PRIVMSG ".$this->settings['irc_channel']." :$msg\n");
		if($this->settings['irc_debug_messages'] == 1) {
			echo("[".date('H:i')."] [Out. IRC Msg.] $sender has logged on\n");
		}
	}
}

?>