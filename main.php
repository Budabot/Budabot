<?php
   /*
   ** Author: Sebuda/Derroylo (both RK2) + Linux compatibility Changes from Dak (RK2)
   ** Description: Creates the setup Procedure, Loads core classes and creates the bot mainloop.
   ** Version: 0.6
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 01.10.2005	
   ** Date(last modified): 12.01.2007
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann and J. Gracik
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */  

$version = "0.6.6";

echo "\n\n\n\n\n";
echo "		**************************************************\n";
echo "		****         Budabot Version: $version           ****\n";
echo "		****    written by Sebuda & Derroylo(RK2)     ****\n";
echo "		****                Project Site:             ****\n";
echo "		****    http://code.google.com/p/budabot2/    ****\n";
echo "		****               Support Forum:             ****\n";
echo "		****          http://www.budabot.com/         ****\n";
echo "		**************************************************\n";
echo "\n";

date_default_timezone_set("UTC");

if(isWindows()) {
    // Load Extention 
    dl("php_sockets.dll");
    dl("php_pdo_sqlite.dll");
    dl("php_pdo_mysql.dll");
} else {
    /*
    * Load Extentions, if not already loaded.
    *
    * Note: These are normally present in a
    * modern Linux system. This is a safeguard.
    */
    if(!extension_loaded('pdo_sqlite')) {
        @dl('pdo_sqlite.so');
    }
    if(!extension_loaded('pdo_mysql')) {
        @dl('pdo_mysql.so');
    }
    
}

//Load Required Files
$config_file = $argv[1];
require_once $config_file;
require_once "./core/aochat.php";
require_once "./core/chatbot.php";
require_once "./core/sql.php";
require_once "./core/xml.php";

//Set Error Level
error_reporting(E_ERROR | E_PARSE);
//error_reporting(-1);

//Show setup dialog
if(!file_exists("delete me for new setup"))
	include("./core/SETUP/setup.php");

//Bring the ignore list to a bot readable format
$ignore = explode(";", $settings["Ignore"]);
unset($settings["Ignore"]);
foreach($ignore as $bot){
	$bot = ucfirst(strtolower($bot));
	$settings["Ignore"][$bot] = true;
}
unset($ignore);


//Remove the account infos from the global var
$login = $vars['login'];
$password = $vars['password'];
unset($vars['login']);
unset($vars['password']);

//////////////////////////////////////////////////////////////
// Create new objects
	global $db;
	$db = new db($settings["DB Type"], $settings["DB Name"], $settings["DB Host"], $settings["DB username"], $settings["DB password"]);
	if($db->errorCode != 0) {
	  	echo "Error in creating Database Object\n";
	  	echo "ErrorMsg: $db->errorInfo";
	  	sleep(5);
	  	die();
	}
	
	$chatBot = new bot($vars, $settings);
	if(!$chatBot)
		die("No Chatbot.....");

/////////////////////////////////////////////
// log on aoChat, msnChat                  //
	$chatBot->connectAO($login, $password);//		
/////////////////////////////////////////////

//Clear the login and the password	
unset($login);
unset($password);

//Clear database settings
unset($settings["DB Type"]);
unset($settings["DB Name"]);
unset($settings["DB Host"]);
unset($settings["DB username"]);
unset($settings["DB password"]);

// make sure logging directory exists
mkdir("./logs/{$vars['name']}.{$vars['dimension']}");

// Call Main Loop
main(true, $chatBot);
/*
** Name: main
** Main Loop
** Inputs: (bool)$forever
** Outputs: None
*/	function main($forever = true,&$chatBot){
		$start = time();
		
		// Create infinite loop
		while($forever==true){					
			$chatBot->ping();
			$chatBot->crons();
			if($exec_connected_events == false && ((time() - $start) > 5))	{
			  	$chatBot->connectedEvents();
			  	$exec_connected_events = true;
			}
		}	
	}	
