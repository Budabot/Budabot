<?php

$accessLevel = Registry::getInstance('accessLevel');
$help = Registry::getInstance('help');

if (preg_match("/^about$/i", $message) || preg_match("/^help about$/i", $message)) {
	global $version;
	$data = file_get_contents("./core/HELP/about.txt");
	$data = str_replace('<version>', $version, $data);
	$msg = Text::make_legacy_blob("About Budabot", $data);
	$sendto->reply($msg);
} else if (preg_match("/^help$/i", $message)) {
	global $version;

	$data = $help->getAllHelpTopics($sender);
	
	if (count($data) == 0) {
		$msg = "<orange>No help files found.<end>";
	} else {
		$blob = '';
		$current_module = '';
		forEach ($data as $row) {
			if ($current_module != $row->module) {
				$blob .= "\n<pagebreak><highlight><u>{$row->module}:</u><end>\n";
				$current_module = $row->module;
			}

			$blob .= "  {$row->name}: {$row->description} <a href='chatcmd:///tell <myname> help {$row->name}'>Click here</a>\n";
		}
		
		$msg = Text::make_blob("Help (main)", $blob, "Help Files for Budabot {$version}");
	}

	$sendto->reply($msg);
} else if (preg_match("/^help (.+)$/i", $message, $arr)) {
	$helpcmd = ucfirst($arr[1]);
	$blob = $help->find($helpcmd, $sender);
	if ($blob !== false) {
		$msg = Text::make_blob("Help ($helpcmd)", $blob);
		$sendto->reply($msg);
	} else {
		$sendto->reply("No help found on this topic.");
	}
}

?>