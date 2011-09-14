<?php
echo "hi";
forEach ($chatBot->spam as $key => $value){
	if ($value > 0) {
		$chatBot->spam[$key] = $value - 10;
	} else {
		$chatBot->spam[$key] = 0;
	}
}

?>