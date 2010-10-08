<?php

$path = getcwd() . "/modules/GUIDEBOT_MODULE/guides/";
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
			$linkContents .= $this->makeLink($topic, "/tell <myname> <symbol>guides $topic", 'chatcmd') . "\n";  
		}
		
		if ($linkContents) {
			$msg = $this->makeLink('Topics (' . count($topicList) . ')', $linkContents, 'blob');
		} else {
			$msg = "No topics available.";   
		}
	} else {
		$msg = "Error reading topics.";	
	}
} else if (preg_match("/^guides (.*)$/i", $message, $arr)) {
	// if they want a certain topic
	// get the filename and read in the file
	$fileName = strtolower($arr[1]);
	$info = getTopicContents($path, $fileName, $fileExt);
	
	// make sure the $ql is an integer between 1 and 300
	if (!$info) {
		$msg = "No info for $fileName could be found";
	} else {	
		$msg = $this->makeLink(ucfirst($fileName), $info, 'blob');
	}
}

$this->send($msg, $sendto);

?>