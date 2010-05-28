<?php

if (preg_match("/^svn update/i", $message)) {
	$command = "svn update --non-interactive";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN UPDATE output :::\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = bot::makeLink('svn update output', $window);
	
	bot::send($msg, $sendto);
} else if (preg_match("/^svn info/i", $message)) {
	$command = "svn info";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$window = "::: SVN INFO output :::\n\n";
	forEach ($output as $line) {
		$window .= $line . "\n";
	}
	
	$msg = bot::makeLink('svn info output', $window);
	
	bot::send($msg, $sendto);
}

?>