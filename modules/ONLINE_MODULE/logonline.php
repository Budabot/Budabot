<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	list($numonline, $msg, $list) = get_online_list();
	if ($numonline != 0) {
		$blob = Text::make_structured_blob($msg, $list);
		$chatBot->sendTell($blob, $sender);
	} else {
		$chatBot->sendTell($msg, $sender);
	}
}
?>
