<?php

if (!function_exists('padRow')) {
	function padRow($str, $size) {
		return str_pad($str, $size - strlen($str), ".");
	}
}

if (preg_match("/^aliaslist$/i", $message)) {
	$paddingSize = 30;
	$blob = "<header> :::::: Alias List :::::: <end>\n\n";
	$a = padRow("Alias", $paddingSize);
	$blob .= "<header>{$a}Command<end>\n\n";
	$count = 0;
	forEach (CommandAlias::getAllAliases() as $alias) {
		if ($count++ % 2 == 0) {
			$color  = "white";
		} else {
			$color  = "highlight";
		}
		$a = padRow($alias->alias, $paddingSize);
		$blob .= "<{$color}>{$a}{$alias->cmd}<end>\n";
	}
	
	$msg = Text::make_blob("Alias List", $blob);
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>