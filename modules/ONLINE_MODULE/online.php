<?php

if (preg_match("/^online$/i", $message) || preg_match("/^online (.*)$/i", $message, $arr)) {
	if (isset($arr)) {
		$prof = strtolower($arr[1]);
		if ($prof != 'all') {
			$prof = Util::get_profession_name($prof);
		}

		if ($prof == null) {
			$syntax_error = true;
			return;
		}
	} else {
		$prof = 'all';
	}

	list($numonline, $msg, $list) = get_online_list($prof);
	if ($numonline != 0) {
		$blob = Text::make_structured_blob($msg, $list);
		$sendto->reply($blob);
	} else {
		$sendto->reply($msg);
	}
} else {
	$syntax_error = true;
}

?>
