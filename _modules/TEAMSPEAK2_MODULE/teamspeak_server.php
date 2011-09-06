<?php

$msg = "";
if (preg_match("/^ts$/i", $message)) {
	//TS Server Info
    $ip 			= Setting::get("ts_ip");
    $queryport 		= Setting::get("ts_queryport");
    $serverport 	= Setting::get("ts_serverport");
    $servername 	= Setting::get("ts_servername");

	//If IP isn't set show error msg
	if ($ip == "Not set yet.") {
	  	$msg = "You need to configure your TS Server before you can use this!";
	    $chatBot->send($msg, $sendto);
		return;
	}

  	//Try to connect to TS Server (with timeout)
    $timeToEnd = (time() + 5);
	$connection = fsockopen($ip, $queryport, $errno, $errstr, 30);

	if ($connection) {
		$infolines = "";
    	fputs($connection,"sel ".$serverport."\n");
        fputs($connection,"si\n");
        fputs($connection,"quit\n");

        while (!feof($connection)){
	        $infolines .= fgets($connection, 1024);
        }
		fclose($connection);

        $infolines = str_replace("[TS]","",$infolines);
        $infolines = str_replace("OK","",$infolines);
        $infolines = trim($infolines);

        //Connected Users
		$indexof = strpos($infolines, "server_currentusers=") + strlen("server_currentusers=");
        $user = substr($infolines, $indexof, strlen($infolines));
		$indexof = strpos($user, "server_currentchannels=") + strlen("server_currentchannels=");
        $user = substr($user, 0, $indexof - strlen("server_currentchannels="));
		$user = trim($user);

        //Server Max Users
		$indexof = strpos($infolines, "server_maxusers=") + strlen("server_maxusers=");
        $maxusers = substr($infolines, $indexof, strlen($infolines));
        
		$indexof = strpos($maxusers, "server_allow_codec_celp51=") + strlen("server_allow_codec_celp51=");
        $maxusers = substr($maxusers, 0, $indexof - strlen("server_allow_codec_celp51="));
   		$maxusers = trim($maxusers);

        //Uptime of the Server
		$indexof = strpos($infolines, "server_uptime=") + strlen("server_uptime=");
   		$uptime = substr($infolines, $indexof, strlen($infolines));
		$indexof = strpos($uptime, "server") + strlen("server_currrentusers=");
		$uptime = substr($uptime, 0, $indexof - strlen("server_currrentusers="));
   		$uptime = trim($uptime);
	    $hours = floor($uptime/3600);
       	$minutes = floor(($uptime%3600)/60);
       	$seconds = floor(($uptime%3600)%60);
        if ($hours>0) {
			$uptime = $hours."h ".$minutes."m ".$seconds."s";
		} else if ($minutes > 0) {
			$uptime = $minutes."m ".$seconds."s";
		} else {
			$uptime = $seconds."s";
		}
        
        //Serverplatform
		$indexof = strpos($infolines, "server_platform=") + strlen("server_platform=");
		$platform = substr($infolines, $indexof, strlen($infolines));
		$indexof = strpos($platform, "server_welcomemessage=") + strlen("server_welcomemessage=");
		$platform = substr($platform, 0, $indexof - strlen("server_welcomemessage="));
   		$platform = trim($platform);
        
        //Number of channels
        $indexof = strpos($infolines, "server_currentchannels=") + strlen("server_currentchannels=");
   		$channels = substr($infolines, $indexof, strlen($infolines));
        $indexof = strpos($channels, "server_bwinlastsec=") + strlen("server_bwinlastsec=");
		$channels = substr($channels, 0, $indexof - strlen("server_bwinlastsec="));
   		$channels = trim($channels);
        
        $link  = "<header>::::: Teamspeak Server Info :::::<end>\n\n";
		$link .= "<u>Infos how to connect to the server</u>\n";
		$link .= "Get the TS Client from 'http://www.goteamspeak.com'\n\n";
		$link .= "Fillout the server Info with:\n";
		$link .= "Server Adress: <highlight>$ip:$serverport<end>\n\n";
		
		$link .= "<u>General Info about the TS Server</u>\n";
		$link .= "Server Name: <highlight>$servername<end>\n";
		$link .= "Server Platform: <highlight>$platform<end>\n";
		$link .= "Server Uptime: <highlight>".$uptime."<end>\n";
		$link .= "Server Maximum: <highlight>$maxusers<end>\n";		
		$link .= "Players Currently Connected: <highlight>$user<end>\n";
		$link .= "Server Channels: <highlight>$channels<end>\n\n";
		
		//Get Players
		$player_array 	= array();
		$innerArray = array();
		$out		= "";
		$j			= 0; 
		$k			= 0;
		
		$connection = fsockopen($ip, $queryport, $errno, $errstr, 30);
		fputs($connection, "pl ".$serverport."\n");		
		fputs($connection, "quit\n");
		while (!feof($connection)) {
			$out .= fgets($connection, 1024);
		}

		$out   = str_replace("[TS]", "", $out);
		$out   = str_replace("loginname", "loginname\t", $out);		
		$data 	= explode("\t", $out);
		$num 	= count($data);				
			
		for ($i = 0; $i < count($data); $i++) {
			$innerArray[$j] = $data[$i];
			if ($j >= 15) {
				$player_array[$k] = $innerArray;
				$j = 0;
				$k = $k + 1;
			} else {
				$j++;
			}
		}			
		fclose($connection);

		//Get Channels
		$cArray 	= array();
		$innerArray = array();	
		$out		= "";
		$j			= 0; 
		$k			= 0;
		
		$connection = fsockopen($ip, $queryport, $errno, $errstr, 30);
		fputs($connection, "cl ".$serverport."\n");		
		fputs($connection, "quit\n");
		while (!feof($connection)) {
			$out .= fgets($connection, 1024);
		}
		$out   = str_replace("[TS]", "", $out);
		$out   = str_replace("\n", "\t", $out);			
		$data 	= explode("\t", $out);
		$num 	= count($data);				
		
		for ($i = 0; $i < count($data); $i++) {
			if ($i >= 10) {
				$innerArray[$j] = $data[$i];
				if ($j>=8) {
					$cArray[$k]=$innerArray;
					$j = 0;
					$k = $k+1;
				} else {
					$j++;
				}
			}			
		}			
        fclose($connection);

		//Give Channels and their users out		
		forEach ($cArray as $channel) {
		  	$channel[5] = str_replace("\"", "", $channel[5]);
		  	$link .= "<u>Channel: {$channel[5]}</u>\n";
		  	$c_id = $channel[0];
			$num_players = 0;
		  	forEach ($player_array as $player) {
			    if ($player[1] == $c_id) {
					$num_players++;
				  	$name = $player[14];
  	                $name = str_replace("\"","",$name);
	                $name = ucfirst($name);
	                $time = $player[8];
	
			       	$hours = floor($time/3600);
			        $minutes = floor(($time%3600)/60);
			        $seconds = floor(($time%3600)%60);
			
			        if ($hours>0) {
						$time = $hours."h ".$minutes."m ".$seconds."s";
					} else if ($minutes>0) {
						$time = $minutes."m ".$seconds."s";
					} else {
						$time = $seconds."s";
					}
	                $link .= "<tab>- <highlight>$name<end>($time)\n";
				}
			}
			if ($num_players == 0) {
				$link .= "<tab>- <highlight>None<end>\n";
			}
		}

		$msg = Text::make_blob("Teamspeak Server Status", $link);
	} else {
		$msg = "Couldn't connect to Teamspeak Server. Try again later.";
	}
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>