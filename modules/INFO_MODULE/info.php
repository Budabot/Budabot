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

require_once('info_functions.php');
if(preg_match("/^info(.*)$/i", $message))
{
	$path = getcwd() . "/modules/INFO_MODULE/info/";
	$fileExt = ".txt";
	$msg = "";
	
	// if they want the list of topics
	if(preg_match("/^info$/i", $message))
	{
		if ($handle = opendir($path))
		{
			$topicList = array();

		    /* This is the correct way to loop over the directory. */
		    while (false !== ($fileName = readdir($handle)))
		    {
			    // if file has the correct extension, it's a topic file
		        if (strpos($fileName, $fileExt))
		        {
        			$topicList[] =  str_replace($fileExt, '', $fileName);
		        }
		    }

		    closedir($handle);
		    
		    global $vars;
		    global $settings;
		    $linkContents = '';
		    foreach($topicList as $topic)
		    {
				$linkContents .= $this->makeLink($topic, "/tell " . $vars['name'] . " " . $settings['symbol'] . "info $topic", 'chatcmd') . "\n";  
				//$linkContents .= $this->makeLink($topic, getTopicContents($path, $topic, $fileExt), "blob") . "\n";  
		    }
		    
		    if($linkContents)
		    {
				$msg = $this->makeLink('Topics (' . count($topicList) . ')', count($topicList) . " Topics Available\n==========\n\n$linkContents", "blob");
		    }
		    else
		    {
			 	$msg = "No topics available.";   
		    }
		}
		else
		{
			$msg = "Error reading topics.";	
		}
	}
	// if they want a certain topic
	else if(preg_match("/^info (.*)$/i", $message, $arr))
	{
		// get the filename and read in the file
		$fileName = $arr[1];
		$info = getTopicContents($path, $fileName, $fileExt);
		
		// make sure the $ql is an integer between 1 and 300
		if (!$info)
		{
			$msg = "No info for $fileName could be found";
		}
		else
		{	
			$msg = $this->makeLink($fileName, $info);
		}
	}
	
	$this->send($msg, $sendto);
}

?>
