<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets or clears the raidleader
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.02.2006
   ** Date(last modified): 03.03.2006
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

if(eregi("^leader (.+)$", $message, $arr)) {
    $uid = AoChat::get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
	if(!$uid)
		$msg = "Player <highlight>".$name."<end> does not exist.";
	elseif(!isset($this->chatlist[$name]))
		$msg = "Player <highlight>".$name."<end> did´t joined this channel.";
	else {
		$this->vars["leader"] = $name;
	  	$msg = "$name is now Raidleader.";	    	
	}
  	bot::send($msg);
} elseif(eregi("^leader$", $message)) {
  	if($this->vars["leader"] == $sender) {
		unset($this->vars["leader"]);
	  	$msg = "Raidleader is cleared.";
	} elseif($this->vars["leader"] != "") {
		if($this->admins[$sender]["level"] >= $this->admins[$this->vars["leader"]]["level"]){
  			$this->vars["leader"] = $sender;
		  	$msg = "$sender is now Raidleader.";
		} else
			$msg = "You can´t take leader from <highlight>{$this->vars["leader"]}<end>.";
	} else {
		$this->vars["leader"] = $sender;
	  	$msg = "$sender is now Raidleader.";
	}
  	bot::send($msg);

} else
	$syntax_error = true;
?>