<?php
   /*
   ** Author: Tyrence (RK2)
   ** Description: Statistics for implants at given ql
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13-OCT-2007
   ** Date(last modified): 13-OCT-2007
   ** 
   ** Copyright (C) 2007 Jason Wheeler
   */

$path = getcwd() . "/modules/INFO_MODULE/info/";
$fileExt = ".txt";
$msg = "";

// if they want the list of topics
if (preg_match("/^info$/i", $message)) {
	if ($handle = opendir($path)) {
		$topicList = array();

		/* This is the correct way to loop over the directory. */
		while (false !== ($fileName = readdir($handle))) {
			// if file has the correct extension, it's a topic file
			if (strpos($fileName, $fileExt)) {
				$topicList[] =  str_replace($fileExt, '', $fileName);
			}
		}

		closedir($handle);
		
		global $vars;
		global $settings;
		$linkContents = '';
		forEach ($topicList as $topic) {
			$linkContents .= bot::makeLink($topic, "/tell <myname> " . $settings['symbol'] . "info $topic", 'chatcmd') . "\n";  
		}
		
		if ($linkContents) {
			$msg = bot::makeLink('Topics (' . count($topicList) . ')', count($topicList) . " Topics Available\n==========\n\n$linkContents", "blob");
		} else {
			$msg = "No topics available.";   
		}
	} else {
		$msg = "Error reading topics.";	
	}
} else if (preg_match("/^info ([a-z0-9_-]+)$/i", $message, $arr) || preg_match("/^([a-z0-9_-]+)$/i", $message, $arr)) {
	// if they want a certain topic
	// second form is for the aliases
	// get the filename and read in the file
	$fileName = strtolower($arr[1]);
	$info = getTopicContents($path, $fileName, $fileExt);
	
	if (empty($info)) {
		$msg = "No info for <highlight>$fileName<end> could be found";
	} else {
		$msg = bot::makeLink(ucfirst($fileName), $info);
	}
} else {
	$syntax_error = true;
	return;
}

bot::send($msg, $sendto);

?>
