<?php

$svnpath = $setting->get('svnpath');
if (preg_match("/^svn dry$/i", $message)) {
	$command = "$svnpath merge –-dry-run -r BASE:HEAD . 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn merge –-dry-run output', $blob);
	
	$sendto->reply($msg);
} else if (preg_match("/^svn update$/i", $message)) {
	$command = "$svnpath info 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	$blob .= "\n";
	
	$command = "$svnpath update --accept " . $setting->get('svnconflict') . " 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob .= $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn update output', $blob);
	
	$sendto->reply($msg);
} else if (preg_match("/^svn info$/i", $message)) {
	$command = "$svnpath info 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);

	$blob = $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob('svn info output', $blob);
	
	$sendto->reply($msg);
} else if (preg_match("/^svn status$/i", $message) || preg_match("/^svn status (.*)$/i", $message, $arr)) {
	$command = "$svnpath status $arr[1] 2>&1";
	$output = array();
	$return_var = '';
	exec($command, $output, $return_var);
	
	$blob = $command . "\n\n";
	forEach ($output as $line) {
		$blob .= $line . "\n";
	}
	
	$msg = Text::make_blob("svn status $arr[1] output", $blob);
	
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>