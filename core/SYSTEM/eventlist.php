<?php

if (preg_match("/^eventlist$/i", $message, $arr) || preg_match("/^eventlist (towers|orgmsg|msg|priv|extPriv|guild|joinPriv|extJoinPriv|leavePriv|extLeavePriv|extJoinPrivRequest|extKickPriv|logOn|logOff|2sec|1min|10mins|15mins|30mins|1hour|24hrs|connect|setup)$/i", $message, $arr)) {
	$list  = "<header>::::: Bot Settings -- Command List :::::<end>\n\n";

	if ($arr[1] != '') {
		$cmdSearchSql = "WHERE c.type LIKE '{$arr[1]}'";
	}

	$sql = "
		SELECT
			type,
			description,
			module,
			file,
			status
		FROM
			eventcfg_<myname>
		$cmdSearchSql
		ORDER BY
			type ASC";
	$db->query($sql);
	$data = $db->fObject();
	forEach ($data as $row) {
		$on = Text::make_link('ON', "/tell <myname> config cmd $row->cmd enable all", 'chatcmd');
		$off = Text::make_link('OFF', "/tell <myname> config cmd $row->cmd disable all", 'chatcmd');
		$adv = Text::make_link('Adv.', "/tell <myname> config cmd $row->cmd $row->module", 'chatcmd');

		if ($row->status == 1) {
			$status = "<green>Enabled<end>";
		} else {
			$status = "<red>Disabled<end>";
		}

		if ($row->description != "") {
			$list .= "$row->type [$row->file] ($adv|$status): $on  $off - ($row->description)\n";
		} else {
			$list .= "$row->type [$row->file] ($adv|$status): $on  $off\n";
		}
	}

	$msg = Text::make_blob("Bot Settings -- Command List", $list);
 	$chatBot->send($msg, $sendto);
}

?>