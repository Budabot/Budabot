<?php

if (preg_match("/^kos$/i", $message)) {
	$data = $db->query("SELECT * FROM koslist");
	if (count($data) == 0) {
		$msg = "No one is on the KOS list.";
	} else {
		forEach ($data as $row) {
			$list[$row->name]++;
		}

		arsort($list);
		$list = array_slice($list, 0, 25, true);
		$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
		$link .= "This list shows the top 25 of added Players\n\n";
		$i = 0;
		forEach ($list as $key => $value) {
			$i++;
			$link .= "$i. $key <highlight>(Voted {$value} times)<end>\n";
		}
			
		$msg = Text::make_blob("KOS-List", $link);
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos add (.+)$/i", $message, $arr)) {
	$explodemsg = explode(' ', $arr[1], 3);
	$name = ucfirst(strtolower($explodemsg[0]));
	if ('reason' == $explodemsg[1]) {
		// compatibility for old style syntax add X reason Y
		$reason = $explodemsg[2];
	} else {
		// otherwise stitch the reason back together
		$reason = $explodemsg[1] . ' ' . $explodemsg[2];
	}

	if (strlen($reason) >= 50) {
		$msg = "The reason can't be longer than 50 characters.";
	} else {
		$row = $db->queryRow("SELECT * FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
		if ($row !== null) {
			$msg = "You have already <highlight>$name<end> on your KOS List.";
		} else {
			$db->exec("INSERT INTO koslist (`time`, `name`, `sender`, `reason`) VALUES (?, ?, ?, ?)", time(), $name, $sender, $reason);
			$msg = "You have successfull added <highlight>$name<end> to the KOS List.";
		}
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos rem (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$row = $db->queryRow("SELECT * FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
	if ($row !== null) {
		$db->exec("DELETE FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
		$msg = "You have successfully removed <highlight>$name<end> from the KOS List.";
	} else if ($chatBot->guildmembers[$sender] < $setting->get('guild_admin_level')) {
		$row = $db->queryRow("SELECT * FROM koslist WHERE `name` = ?", $name);
		if ($row !== null) {
			$db->exec("DELETE FROM koslist WHERE `name` = ?", $name);
			$msg = "You have successfully removed <highlight>$name<end> from the KOS List.";
		} else {
			$msg = "No one with this name is on the KOS List.";
		}
	} else {
		$msg = "You don't have this player on your KOS List.";
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^kos (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$data = $db->query("SELECT * FROM koslist WHERE `name` = ? LIMIT 0, 40", $name);
	if (count($data) >= 1) {
		$link  = "<header>::::: Kill On Sight list :::::<end>\n\n";
		$link .= "The following Players has added <highlight>$name<end> to his list\n\n";
		forEach ($data as $row) {
			$link .= "Name: <highlight>$row->sender<end>\n";
			$link .= "Date: <highlight>".date("dS F Y, H:i", $row->time)."<end>\n";
			if ($row->reason != "") {
				// only show the reason if there is one
				$link .= "Reason: <highlight>$row->reason<end>\n";
			}

			$link .= "\n";
		}
		$msg = Text::make_blob("KOS-List from $name", $link);
	} else {
		$msg = "Character <highlight>$name<end> is not on the KOS List.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>