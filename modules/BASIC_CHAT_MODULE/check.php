<?php

$channel_type = "priv";
   
if (preg_match("/^check$/i", $message) || preg_match("/^check all$/i", $message)) {
	$list = "<header>::::: Check for all members :::::<end>\n\n";
	$db->query("SELECT name FROM online WHERE added_by = '<myname>' AND channel_type = '{$channel_type}'");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$content .= " \\n /assist $row->name";
	}

	$list .= "<a href='chatcmd:///text AssistAll: $content'>Click here to check who is here</a>";
	$msg = Text::make_blob("Check on all", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^check prof$/i", $message)) {
	$list = "<header>::::: Check for all professions :::::<end>\n\n";
	$db->query("SELECT o.name, p.profession FROM online o LEFT JOIN players p ON (o.name = o.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = '{$channel_type}' ORDER BY `profession` DESC");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		$prof[$row->profession] .= " \\n /assist $row->name";
	}

	ksort($prof);
	
	forEach ($prof as $key => $value) {
		$list .= "<a href='chatcmd:///text Assist $key: $value'>Click here to check $key</a>\n";
	}

	$msg = Text::make_blob("Check by profession", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^check org$/i", $message)) {
	$list = "<header>::::: Check for all organizations :::::<end>\n\n";
	$db->query("SELECT o.name, p.guild FROM online o LEFT JOIN players p ON (o.name = p.name AND p.dimension = '<dim>') WHERE added_by = '<myname>' AND channel_type = '{$channel_type}' ORDER BY `guild` DESC");
	$data = $db->fObject('all');
	forEach ($data as $row) {
		if ($row->guild == "") {
			$org["Non orged"] .= " \\n /assist $row->name";
		} else {
			$org[$row->guild] .= " \\n /assist $row->name";
		}
	}
	
	ksort($org);
	
	forEach ($org as $key => $value) {
		$list .= "<a href='chatcmd:///text Assist $key: $value'>Click here to check $key</a>\n";
	}

	$msg = Text::make_blob("Check by Organization", $list);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>