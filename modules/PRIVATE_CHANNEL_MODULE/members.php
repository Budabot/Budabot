<?php

if (preg_match("/^members$/i", $message)) {
	$db->query("SELECT * FROM members_<myname> m LEFT JOIN players p ON m.charid = p.charid ORDER BY `name`");
	$autoguests = $db->numrows();
	if ($autoguests != 0) {
	  	$list .= "<header>::::: Members :::::<end>\n\n";
		$data = $db-fObject('all');
	  	forEach ($data as $row) {
	  	  	if (Buddylist::is_online($row->name)) {
				$status = "<green>Online";
				if ($chatBot->get_in_chatlist($row->charid) !== null) {
			    	$status .= " and in channel";
				}
			} else {
				$status = "<red>Offline";
			}

	  		$list .= "<tab>- $row->name ($status<end>)\n";
	  	}
	  	
	    $msg = Text::make_link("$autoguests member(s)", $list);
		$chatBot->send($msg, $sendto);
	} else {
       	$chatBot->send("There are no members of this bot.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
