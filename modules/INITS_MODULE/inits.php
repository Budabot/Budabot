<?php

/// inits <a href="itemref://280727/280727/300">Sloth of the Xan</a>
if(preg_match('/^inits \<a href\=\"itemref\:\/\/([0-9]+)\/([0-9]+)\/([0-9]+)\"\>/i', $message, $arr))
{
	$url = "http://inits.xyphos.com/?";
	$url .= "lowid={$arr[1]}&";
	$url .= "highid={$arr[2]}&";
	$url .= "ql={$arr[3]}&";
	$url .= "output=aoml";

        $ctx - stream_context_create( array( 'http' => array( 'timeout' => 60 ) ) );

	$msg = "Calculating Inits... Please wait.";
	if($type == "msg")
		bot::send($msg, $sender);
	elseif($type == "priv")
		bot::send($msg);
	elseif($type == "guild")
		bot::send($msg, "guild");

	$msg = file_get_contents($url, 0, $ctx);
	if(empty($msg)) { $msg = "Unable to query Central Items Database."; }

		
} else {
	$msg = "Syntax Error! Proper Syntax is <highlight>inits [drop weapon in chat]<end>";
	// . <br>" . 	str_replace(array('<', '>'), array('&ltl', '&gt;'), $message);;
}

bot::send($msg, $sendto);

?>