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

if(preg_match("/^scout (.*)$/i", $message)) {

	$msg = "";

  	if (preg_match("/^scout ([A-Za-z0-9]+) ([0-9]+) ([0-9]{1,2}:[0-9]{2}:[0-9]{2}) ([0-9]+) ([A-Za-z]+) (.*)$/i", $message, $arr)) {

	  	$zone = strtoupper($arr[1]);
	  	$base_number = $arr[2];
	  	$closing_time = $arr[3];
	  	$ct_ql = $arr[4];
	  	$side = ucfirst(strtolower($arr[5]));
	  	$org = ucfirst($arr[6]);
	  	
	  	$closing_time_array = explode(':', $closing_time);
		$closing_time_seconds = $closing_time_array[0] * 3600 + $closing_time_array[1] * 60 + $closing_time_array[2];
	  	
	  	// if side isn't omni, neutral or clan
	  	if ($side != 'Omni' && $side != 'Neutral' && $side != 'Clan') {
			$msg = "Valid values for side are: 'Omni', 'Neutral', and 'Clan'.";
	  	} else {

		  	$db->query("SELECT * FROM tower_watch WHERE zone = '$zone' AND base_number = $base_number");
		  	$result = $db->fObject("all");
		  	if ($db->numRows()) {
			  	
			  	$db->query("UPDATE tower_watch SET close_time = $closing_time_seconds, ct_ql = $ct_ql, side = '$side', org = '$org' WHERE zone = '$zone' AND base_number = $base_number");
			  	$msg = "Tower site has been updated successfully.";
		  	
			} else {
			
				$db->query("INSERT INTO tower_watch (zone, base_number, close_time, ct_ql, side, org) VALUES('$zone', $base_number, $closing_time_seconds, $ct_ql, '$side', '$org')");
			  	$msg = "Tower site has been added successfully.";
  			}
  		}
	
  	} else {
		
	  	$msg = "Usage: <symbol>scout &lt;zone&gt; &lt;base number&gt; &lt;closing time&gt; &lt;CT QL&gt; &lt;Omni|Clan|Neutral&gt; &lt;org name&gt;";
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
