<?php

if (preg_match("/^svn dry$/i", $message)) {
	$command = "svn merge –-dry-run -r BASE:HEAD . 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN MERGE --dry-run :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn merge –-dry-run output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn update$/i", $message)) {
	$command = "svn update --accept " . Setting::get('svnconflict') . " 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN UPDATE output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn update output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn info$/i", $message)) {
	$command = "svn info 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN INFO output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn info output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn status$/i", $message) || preg_match("/^svn status (.*)$/i", $message, $arr)) {
	$command = "svn status $arr[1] 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN STATUS $arr[1] output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_blob("svn status $arr[1] output", $window);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>