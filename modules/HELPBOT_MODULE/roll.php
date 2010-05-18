<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Does a random flip or a roll
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 15.01.2006
   ** Date(last modified): 21.11.2006
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

if(preg_match("/^flip$/i", $message)) {
	$db->query("SELECT * FROM roll_<myname> WHERE `type` = 0 AND `name` = '$sender' ORDER BY `time`");
	if($db->numrows() == 0) {
	  	$flip = rand(1, 2);
		$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `result`) VALUES (".time().", '$sender', 0, $flip)");
		$ver_num = $db->lastInsertId();
	  	if($flip == 1)
	  		$msg = "The coin landed <highlight>heads<end>, to verify do /tell <myname> verify $ver_num";
	  	else
		  	$msg = "The coin landed <highlight>tails<end>, to verify do /tell <myname> verify $ver_num";
	} else {
	  	$row = $db->fObject();
	  	if((time() - $row->time) <= 30)
	  		$msg = "You can only flip once every 30 seconds.";
	  	else {
		  	$flip = rand(1,2);
			$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `result`) VALUES (".time().", '$sender', 0, $flip)");
			$ver_num = $db->lastInsertId();
		  	if($flip == 1)
		  		$msg = "The coin landed <highlight>heads<end>, to verify do /tell <myname> verify $ver_num";
		  	else
			  	$msg = "The coin landed <highlight>tails<end>, to verify do /tell <myname> verify $ver_num";
		}		
	}

    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} elseif(preg_match("/^roll ([0-9]+)$/i", $message, $arr)) {
  	if($arr[1] > getrandmax())
		$msg = "Can't use the number you have given me. Maximum is <highlight>".getrandmax()."<end>";
	else {  		
		$db->query("SELECT * FROM roll_<myname> WHERE `type` = 1 AND `name` = '$sender' ORDER BY `time`");
		if($db->numrows() == 0) {
		  	$num = rand(1, $arr[1]);
			$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (".time().", '$sender', 1, 1, $arr[1], $num)");
		  	$ver_num = $db->lastInsertId();
			$msg = "Between 1 and $arr[1] i rolled a $num, to verify do /tell <myname> verify $ver_num";
		} else {
		  	$row = $db->fObject();
		  	if((time() - $row->time) <= 30)
		  		$msg = "You can only flip once ever 30 seconds.";
		  	else {
			  	$num = rand(1, $arr[1]);
				$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (".time().", '$sender', 1, 1, $arr[1], $num)");
			  	$ver_num = $db->lastInsertId();
				$msg = "Between 1 and $arr[1] i rolled a $num, to verify do /tell <myname> verify $ver_num";
			}		
		}
	}
	  	
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} elseif(preg_match("/^roll ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
  	if($arr[2] >= getrandmax())
		$msg = "Can't use the number you have given me. Maximum is <highlight>".getrandmax()."<end>";
	elseif($arr[1] >= $arr[2])
		$msg = "The first number can't be higher then the second one.";
	else {
		$db->query("SELECT * FROM roll_<myname> WHERE `type` = 1 AND `name` = '$sender' ORDER BY `time`");
		if($db->numrows() == 0) {
		  	$num = rand($arr[1], $arr[2]);
			$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (".time().", '$sender', 1, $arr[1], $arr[2], $num)");
			$ver_num = $db->lastInsertId();
			$msg = "Between $arr[1] and $arr[2] i rolled a $num, to verify do /tell <myname> verify $ver_num";
		} else {
		  	$row = $db->fObject();
		  	if((time() - $row->time) <= 30)
		  		$msg = "You can only flip once ever 30 seconds.";
		  	else {
			  	$num = rand($arr[1], $arr[1]);
				$db->query("INSERT INTO roll_<myname> (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (".time().", '$sender', 1, $arr[1], $arr[2], $num)");
				$ver_num = $db->lastInsertId();
				$msg = "Between $arr[1] and $arr[2] i rolled a $num, to verify do /tell <myname> verify $ver_num";
			}
		}
	}
	
	if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} elseif(preg_match("/^verify ([0-9]+)$/i", $message, $arr)) {
	$db->query("SELECT * FROM roll_<myname> WHERE `id` = $arr[1] ORDER BY `time`");
	if($db->numrows() == 0)
		$msg = "Sry but your verify number doesn't exist.";
	else {
	  	$row = $db->fObject();
	  	$time = time() - $row->time;
	  	$msg = "$time seconds ago i told <highlight>$row->name<end>: ";
	  	if($row->type == 0) {
		    if($row->result == 1)
		    	$msg .= "The coin landed <highlight>heads<end>";
		    else
		    	$msg .= "The coin landed <highlight>tails<end>";
		} else
	  		$msg .= "Between $row->start and $row->end i rolled a <highlight>$row->result<end>";
	}
	
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
    	bot::send($msg);
    elseif($type == "guild")
    	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>