<?php

if (preg_match("/^sm$/i", $message)) {
	if (count($this->vars["Guest"]) > 0) {
		$db->query("SELECT * FROM priv_chatlist_<myname> p1 LEFT JOIN players p2 ON p1.name = p2.name ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header> {$numguest} player(s) currently in chat<end>\n\n";
	    while ($row = $db->fObject()) {
			$list .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
	    }

		$msg = bot::makeLink("Chatlist ({$numguest})", $list);
		bot::send($msg, $sendto);
	} else {
		bot::send("No players are in the channel.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>
