<?php

if (preg_match("/^impreq ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
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
