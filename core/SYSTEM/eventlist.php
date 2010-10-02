<?php

if (preg_match("/^eventlist$/i", $message, $arr) || preg_match("/^eventlist (towers|orgmsg|msg|priv|extPriv|guild|joinPriv|extJoinPriv|leavePriv|extLeavePriv|extJoinPrivRequest|extKickPriv|logOn|logOff|2sec|1min|10mins|15mins|30mins|1hour|24hrs|connect|setup)$/i", $message, $arr)) {
	$list  = "<header>::::: Bot Settings -- Command List :::::<end>\n\n";

	if ($arr[1] != '') {
		$cmdSearchSql = "AND c.type LIKE '%{$arr[1]}%'";
	}

	$sql = "
		SELECT
			c.type,
			c.description,
			c.module,
			c.file,
			c.status
		FROM
			cmdcfg_<myname> c
		WHERE
			c.cmdevent = 'event'
			$cmdSearchSql
		ORDER BY
			type ASC";
	$db->query($sql);

	while ($row = $db->fObject()) {
		$on = bot::makeLink('ON', "/tell <myname> config cmd $row->cmd enable all", 'chatcmd');
		$off = bot::makeLink('OFF', "/tell <myname> config cmd $row->cmd disable all", 'chatcmd');
		$adv = bot::makeLink('Adv.', "/tell <myname> config cmd $row->cmd $row->module", 'chatcmd');

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

	$msg = bot::makeLink("Bot Settings -- Command List", $list);
 	bot::send($msg, $sendto);
}

?>