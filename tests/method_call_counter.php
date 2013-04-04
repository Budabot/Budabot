<?php

/*

Method call counter

Iterates through all php-files in modules-directory and
looks for method calls. For each method call its call count
is recorded and finally printed.

The recorded call counts are ordered in descenting order.

This script should be useful for figuring out which methods
are most widely called throughtout the codebase and which
should the documented the best.

*/

printCallCounts(getCallCounts());

function printCallCounts($calls) {
	foreach ($calls as $name => $count) {
		print "METHOD CALL COUNT ". str_pad($count, 3) .": $name()\n";
	}
}

function getCallCounts() {
	$calls = array();
	//$calls = collectCalls($calls, '../core');
	$calls = collectCalls($calls, '../modules');
	$calls = sortCallsByCount($calls);
	return $calls;	
}

function collectCalls($calls, $path) {
	$dir_iterator = new RecursiveDirectoryIterator($path);
	$iterator = new RecursiveIteratorIterator($dir_iterator);
	foreach ($iterator as $file) {
		if (preg_match("~[.]php$~i", $file)) {
			$contents = file_get_contents($file);
			if (preg_match_all("~([a-z0-9_]*)->([a-z0-9_]+)\\(~i", $contents, $matches, PREG_SET_ORDER)) {
				
				foreach ($matches as $match) {
					$varName = $match[1];
					$methodName = $match[2];
					if ($varName != 'this' && $varName != 'that') {
						isset($calls[$methodName])? $calls[$methodName]++:
							$calls[$methodName] = 1;
					}
				}
			}
		}
	}
	return $calls;
}

function sortCallsByCount($calls) {
	uasort($calls, function($callCount1, $callCount2) {
	    if ($callCount1 == $callCount2) {
	        return 0;
	    }
	    return ($callCount1 > $callCount2) ? -1 : 1;
	});
	return $calls;
}

