<?php

if (preg_match("/^svn changes/i", $message)) {
	$command = "svn merge –-dry-run -r BASE:HEAD .";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN MERGE --dry-run :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn merge –-dry-run output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn update/i", $message)) {
	$command = "svn update --accept " . $this->settings['svnconflict'];
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN UPDATE output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn update output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn info/i", $message)) {
	$command = "svn info";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN INFO output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn info output', $window);
	
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^svn status/i", $message) || preg_match("/^svn status (.*)/i", $message, $arr)) {
	$command = "svn status $arr[1]";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN STATUS $arr[1] output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn status $arr[1] output', $window);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>