<?php
if (isset($chatBot->data['guard'])) {
	forEach($chatBot->data['guard'] as $key => $value) {
		if($chatBot->data['guard'][$key]["g"] != "ready") {
			$rem = $chatBot->data['guard'][$key]["g"] - time();
			if($rem >= 319 && $rem < 321) {
				$msg = "<blue>20sec remaining on Guardian.<end>";
				$sendto->reply($msg);
			} elseif($rem >= 305 && $rem <= 307) {
				$pos = array_search($key, $chatBot->data['glist']);
				if(isset($chatBot->data['glist'][$pos + 1]))
					$next = " <yellow>Next is {$chatBot->data['glist'][$pos + 1]}<end>";
				$msg = "<blue>6sec remaining on Guardian.$next<end>";  		
				$sendto->reply($msg);
			} elseif($rem >= 299 && $rem <= 301) {
				$pos = array_search($key, $chatBot->data['glist']);
				if(isset($chatBot->data['glist'][$pos + 1]))
					$next = " <yellow>Next is {$chatBot->data['glist'][$pos + 1]}<end>";
				$msg = "<blue>Guardian has terminated.$next<end>";
				$sendto->reply($msg);
			} elseif($rem <= 0) {
				$msg = "<blue>Guardian is ready on $key.<end>";
				$chatBot->data['guard'][$key]["g"] = "ready";
				$sendto->reply($msg);
			}
		}
	}
}

?>