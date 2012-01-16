<?php

$whitelist = Registry::getInstance('whitelist');

if (preg_match("/^whitelist$/", $message)) {
	$list = $whitelist->all();
	if (count($list) == 0) {
		$sendto->reply("No entries in whitelist");
	} else {
		$blob = '';
		forEach ($list as $entry) {
			$remove = Text::make_chatcmd('Remove', "/tell <myname> whitelist remove $entry->name");
			$blob .= "<white>{$entry->name}<end> [<green>added by {$entry->added_by}<end>] <white>{$entry->added_dt}<end> {$remove}\n";
		}
		$msg = Text::make_blob("Whitelist", $blob);
		$sendto->reply($msg);
	}
} else if (preg_match("/^whitelist add (.+)$/", $message, $arr)) {
	$sendto->reply($whitelist->add($arr[1], $sender));
} else if (preg_match("/^whitelist (rem|remove|del|delete) (.+)$/", $message, $arr)) {
	$sendto->reply($whitelist->remove($arr[2]));
} else {
	$syntax_error = true;
}

?>