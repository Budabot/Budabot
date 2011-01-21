<?php

if (preg_match("/^cmdlist$/i", $message, $arr) || preg_match("/^cmdlist (.*)$/i", $message, $arr)) {
	$list  = "<header>::::: Bot Settings -- Command List :::::<end>\n\n";
	
	if ($arr[1] != '') {
		$cmdSearchSql = "AND c.cmd LIKE '%{$arr[1]}%'";
	}

	$sql = "
		SELECT
			cmd,
			description,
			module,
			file,
			admin,
			(SELECT count(*) FROM cmdcfg_<myname> t1 WHERE t1.cmd = c.cmd AND t1.type = 'guild') guild_avail,
			(SELECT count(*) FROM cmdcfg_<myname> t2 WHERE t2.cmd = c.cmd AND t2.type = 'guild' AND t2.status = 1) guild_status,
			(SELECT count(*) FROM cmdcfg_<myname> t3 WHERE t3.cmd = c.cmd AND t3.type ='priv') priv_avail,
			(SELECT count(*) FROM cmdcfg_<myname> t4 WHERE t4.cmd = c.cmd AND t4.type = 'priv' AND t4.status = 1) priv_status,
			(SELECT count(*) FROM cmdcfg_<myname> t5 WHERE t5.cmd = c.cmd AND t5.type ='msg') msg_avail,
			(SELECT count(*) FROM cmdcfg_<myname> t6 WHERE t6.cmd = c.cmd AND t6.type = 'msg' AND t6.status = 1) msg_status
		FROM
			cmdcfg_<myname> c
		WHERE
			(c.cmdevent = 'cmd'	OR c.cmdevent = 'subcmd')
			$cmdSearchSql
		GROUP BY
			c.cmd, c.description, c.module
		ORDER BY
			cmd ASC";
	$db->query($sql);

	while ($row = $db->fObject()) {
		$guild = '';
		$priv = '';
		$msg = '';

		$on = bot::makeLink('ON', "/tell <myname> config cmd $row->cmd enable all", 'chatcmd');
		$off = bot::makeLink('OFF', "/tell <myname> config cmd $row->cmd disable all", 'chatcmd');
		$adv = bot::makeLink('Adv.', "/tell <myname> config cmd $row->cmd", 'chatcmd');
		
		if ($row->msg_avail == 0) {
			$tell = "|_";
		} else if ($row->msg_status == 1) {
			$tell = "|<green>T<end> ($row->admin)";
		} else {
			$tell = "|<red>T<end> ($row->admin)";
		}
		
		if ($row->guild_avail == 0) {
			$guild = "|_";
		} else if ($row->guild_status == 1) {
			$guild = "|<green>G<end> ($row->admin)";
		} else {
			$guild = "|<red>G<end> ($row->admin)";
		}
		
		if ($row->priv_avail == 0) {
			$priv = "|_";
		} else if ($row->priv_status == 1) {
			$priv = "|<green>P<end> ($row->admin)";
		} else {
			$priv = "|<red>P<end> ($row->admin)";
		}
		
		if ($row->description != "") {
			$list .= "$row->cmd [$row->file] ($adv$tell$guild$priv): $on  $off - ($row->description)\n";
		} else {
			$list .= "$row->cmd - ($adv$tell$guild$priv): $on  $off\n";
		}
	}

	$msg = bot::makeLink("Bot Settings -- Command List", $list);
 	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>