<?php

if (preg_match("/^impql ([0-9]+)$/i", $message, $arr)) {
	$ql = $arr[1];

	// make sure the $ql is an integer between 1 and 300
	if (($ql < 1) || ($ql > 300)) {
		$msg = "You must enter a value between 1 and 300.";
	} else {
		$obj = getRequirements($ql);
		$clusterInfo = formatClusterBonuses($obj);
		$link = Text::make_blob('More info', $clusterInfo);
		$msg = "\nFor ql $ql imps\nTreatment required: $obj->treatment.\nAbility Required: $obj->ability\n$link";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
