<?php

if (isset($chatBot->guildmembers[$sender]) && $chatBot->is_ready()) {
	$msg = "";
	list($numonline, $msg, $list) = online($sender, $sendto, $this);
	if ($numonline != 0) {
		$blob = Text::make_link($msg, $list);
		$chatBot->send($blob, $sender);
	} else {
		$chatBot->send($msg, $sender);
	}
}
?>
