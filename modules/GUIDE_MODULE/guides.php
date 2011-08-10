<?php

$path = getcwd() . "/modules/GUIDE_MODULE/guides/";
$fileExt = ".txt";
$msg = "";

if ($message == "guides") {
	$message = "guides guides";
}
	
// if they want the list of topics
if (preg_match("/^guides list$/i", $message)) {
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

		sort($topicList);

		$linkContents = '';
		forEach ($topicList as $topic) {
			$linkContents .= Text::make_link($topic, "/tell <myname> <symbol>guides $topic", 'chatcmd') . "\n";  
		}
		
		if ($linkContents) {
			$msg = Text::make_blob('Topics (' . count($topicList) . ')', $linkContents);
		} else {
			$msg = "No topics available.";   
		}
	} else {
		$msg = "Error reading topics.";	
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^guides ([a-z0-9_-]+)$/i", $message, $arr)) {
	// if they want a certain topic
	// get the filename and read in the file
	$fileName = strtolower($arr[1]);
	$info = getTopicContents($path, $fileName, $fileExt);
	
	// make sure the $ql is an integer between 1 and 300
	if (!$info) {
		$msg = "No info for $fileName could be found";
	} else {	
		$msg = Text::make_blob(ucfirst($fileName), $info);
	}
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
