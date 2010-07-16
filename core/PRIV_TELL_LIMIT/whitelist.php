<?php

if (preg_match("/^whitelist$/", $message)) {
	$list = Whitelist::all();
	if (count($list) == 0) {
		bot::send("No entries in whitelist", $sendto);
	} else {
		$blob = "<highlight>::: Whitelist :::<end>\n\n";
		forEach ($list as $entry) {
			$remove = bot::makeLink('Remove', "/tell <myname> whitelist remove $entry->name", 'chatcmd');
			$blob .= "<white>$entry->name<end> added by <white>$entry->added_by<end> on $entry->added_dt $remove\n";
		}
		$msg = bot::makeLink("Whitelist", $blob, 'blob');
		bot::send($msg, $sendto);
	}
} else if (preg_match("/^whitelist add (.+)$/", $message, $arr)) {
	bot::send(Whitelist::add($arr[1], $sender), $sendto);
} else if (preg_match("/^whitelist (rem|remove|del|delete) (.+)$/", $message, $arr)) {
	bot::send(Whitelist::remove($arr[2]), $sendto);
}

?>