<?php
   /*
   ** Module: TOWER_WATCH
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you to keep track of the opentimes of tower sites.
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23-November-2007
   ** Date(last modified): 9-Mar-2010
   ** 
   ** Copyright (C) 2008 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** This module and all it's files and contents are licensed
   ** under the GNU General Public License.  You may distribute
   ** and modify this module and it's contents freely.
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

if(preg_match("/^remscout( (.*))?$/i", $message)) {
	
	$msg = "";

  	if (preg_match("/^remscout ([A-Za-z0-9]+) ([0-9]+)/i", $message, $arr)) {

	  	$zone = strtoupper($arr[1]);
	  	$base_number = $arr[2];

	  	$numRows = $db->exec("DELETE FROM tower_watch WHERE zone = '$zone' AND base_number = $base_number");
		if ($numRows) {
		  	$msg = "Tower site has been deleted successfully.";
	  	} else {
		  	$msg = "Tower site does not exist in watch list.";
  		}
	
  	} else {
		$msg = "Usage: <symbol>remscout &lt;zone&gt; &lt;base number&gt;";
	}

    if($type == "msg") {
        bot::send($msg, $sender);
    } elseif($type == "priv") {
       	bot::send($msg);
	} elseif($type == "guild") {
       	bot::send($msg, "guild");
   	}
}
?>
