<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	list($numonline, $msg, $list) = get_online_list();
	if ($numonline != 0) {
		$blob = Text::make_link($msg, $list);
		$chatBot->send($blob, $sender);
	} else {
		$chatBot->send($msg, $sender);
	}
}
?>
