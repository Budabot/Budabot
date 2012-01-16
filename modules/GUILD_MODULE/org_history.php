<?php
   
if (preg_match("/^orghistory$/i", $message, $arr) || preg_match("/^orghistory (\\d+)$/i", $message, $arr)) {
	
	$pageSize = 20;
	$page = 1;
	if ($arr[1] != '') {
		$page = $arr[1];
	}
	
	$startingRecord = ($page - 1) * $pageSize;

	$blob = '';
	
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` ORDER BY time DESC LIMIT $startingRecord, $pageSize";
	$data = $db->query($sql);
	forEach ($data as $row) {
		$blob .= "$row->actor $row->action <highlight>$row->actee<end> in $row->organization at " . date(Util::DATETIME, $row->time)." (GMT)\n";
	}

	$msg = Text::make_blob('Org History', $blob);

	$sendto->reply($msg);
} else if (preg_match("/^orghistory (.+)$/i", $message, $arr)) {

	$character = $arr[1];

	$blob = '';
	
	$window .= "\n  Actions on $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` WHERE actee LIKE ? ORDER BY time DESC";
	$data = $db->query($sql, $character);
	forEach ($data as $row) {
		$blob .= "$row->actor $row->action <highlight>$row->actee<end> in $row->organization at " . date(Util::DATETIME, $row->time)." (GMT)\n";
	}

	$blob .= "\n  Actions by $character\n";
	$sql = "SELECT actor, actee, action, organization, time FROM `#__org_history` WHERE actor LIKE ? ORDER BY time DESC";
	$data = $db->query($sql, $character);
	forEach ($data as $row) {
		$blob .= "$row->actor $row->action <highlight>$row->actee<end> in $row->organization at " . date(Util::DATETIME, $row->time)." (GMT)\n";
	}

	$msg = Text::make_blob('Org History', $blob);

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
