<?php

if (isset($chatBot->data['bior'])) {
	forEach($chatBot->data['bior'] as $key => $value) {
		if ($chatBot->data['bior'][$key]["b"] != "ready") {
			$rem = $chatBot->data['bior'][$key]["b"] - time();
			if ($rem >= 319 && $rem <= 321) {
				$msg = "<blue>20sec remaining on Bio Regrowth.<end>";
				$sendto->reply($msg);
			} else if ($rem >= 305 && $rem <= 307) {
				$pos = array_search($key, $chatBot->data['blist']);
				if (isset($chatBot->data['blist'][$pos + 1])) {
					$next = " <yellow>Next is {$chatBot->data['blist'][$pos + 1]}<end>";
				}
				$msg = "<blue>6sec remaining on Bio Regrowth.$next<end>";  		
				$sendto->reply($msg);
			} else if ($rem >= 299 && $rem <= 301) {
				$pos = array_search($key, $chatBot->data['blist']);
				if (isset($chatBot->data['blist'][$pos + 1])) {
					$next = " <yellow>Next is {$chatBot->data['blist'][$pos + 1]}<end>";
				}
				$msg = "<blue>Bio Regrowth has terminated.$next<end>";
				$sendto->reply($msg);
			} else if ($rem <= 0) {
				$msg = "<blue>Bio Regrowth is ready on $key.<end>";
				$chatBot->data['bior'][$key]["b"] = "ready";
				$sendto->reply($msg);
			}
		}
	}
}

?>