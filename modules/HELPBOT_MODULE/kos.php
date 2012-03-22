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
		$link = "<highlight>Most Wanted List:<end>\n\n";
		$i = 0;
		forEach ($list as $key => $value) {
			$i++;
			$link .= "$i. $key <highlight>(Voted {$value} times)<end>\n";
		}
			
		$msg = Text::make_blob("Kill On Sight list", $link);
	}

	$sendto->reply($msg);
} else if (preg_match("/^kos add (.+) reason (.+)$/i", $message, $arr) || preg_match("/^kos add (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$reason = $arr[2];
	
	if (strlen($reason) >= 50) {
		$msg = "The reason can't be longer than 50 characters.";
	} else {
		$row = $db->queryRow("SELECT * FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
		if ($row !== null) {
			$msg = "$name is already on your KOS List.";
		} else {
			$db->exec("INSERT INTO koslist (`time`, `name`, `sender`, `reason`) VALUES (?, ?, ?, ?)", time(), $name, $sender, $reason);
			$msg = "You have successfull added <highlight>$name<end> to your KOS List.";
		}
	}

	$sendto->reply($msg);
} else if (preg_match("/^kos (rem|del) (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));
	$row = $db->queryRow("SELECT * FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
	if ($row !== null) {
		$db->exec("DELETE FROM koslist WHERE `sender` = ? AND `name` = ?", $sender, $name);
		$msg = "You have successfully removed <highlight>$name<end> from your KOS List.";
	} else if ($chatBot->guildmembers[$sender] < $setting->get('guild_admin_rank')) {
		$row = $db->queryRow("SELECT * FROM koslist WHERE `name` = ?", $name);
		if ($row !== null) {
			$db->exec("DELETE FROM koslist WHERE `name` = ?", $name);
			$msg = "You have successfully removed <highlight>$name<end> from the KOS List.";
		} else {
			$msg = "<highlight>$name<end> is not on the KOS List.";
		}
	} else {
		$msg = "You don't have <higlight>$name<end> on your KOS List.";
	}

	$sendto->reply($msg);
} else if (preg_match("/^kos (.+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$data = $db->query("SELECT * FROM koslist WHERE `name` = ? LIMIT 0, 40", $name);
	if (count($data) >= 1) {
		$link = "The following players have added <highlight>$name<end> to their list\n\n";
		forEach ($data as $row) {
			$link .= "Name: <highlight>$row->sender<end>\n";
			$link .= "Date: <highlight>".date(Util::DATETIME, $row->time)."<end>\n";
			if ($row->reason != "") {
				// only show the reason if there is one
				$link .= "Reason: <highlight>$row->reason<end>\n";
			}

			$link .= "\n";
		}
		$msg = Text::make_blob("Kill On Sight list for $name", $link);
	} else {
		$msg = "<highlight>$name<end> is not on the KOS List.";
	}

	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>