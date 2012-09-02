<?php

if (preg_match("/^lastseen (.+)$/i", $message, $arr)) {
	// Get User id
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
        $msg = "Character <highlight>$name<end> does not exist.";
    } else {
		$alts = Registry::getInstance('alts');
		$altInfo = $alts->get_alt_info($name);
		$onlineAlts = $altInfo->get_online_alts();
		if (count($onlineAlts) > 0) {
			$msg = "This player is currently <green>online<end> as " . implode(', ', $onlineAlts) . ".";
		} else {
			$namesSql = '';
			forEach ($altInfo->get_all_alts() as $alt) {
				if ($namesSql) {
					$namesSql .= ", ";
				}
				$namesSql .= "'$alt'";
			}
			$row = $db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` IN ($namesSql) AND `mode` != 'del' ORDER BY logged_off DESC");

			if ($row !== null) {
				if ($row->logged_off == 0) {
					$msg = "<highlight>$name<end> has never logged on.";
				} else {
					$msg = "Last seen at ".date(Util::DATETIME, $row->logged_off)."(GMT) on <highlight>" . $row->name . "<end>.";
				}
			} else {
				$msg = "This player is not a member of the org.";
			}
		}
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
