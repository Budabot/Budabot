<?php

if (preg_match("/^whitelist$/", $message)) {
	$list = Whitelist::all();
	if (count($list) == 0) {
		$chatBot->send("No entries in whitelist", $sendto);
	} else {
		$blob = "<header> :::::: Whitelist :::::: <end>\n\n";
		forEach ($list as $entry) {
			$remove = Text::make_chatcmd('Remove', "/tell <myname> whitelist remove $entry->name");
			$blob .= "<white>{$entry->name}<end> [<green>added by {$entry->added_by}<end>] <white>{$entry->added_dt}<end> {$remove}\n";
		}
		$msg = Text::make_blob("Whitelist", $blob);
		$chatBot->send($msg, $sendto);
	}
} else if (preg_match("/^whitelist add (.+)$/", $message, $arr)) {
	$chatBot->send(Whitelist::add($arr[1], $sender), $sendto);
} else if (preg_match("/^whitelist (rem|remove|del|delete) (.+)$/", $message, $arr)) {
	$chatBot->send(Whitelist::remove($arr[2]), $sendto);
} else {
	$syntax_error = true;
}

?>