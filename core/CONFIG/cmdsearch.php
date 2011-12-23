<?php

if (preg_match("/^cmdsearch (.*)/i", $message, $arr)) {
	$search = $arr[1];

	$tmp = explode(" ", $search);
	$first = true;
	forEach ($tmp as $key => $value) {
		$value = str_replace("'", "''", $value);
		if ($first) {
			$cmdQuery .= "`cmd` LIKE '%$value%'";
			$descriptionQuery .= "`description` LIKE '%$value%'";
			$first = false;
		} else {
			$cmdQuery .= " AND `cmd` LIKE '%$value%'";
			$descriptionQuery .= " AND `description` LIKE '%$value%'";
		}
	}

	$sqlquery = "SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname> WHERE status = 1 AND ($cmdQuery) OR ($descriptionQuery)";
	$data = $db->query($sqlquery);
	
	$access = false;
	if (AccessLevel::checkAccess($sender, 'mod')) {
		$access = true;
	}
	
	$blob = "<header> :::::: Command Search Results :::::: <end>\n\n";
	forEach ($data as $row) {
		if ($row->help != '') {
			$helpLink = Text::make_chatcmd("Help", "/tell <myname> help $row->help");
		} else {
			$helpLink = Text::make_chatcmd("Help", "/tell <myname> help $row->cmd");
		}
		if ($access) {
			$module = Text::make_chatcmd($row->module, "/tell <myname> config {$row->module}");
		} else {
			$module = "<yellow>{$row->module}<end>";
		}

		$cmd = str_pad($row->cmd . " ", 20, ".");
		$blob .= "<highlight>{$cmd}<end> {$module} - {$row->description} ({$helpLink})\n";
	}

	if (count($data) == 0) {
		$msg = "No results found.";
	} else {
		$msg = Text::make_blob(count($data) . ' results found', $blob);
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>