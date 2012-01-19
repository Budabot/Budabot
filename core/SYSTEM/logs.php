<?php

if (preg_match("/^logs$/i", $message)) {
	if ($handle = opendir(LegacyLogger::get_logging_directory())) {
		$blob = '';
		while (false !== ($file = readdir($handle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			
			$file_link = Text::make_chatcmd($file, "/tell <myname> logs $file");
			$blob .= $file_link . "\n";
		}
		closedir($handle);
		
		$msg = Text::make_blob('Log Files', $blob);
	} else {
		$msg = "Could not open log directory: '" . LegacyLogger::get_logging_directory() . "'";
	}
	$sendto->reply($msg);
} else if (preg_match("/^logs ([a-zA-Z0-9_\\.]+)$/i", $message, $arr)) {
	$filename = LegacyLogger::get_logging_directory() . "/" . $arr[1];
	$readsize = $setting->get('max_blob_size') - 500;
	
	try {
		$file = new ReverseFileReader($filename);
		$contents = '';
		while (!$file->sof()) {
			$line = $file->getLine();
			if (strlen($contents . $line) > $readsize) {
				break;
			}
			$contents .= $line;
		}
		$msg = Text::make_blob($arr[1], $contents);
	} catch (Exception $e) {
		$msg = "Error: " . $e->getMessage();
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
