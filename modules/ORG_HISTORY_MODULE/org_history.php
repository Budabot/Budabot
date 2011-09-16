<?php
   
if (preg_match("/^orghistory$/i", $message, $arr) || preg_match("/^orghistory (\\d+)$/i", $message, $arr)) {
	
	$pageSize = 20;
	$page = 1;
	if ($arr[1] != '') {
		$page = $arr[1];
	}
	
	$startingRecord = ($page - 1) * $pageSize;

	$window = Text::make_header("Org History", array('Help' => '/tell <myname> help orghistory'));
	
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` ORDER BY time DESC LIMIT $startingRecord, $pageSize";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$msg = Text::make_blob('Org History', $window);

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^orghistory (.+)$/i", $message, $arr)) {

	$character = $arr[1];

	$window = Text::make_header("Org History", array('Help' => '/tell <myname> help orghistory'));
	
	$window .= "\n  Actions on $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` WHERE actee LIKE '$character' ORDER BY time DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$window .= "\n  Actions by $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` WHERE actor LIKE '$character' ORDER BY time DESC";
	$db->query($sql);
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$window .= "$row->actor $row->action $row->actee in $row->organization at " . gmdate("M j, Y, G:i", $row->time)." (GMT)\n";
	}

	$msg = Text::make_blob('Org History', $window);

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
