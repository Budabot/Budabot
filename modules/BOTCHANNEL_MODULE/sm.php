<?php

if (preg_match("/^sm$/i", $message)) {
	if (count($this->vars["Guest"]) > 0) {
		$db->query("SELECT * FROM priv_chatlist ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header> {$numguest} player currently in chat<end>\n\n";
	    while ($row = $db->fObject()) {
			$list .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
	    }

		$msg = bot::makeLink("Chatlist ({$numguest} players)", $list);
		bot::send($msg, $sendto);
	} else {
		bot::send("No players are in the channel.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>
