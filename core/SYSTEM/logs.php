<?php

if (preg_match("/^logs$/i", $message)) {
	if ($handle = opendir(LegacyLogger::get_logging_directory())) {
		$blob = '';
		while (false !== ($file = readdir($handle))) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			
			$file_link = Text::make_chatcmd($file, "/tell <myname> logs $file");
			$errorLink = Text::make_chatcmd("ERROR", "/tell <myname> logs $file ERROR");
			$chatLink = Text::make_chatcmd("CHAT", "/tell <myname> logs $file CHAT");
			$blob .= "$file_link [$errorLink] [$chatLink] \n";
		}
		closedir($handle);
		
		$msg = Text::make_blob('Log Files', $blob);
	} else {
		$msg = "Could not open log directory: '" . LegacyLogger::get_logging_directory() . "'";
	}
	$sendto->reply($msg);
} else if (preg_match("/^logs ([a-zA-Z0-9-_\\.]+)$/i", $message, $arr) || preg_match("/^logs ([a-zA-Z0-9-_\\.]+) (.+)$/i", $message, $arr)) {
	$filename = LegacyLogger::get_logging_directory() . "/" . $arr[1];
	$readsize = $setting->get('max_blob_size') - 500;
	
	try {
		$file = new ReverseFileReader($filename);
		$contents = '';
		while (!$file->sof()) {
			$line = $file->getLine();
			
			// if user entered search criteria, filter by that
			if (isset($arr[2]) && !preg_match("/{$arr[2]}/i", $line)) {
				continue;
			}
			
			if (strlen($contents . $line) > $readsize) {
				break;
			}
			$contents .= $line;
		}
		$file->close();
		if (empty($contents)) {
			$msg = "File is empty or nothing matched your search criteria.";
		} else {
			if (isset($arr[2])) {
				$contents = "Search: $arr[2]\n\n" . $contents;
			}
			$msg = Text::make_blob($arr[1], $contents);
		}
	} catch (Exception $e) {
		$msg = "Error: " . $e->getMessage();
	}
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
