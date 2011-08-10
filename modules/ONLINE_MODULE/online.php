<?php

if (preg_match("/^online$/i", $message) || preg_match("/^online (.*)$/i", $message, $arr)) {
	if ($arr) {
		$prof = strtolower($arr[1]);
		if ($prof != 'all') {
			$prof = Util::get_profession_name($prof);
		}
		
		if ($prof === null) {
			$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, trad or all";
			$chatBot->send($msg, $sendto);
			return;
		}
	} else {
		$prof = 'all';
	}

	list($numonline, $msg, $list) = get_online_list($prof);
	if ($numonline != 0) {
		$blob = Text::make_blob($msg, $list);
		$chatBot->send($blob, $sendto);
	} else {
		$chatBot->send($msg, $sendto);
	}
} else {
	$syntax_error = true;
}

?>
