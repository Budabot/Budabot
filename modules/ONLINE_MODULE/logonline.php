<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	list($numonline, $msg, $blob) = get_online_list();
	if ($numonline != 0) {
		$msg = Text::make_blob($msg, $blob);
		$chatBot->sendTell($msg, $sender);
	} else {
		$chatBot->sendTell($msg, $sender);
	}
}
?>
