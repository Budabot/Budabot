<?php

if (preg_match("/^whitelist$/", $message)) {
	$list = Whitelist::all();
	if (count($list) == 0) {
		$chatBot->send("No entries in whitelist", $sendto);
	} else {
		$blob = "<highlight>::: Whitelist :::<end>\n\n";
		forEach ($list as $entry) {
			$remove = Text::make_link('Remove', "/tell <myname> whitelist remove $entry->name", 'chatcmd');
			$blob .= "<white>$entry->name<end> added by <white>$entry->added_by<end> on $entry->added_dt $remove\n";
		}
		$msg = Text::make_link("Whitelist", $blob, 'blob');
		$chatBot->send($msg, $sendto);
	}
} else if (preg_match("/^whitelist add (.+)$/", $message, $arr)) {
	$chatBot->send(Whitelist::add($arr[1], $sender), $sendto);
} else if (preg_match("/^whitelist (rem|remove|del|delete) (.+)$/", $message, $arr)) {
	$chatBot->send(Whitelist::remove($arr[2]), $sendto);
}

?>