<?php

if (preg_match("/^nanolines$/i", $message, $arr)) {
	$sql = "SELECT DISTINCT profession FROM nanolines ORDER BY profession ASC";
	$db->query($sql);
	$data = $db->fObject('all');

	$window = Text::make_header("Nanolines - Professions", "none");
	forEach ($data as $row) {
		$window .= Text::make_link($row->profession, "/tell <myname> <symbol>nlprof $row->profession", 'chatcmd');
		$window .= "\n";
	}
	$window .= "\n\nAO Nanos by Voriuste"
	$window .= "\nModule created by Tyrence (RK2)";

	$msg = Text::make_blob('Nanolines', $window);

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
