<?php

if (preg_match("/^system$/i", $message, $arr)) {
	global $version;

	$blob = "<header>::::: System Info :::::<end>\n\n";
	$blob .= "Budabot $version\n\n";
	
	$blob .= "SuperAdmin: '{$this->vars['SuperAdmin']}'\n";
	$blob .= "Guild: '{$this->vars['my guild']}' (" . $this->vars['my guild id'] . ")\n\n";
	
	$blob .= "Current Memory Usage: " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "Current Memory Usage (Real): " . Util::bytes_convert(memory_get_usage(1)) . "\n";
	$blob .= "Peak Memory Usage: " . Util::bytes_convert(memory_get_usage()) . "\n";
	$blob .= "Peak Memory Usage (Real): " . Util::bytes_convert(memory_get_peak_usage(1)) . "\n\n";
	
	$date_string = Util::unixtime_to_readable(time() - $this->vars['startup']);
	$blob .= "The bot has been online for $date_string.\n\n";
	
	$blob .= "Number of active commands: ?\n";
	$blob .= "Number of active events: " . count($this->events) . "\n";
	$blob .= "Number of characters on the friendlist: " . count($this->buddyList) . "\n";
	
	$msg = Text::make_link('System Info', $blob, 'blob');
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>