<?php

$svnpath = $setting->get('svnpath');
if (preg_match("/^svn dry$/i", $message)) {
	$command = "$svnpath merge –-dry-run -r BASE:HEAD . 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = "<header> :::::: SVN MERGE --dry-run :::::: <end>\n\n";
	$blob .= $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn merge –-dry-run output', $blob);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn update$/i", $message)) {
	$command = "$svnpath update --accept " . $setting->get('svnconflict') . " 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = "<header> :::::: SVN UPDATE output :::::: <end>\n\n";
	$blob .= $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn update output', $blob);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn info$/i", $message)) {
	$command = "$svnpath info 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = "<header> :::::: SVN INFO output :::::: <end>\n\n";
	$blob .= $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn info output', $blob);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn status$/i", $message) || preg_match("/^svn status (.*)$/i", $message, $arr)) {
	$command = "$svnpath status $arr[1] 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = "<header> :::::: SVN STATUS $arr[1] output :::::: <end>\n\n";
	$blob .= $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob("svn status $arr[1] output", $blob);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>