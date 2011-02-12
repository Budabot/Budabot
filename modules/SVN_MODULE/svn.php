<?php

if (preg_match("/^svn update dryrun/i", $message)) {
	$command = "svn update --dry-run";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "<header> :::::: SVN UPDATE --dry-run output :::::: <end>\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn update --dry-run output', $window);
	
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
} else if (preg_match("/^svn status/i", $message)) {
	$command = "svn status";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN STATUS output :::\n\n";
	$window .= $command . "\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = Text::make_link('svn status output', $window);
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>