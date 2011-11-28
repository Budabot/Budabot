<?php

if (preg_match("/^implant ([0-9]+)$/i", $message, $arr)) {
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
} else if (preg_match("/^implant ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	$ability = $arr[1];
	$treatment = $arr[2];

	if ($treatment < 11 || $ability < 6) {
		$msg = "You do not have enough treatment or ability to wear an implant.";
	} else {
		$obj = findMaxImplantQlByReqs($ability, $treatment);
		$clusterInfo = formatClusterBonuses($obj);
		$link = Text::make_blob("ql $obj->ql", $clusterInfo);

		$msg = "\nThe highest ql implant you can wear is $link which requires:\nTreatment: $obj->treatment\nAbility: $obj->ability";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
