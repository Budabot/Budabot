<?php

if (preg_match("/^logs$/i", $message)) {
	if ($handle = opendir(Logger::get_logging_directory())) {
		$blob .= "<header> :::::: Log Files :::::: <end>\n\n";
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
		$msg = "Could not open log directory: '" . Logger::get_logging_directory() . "'";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^logs ([a-zA-Z0-9_\\.]+)$/i", $message, $arr)) {
	$filename = Logger::get_logging_directory() . "/" . $arr[1];
	$size = filesize($filename);
	$readsize = Setting::get('max_blob_size') - 500;
	
	if ($fp = fopen($filename, 'r')) {
		fseek($fp, $size - $readsize);
		$contents = fread($fp, $readsize);
		fclose($fp);
		
		$blob = "<header> :::::: {$arr[1]} :::::: <end>\n\n" . $contents;
		$msg = Text::make_blob($arr[1], $blob);
	} else {
		$msg = "Could not open file: '{$filename}'";
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
