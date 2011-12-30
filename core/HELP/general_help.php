<?php

if (preg_match("/^about$/i", $message) || preg_match("/^help about$/i", $message)) {
	global $version;
	$data = file_get_contents("./core/HELP/about.txt");
	$data = str_replace('<version>', $version, $data);
	$msg = Text::make_blob("About Budabot", $data);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^help$/i", $message)) {
	global $version;

	$sql = "SELECT * FROM hlpcfg_<myname> ORDER BY module ASC";
	$data = $db->query($sql);
	
	$help_array = array();
	forEach ($data as $row) {
		if (AccessLevel::check_access($sender, $row->admin)) {
			$help_array []= $row;
		}
	}

	if (count($help_array) == 0) {
		$msg = "<orange>No Helpfiles found.<end>";
	} else {
		$blob = "<header> :::::: Help Files for Budabot {$version} :::::: <end>\n\n";
		$current_module = '';
		forEach ($help_array as $row) {
			if ($current_module != $row->module) {
				$blob .= "\n<pagebreak><highlight><u>{$row->module}:</u><end>\n";
				$current_module = $row->module;
			}
			
			$blob .= "  *{$row->name}: {$row->description} <a href='chatcmd:///tell <myname> help {$row->name}'>Click here</a>\n";
		}
		
		$msg = Text::make_blob("Help (main)", $blob);
	}

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^help (.+)$/i", $message, $arr)) {
	$helpcmd = ucfirst($arr[1]);
	$blob = $chatBot->getInstance('help')->find($helpcmd, $sender);
	if ($blob !== false) {
		$msg = Text::make_blob("Help ($helpcmd)", $blob);
		$chatBot->send($msg, $sendto);
	} else {
		$chatBot->send("No help found on this topic.", $sendto);
	}
}

?>