<?php

if (isset($this->guildmembers[$sender])) {
	$msg = "";
	list($numonline, $msg, $list) = online($sender, $sendto, $this);
	if ($numonline != 0) {
		$blob = Text::make_link($msg, $list);
		bot::send($blob, $sender);
	} else {
		bot::send($msg, $sender);
	}
}
?>
