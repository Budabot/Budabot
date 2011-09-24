<?php

if (isset($chatBot->largespam)) {
	forEach ($chatBot->largespam as $key => $value){
		if ($value > 0) {
			$chatBot->largespam[$key] = $value - 1;
		} else {
			$chatBot->largespam[$key] = 0;
		}
	}
}

?>