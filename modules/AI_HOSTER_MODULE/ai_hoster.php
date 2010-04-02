<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Rolling for the hoster of an alien city raid
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 10.08.2006
   ** Date(last modified): 23.11.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

global $aihoster;
if(eregi("^crhoster clear$", $message)) {
  	$aihoster = "";
  	$msg = "City Raid Hosterlist has been cleared by <highlight>$sender<end>.";
  	bot::send($msg);	
} elseif(eregi("^crhoster start$", $message, $arr)) {
	//Check if a list already created
  	if($aihoster["created"] == true) {
	    $msg = "A City Raid Hosterlist has been already created.";
	    bot::send($msg);
	    return;
	}

	//Create the list
	$aihoster["created"] = true;

	//Send info
	bot::send("The City Raid Hosterlist has been created.");
	bot::send("To add use <highlight><symbol>crhadd<end>, to remove yourself do <highlight><symbol>crhrem<end>");
} elseif(eregi("^crhoster list$", $message)) {
	//Check if a list already created
  	if($aihoster["created"] != true) {
	    $msg = "No City Raid Hosterlist has been created.";
	    bot::send($msg);
	    return;
	}

	$list = "Players that have been added to the City Raid Hosterlist: ";
	foreach($aihoster["list"] as $key => $value)
		$list .= "[<highlight>$key<end>] ";
	
 	if(count($aihoster["list"]) == 0) {
		$list .= "<highlight>No one added yet.<end>";
	}

	//Send info
	bot::send($list);
	bot::send("To add use <highlight><symbol>crhadd<end>, to remove yourself do <highlight><symbol>crhrem<end>");
} elseif(eregi("^crhoster roll$", $message)) {
	//Check if a loot list exits
  	if(!is_array($aihoster)) {
	    $msg = "You need to start a City Raidhoster roll first!";
	    bot::send($msg);
	    return;
	}
  	
  	if(count($aihoster["list"]) == 0) {
	    $msg = "No one added to the City Raidhoster roll!";
	    bot::send($msg);	    
	    return;
	}

  	if(count($aihoster["list"]) == 1) {
		$winner = $aihoster["list"];
		$winner = key($winner);
	    $msg = "Hoster of this raid is <highlight>$winner<end>.";
	    bot::send($msg);
	    $db->query("UPDATE aihosters_<myname> SET `num` = 0 WHERE `name` = '$winner'");
	    $aihoster = "";
	    return;
	}

	$query = "(";
	$first = true;
	foreach($aihoster["list"] as $key => $value) {
		if($first)
			$query .= "`name` = '$key'";
		else
			$query .= "OR `name` = '$key'";
		$first = false;
	}
	
	$query .= ")";
  	$db->query("SELECT * FROM aihosters_<myname> WHERE $query AND `num` > 0 ORDER BY `num` DESC");
  	if($db->numrows() == 0) {
	    $winner = array_rand($aihoster["list"], 1);
	    bot::send("Hoster of this raid is <highlight>$winner<end>.");
		unset($aihoster["list"][$winner]);
		foreach($aihoster["list"] as $key => $value) {
			$db->query("SELECT * FROM aihosters_<myname> WHERE name = '$key'");
			if($db->numrows() == 0)
				$db->query("INSERT INTO aihosters_<myname> VALUES ('$key', 1);");
			else
				$db->query("UPDATE aihosters_<myname> SET `num` = 1 WHERE `name` = '$key';");
		}		
	    $aihoster = "";
	    return;
	}

	$list = "";
	while($row = $db->fObject()) {
	  	$list[$row->name] = $row->num;
	}

	//Check if someone lost the roll more than 2times as the others
	arsort($list);
	$tmp = $list;
	$first = array_shift($tmp);
	$second = array_shift($tmp);
	if(($first - $second) >= 2) {
	  	$winner = key($list);
	  	bot::send("Hoster of this raid is <highlight>$winner<end>.");
		unset($aihoster["list"][$winner]);
		$db->query("UPDATE aihosters_<myname> SET `num` = 0 WHERE `name` = '$winner';");
		foreach($aihoster["list"] as $key => $value) {
			$db->query("SELECT * FROM aihosters_<myname> WHERE `name` = '$key'");
			if($db->numrows() == 0)
				$db->query("INSERT INTO aihosters_<myname> VALUES ('$key', 1);");
			else {
	  		  	$row = $db->fObject();
			  	$num = $row->num + 1;
				$db->query("UPDATE aihosters_<myname> SET `num` = $num WHERE `name` = '$key';");
			}
		}		
	    $aihoster = "";
	    return;
	}

    $winner = array_rand($list, 1);
    bot::send("Hoster of this raid is <highlight>$winner<end>.");
	unset($list[$winner]);
	unset($aihoster["list"][$winner]);
    $db->query("UPDATE aihosters_<myname> SET `num` = 0 WHERE `name` = '$winner';");
	foreach($aihoster["list"] as $key => $value) {
		$db->query("SELECT * FROM aihosters_<myname> WHERE `name` = '$key'");
		if($db->numrows() == 0) {
			$db->query("INSERT INTO aihosters_<myname> VALUES ('$key', 1);");
		} else {
		  	$row = $db->fObject();
		  	$num = $row->num + 1;
			$db->query("UPDATE aihosters_<myname> SET `num` = $num WHERE `name` = '$key';");
		}
	}
	$aihoster = "";
} else
	$syntax_error = true;
?>