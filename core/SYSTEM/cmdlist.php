<?php

if (preg_match("/^cmdlist$/i", $message, $arr) || preg_match("/^cmdlist (.*)$/i", $message, $arr)) {
	$list  = "<header> :::::: Command List :::::: <end>\n\n";
	
	if ($arr[1] != '') {
		$cmdSearchSql = "AND c.cmd LIKE '%{$arr[1]}%'";
	}

	$sql = "
		SELECT
			cmd,
			cmdevent,
			description,
			module,
			file,
			admin,
			dependson,
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

	$data = $db->fObject('all');
	forEach ($data as $row) {
		$guild = '';
		$priv = '';
		$msg = '';
		
		if ($row->cmdevent == 'subcmd') {
			$cmd = $row->dependson;
		} else {
			$cmd = $row->cmd;
		}

		if (AccessLevel::checkAccess($sender, '3')) {
			$on = Text::make_link('ON', "/tell <myname> config cmd $cmd enable all", 'chatcmd');
			$off = Text::make_link('OFF', "/tell <myname> config cmd $cmd disable all", 'chatcmd');
			$adv = Text::make_link('Permissions', "/tell <myname> config cmd $cmd", 'chatcmd');
			$adv_link = " ($adv) $on  $off";
		}
		
		if ($row->msg_avail == 0) {
			$tell = "_";
		} else if ($row->msg_status == 1) {
			$tell = "<green>T<end>";
		} else {
			$tell = "<red>T<end>";
		}
		
		if ($row->guild_avail == 0) {
			$guild = "_";
		} else if ($row->guild_status == 1) {
			$guild = "<green>G<end>";
		} else {
			$guild = "<red>G<end>";
		}
		
		if ($row->priv_avail == 0) {
			$priv = "_";
		} else if ($row->priv_status == 1) {
			$priv = "<green>P<end>";
		} else {
			$priv = "<red>P<end>";
		}
		
		$list .= "$row->cmd ({$tell}|{$guild}|{$priv}) {$adv_link} - ($row->description)\n";
	}

	$msg = Text::make_blob("Bot Settings -- Command List", $list);
 	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>