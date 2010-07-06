<?php

if (preg_match("/^cmdlist$/i", $message, $arr)) {
	$list  = "<header>::::: Bot Settings -- Command List :::::<end>\n\n";

	$sql = "
		SELECT
			cmd,
			description,
			module,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type = 'guild') guild_avail,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type = 'guild' AND status = 1) guild_status,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type ='priv') priv_avail,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type = 'priv' AND status = 1) priv_status,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type ='msg') msg_avail,
			(SELECT count(*) FROM cmdcfg_<myname> WHERE cmd = c.cmd AND type = 'msg' AND status = 1) msg_status
		FROM
			cmdcfg_<myname> c
		WHERE
			cmdevent = 'cmd'
			OR cmdevent = 'subcmd'
		GROUP BY
			cmd, description, module
		ORDER BY
			cmd ASC";
	$db->query($sql);
	while ($row = $db->fObject()) {
		$guild = '';
		$priv = '';
		$msg = '';
		
		$on = "<a href='chatcmd:///tell <myname> config cmd $row->cmd enable all'>ON</a>";
		$off = "<a href='chatcmd:///tell <myname> config cmd $row->cmd disable all'>OFF</a>";
		$adv = "<a href='chatcmd:///tell <myname> config cmd $row->cmd $row->module'>Adv.</a>";
		
		if ($row->msg_avail == 0)
			$tell = "|_";
		else if ($row->msg_status == 1)
			$tell = "|<green>T<end>";
		else
			$tell = "|<red>T<end>";
		
		if ($row->guild_avail == 0)
			$guild = "|_";
		else if ($row->guild_status == 1)
			$guild = "|<green>G<end>";
		else
			$guild = "|<red>G<end>";
		
		if ($row->priv_avail == 0)
			$priv = "|_";
		else if ($row->priv_status == 1)
			$priv = "|<green>P<end>";
		else
			$priv = "|<red>P<end>";
		
		if ($row->description != "") {
			$list .= "$row->cmd ($adv$tell$guild$priv): $on  $off - ($row->description)\n";
		} else {
			$list .= "$row->cmd - ($adv$tell$guild$priv): $on  $off\n";
		}
	}

	$msg = $this->makeLink("Bot Settings -- Command List", $list);
 	$this->send($msg, $sendto);
}

?>