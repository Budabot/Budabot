<?php

if (preg_match("/^sm$/i", $message)) {
	if (count($chatBot->chatlist) > 0) {
		$db->query("SELECT p.*, o.name as name FROM online o LEFT JOIN players p ON o.name = p.name WHERE `channel_type` = 'priv' AND added_by = '<myname>' ORDER BY `profession`, `level` DESC");
		$numguest = $db->numrows();

		$list = "<header> {$numguest} player(s) currently in chat<end>\n\n";
	    while ($row = $db->fObject()) {
			if ($row->profession == null) {
				$list .= "<white>$row->name<white> - Unknown\n";
			} else {
				$list .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
			}
	    }

		$msg = Text::make_blob("Chatlist ({$numguest})", $list);
		$chatBot->send($msg, $sendto);
	} else {
		$chatBot->send("No players are in the private channel.", $sendto);
	}
} else {
	$syntax_error = true;
}
?>
