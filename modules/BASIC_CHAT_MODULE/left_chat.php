<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Privatechannel leave
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 14.02.2006
   ** 
   ** Copyright (C) 2005, 2006 Carsten Lohmann
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

global $caller;
if($this->vars["leader"] == $sender){
	$this->vars["leader"] = "";
	bot::send("<yellow>Leader has been cleared, $sender left channel.");			
}		
if($caller == $sender)
	unset($caller);
	
if(!isset($this->vars["Guest"][$sender])) {
	$db->query("DELETE FROM priv_chatlist_<myname> WHERE `name` = '$sender'");
}
?>
