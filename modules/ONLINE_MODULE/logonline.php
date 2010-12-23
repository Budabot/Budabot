<?php

if (isset($this->guildmembers[$sender])) {
	$msg = "";
	$type = "msg";
	list($numonline, $msg, $list) = online($type, $sender, $sendto, $this);
	if ($numonline != 0) {
		$blob = bot::makeLink($msg, $list);
		bot::send($blob, $sender);
	} else {
		bot::send($msg, $sender);
	}
}
?>
