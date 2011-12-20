<?php

if (preg_match("/^notes$/i", $message)) {
	$blob = "<header> :::::: Notes for $sender :::::: <end>\n\n";

	$sql = "SELECT * FROM notes WHERE name LIKE ?";
  	$data = $db->query($sql, $sender);
	
	if (count($data) == 0) {
		$msg = "No notes for $sender.";
	} else {
		forEach ($data as $row) {
			$remove = Text::make_chatcmd('Remove', "/tell <myname> <symbol>notes rem $row->id");
			$blob .= "$remove $row->note\n\n";
		}
		$msg = Text::make_blob("Notes for $sender", $blob);
	}
  	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^notes add (.*)$/i", $message, $arr)) {
	$note = $arr[1];

	$db->exec("INSERT INTO notes (name, note) VALUES(?, ?)", $sender, $note);
	$msg = "Note added successfully.";

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^notes rem (\\d+)$/i", $message, $arr)) {
	$id = $arr[1];

	$numRows = $db->exec("DELETE FROM notes WHERE id = ? AND name LIKE ?", $id, $sender);
	if ($numRows == 0) {
		$msg = "Note could not be found or note does not belong to you.";
	} else {
		$msg = "Note deleted successfully.";
	}

    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
