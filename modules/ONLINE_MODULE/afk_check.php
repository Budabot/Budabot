<?php

// to stop raising and lowering the cloak messages from triggering afk check
if (!Util::isValidSender($sender)) {
	return;
}

if (!preg_match("/^.?afk(.*)$/i", $message)) {
	$row = $db->queryRow("SELECT afk FROM online WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);

	if ($row !== null && $row->afk != '') {
		$db->exec("UPDATE online SET `afk` = '' WHERE `name` = ? AND added_by = '<myname>' AND channel_type = ?", $sender, $type);
		$msg = "<highlight>{$sender}<end> is back";
	} else {
		list($name, $other) = explode(" ", $message, 2);
		$name = ucfirst(strtolower($name));

		if (isset($this->id[$name]) && Util::isValidSender($this->id[$name])) {
			$row = $db->queryRow("SELECT afk FROM online WHERE `name` = ? AND added_by = '<myname>'", $name);

			if ($row !== null) {
				if ($row->afk == "1") {
					$msg = "<highlight>{$name}<end> is currently AFK.";
				} else if ($row->afk == "kiting") {
					$msg = "<highlight>{$name}<end> is currently Kiting.";
				} else if ($row->afk != "") {
					$msg = "<highlight>{$name}<end> is currently AFK: <highlight>{$row->afk}<end>";
				}
			}
		}
	}
	
	if ('' != $msg) {
		if ('priv' == $type) {
			$this->chatBot->sendPriv($msg);
		} else if ('guild' == $type) {
			$this->chatBot->sendGuild($msg);
		}
	}
}

?>
