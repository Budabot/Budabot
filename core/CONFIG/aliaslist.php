<?php

if (!function_exists('padRow')) {
	function padRow($str, $size) {
		return str_pad($str, $size - strlen($str), ".");
	}
}

if (preg_match("/^aliaslist$/i", $message)) {
	$paddingSize = 30;

	$a = padRow("Alias", $paddingSize);
	$blob = "<header>{$a}Command<end>\n\n";
	$count = 0;
	forEach (Registry::getInstance('commandAlias')->getEnabledAliases() as $alias) {
		if ($count++ % 2 == 0) {
			$color = "white";
		} else {
			$color = "highlight";
		}
		$removeLink = Text::make_chatcmd('Remove', "/tell <myname> remalias {$alias->alias}");
		$a = padRow($alias->alias, $paddingSize);
		$blob .= "<{$color}>{$a}{$alias->cmd}<end> $removeLink\n";
	}
	
	$msg = Text::make_blob('Alias List', $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>