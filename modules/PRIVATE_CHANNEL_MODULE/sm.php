<?php

if (preg_match("/^sm$/i", $message)) {
	if (count($chatBot->chatlist) > 0) {
		$data = $db->query("SELECT p.*, o.name as name FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE `channel_type` = 'priv' AND added_by = '<myname>' ORDER BY `profession`, `level` DESC");
		$numguest = count($data);

		$blob = '';
	    forEach ($data as $row) {
			if ($row->profession == null) {
				$blob .= "<white>$row->name<white> - Unknown\n";
			} else {
				$blob .= "<white>$row->name - $row->level<end><green>/$row->ai_level<end><white> $row->profession, $row->guild<end>\n";
			}
	    }

		$msg = Text::make_blob("Chatlist ($numguest)", $blob);
		$chatBot->send($msg, $sendto);
	} else {
		$chatBot->send("No players are in the private channel.", $sendto);
	}
} else {
	$syntax_error = true;
}

?>
