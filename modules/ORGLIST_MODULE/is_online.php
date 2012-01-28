<?php

if (preg_match("/^is (.+)$/i", $message, $arr)) {
    // Get User id
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if (!$uid) {
        $msg = "Character <highlight>$name<end> does not exist.";
		$sendto->reply($msg);
    } else {
        //if the player is a buddy then
		$online_status = $buddylistManager->is_online($name);
		if ($online_status === null) {
			$chatBot->data["ONLINE_MODULE"]['playername'] = $name;
			$chatBot->data["ONLINE_MODULE"]['sendto'] = $sendto;
			$buddylistManager->add($name, 'is_online');
		} else {
            $row = $db->queryRow("SELECT * FROM org_members_<myname> WHERE `name` = ?", $name);
            if ($row !== null) {
                if ($row->logged_off != "0") {
                    $logged_off = "\nLogged off at ".date(Util::DATETIME, $row->logged_off)."(GMT)";
				}
            }
            if ($online_status) {
                $status = "<green>online<end>";
            } else {
                $status = "<red>offline<end>".$logged_off;
			}
            $msg = "Character <highlight>$name<end> is $status.";
			$sendto->reply($msg);
        }
    }
} else {
	$syntax_error = true;
}

?>