/*
** Name: callback
** Function called by Aochat each time a incoming packet is received.
** Inputs: (int)$type, (array)$arguments, (object)&$incBot
** Outputs: None
*/	function callback($type, $args){
		global $chatBot;
		$chatBot->processCallback($type, $args);	
	}// End function
  
  
 /*===============================
** Name: log
** Record incoming info into the chatbot's log.
*/	function newLine($channel, $sender, $message, $target){
		global $vars;

		if ($channel == "") {
			return;
		}
			
		if ($sender == "") {
			return;
		}
		
		if ($channel == "Buddy") {
			$line = "[".date("Ymd H:i", time())."] [$channel] $sender $message";
		} else {
			$line = "[".date("Ymd H:i", time())."] [$channel] $sender: $message";
		}

        $line = preg_replace("/<font(.+)>/U", "", $line);
        $line = preg_replace("/<\/font>/U", "", $line);
        $line = preg_replace("/<a(\\s+)href=\"(.+)\">/sU", "[link]", $line);
        $line = preg_replace("/<a(\\s+)href='(.+)'>/sU", "[link]", $line);
        $line = preg_replace("/<\/a>/U", "[/link]", $line);
        
		echo "$line\n";
		
		if ($target == 1 || $channel == "logOn" || $channel == "logOff" || $channel == "Buddy")
			return;
		
		if ($channel == "Inc. Msg." || $channel == "Out. Msg.")
			$channel = "Tells";

		$today =  date("Ym");

        /*
        * Open and append to log-file. Complain on failure.
        */
        $filename = "./logs/{$vars['name']}.{$vars['dimension']}/$today.$channel.txt";
        if (($fp = fopen($filename, "a")) === FALSE) {
            echo "    *** Failed to open log-file $filename for writing ***\n";
        } else {
            fwrite($fp, $line . PHP_EOL);
            fclose($fp);
        }
        
	}
    
    /**
    * isWindows is a little utility function to check
    * whether the bot is running Windows or something
    * else: returns true if under Windows, else false
    */
    function isWindows() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }
	
	/**
	 * Takes two version numbers.  Returns 1 if the first is greater than the second.
	 * Returns -1 if the second is greater than the first.  Returns 0 if they are equal.
	 */
	function compareVersionNumbers($ver1, $ver2) {
		$ver1Array = explode('.', $ver1);
		$ver2Array = explode('.', $ver2);
		
		for ($i = 0; $i < count($ver1Array) && $i < count($ver2Array); $i++) {
			if ($ver1Array[$i] > $ver2Array[$i]) {
				return 1;
			} else if ($ver1Array[$i] < $ver2Array[$i]) {
				return -1;
			}
		}
		
		if (count($ver1Array) > count($ver2Array)) {
			return 1;
		} else if (count($ver1Array) < count($ver2Array)) {
			return -1;
		} else {
			return 0;
		}
	}

	// taken from http://www.php.net/manual/en/function.date-diff.php
	function date_difference($sdate, $edate) {
		$time = $edate - $sdate;
		if ($time>=0 && $time<=59) {
			// Seconds
			$timeshift = $time.' seconds';

		} else if ($time>=60 && $time<=3599) {
			// Minutes + Seconds
			$pmin = ($edate - $sdate) / 60;
			$premin = explode('.', $pmin);
			
			$presec = $pmin-$premin[0];
			$sec = $presec*60;
			
			$timeshift = $premin[0].' min '.round($sec,0).' sec';

		} else if ($time>=3600 && $time<=86399) {
			// Hours + Minutes
			$phour = ($edate - $sdate) / 3600;
			$prehour = explode('.',$phour);
			
			$premin = $phour-$prehour[0];
			$min = explode('.',$premin*60);
			
			$presec = '0.'.$min[1];
			$sec = $presec*60;

			$timeshift = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec';

		} else if ($time>=86400) {
			// Days + Hours + Minutes
			$pday = ($edate - $sdate) / 86400;
			$preday = explode('.',$pday);

			$phour = $pday-$preday[0];
			$prehour = explode('.',$phour*24); 

			$premin = ($phour*24)-$prehour[0];
			$min = explode('.',$premin*60);
			
			$presec = '0.'.$min[1];
			$sec = $presec*60;
			
			$timeshift = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec';

		}
		return $timeshift;
	}
	
	function bytesConvert($bytes) {
		$ext = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$unitCount = 0;
		for(; $bytes > 1024; $unitCount++) {
			$bytes /= 1024;
		}
		return round($bytes, 2) ." ". $ext[$unitCount];
	}
?>
