<?php

if (preg_match("/^sm$/i", $message)) {
	if (count($this->chatlist) > 0) {
		$db->query("SELECT p2.*, p1.name as name FROM priv_chatlist_<myname> p1 LEFT JOIN players p2 ON p1.name = p2.name ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header> {$numguest} player(s) currently in chat<end>\n\n";
	    while ($row = $db->fObject()) {
			if ($row->profession == null) {
				$list .= "<white>$row->name<white> - Unknown\n";
			} else {
				$list .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
			}
	    }

		$msg = Text::make_link("Chatlist ({$numguest})", $list);
		$chatBot->send($msg, $sendto);
	} else {
		$chatBot->send("No players are in the private channel.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>
