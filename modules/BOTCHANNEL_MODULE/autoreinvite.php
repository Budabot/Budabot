<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Reinvites the players that have been in the privgroup before restart/crash
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.07.2006
   ** Date(last modified): 23.07.2006
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
   
if (count($this->vars["members_before_restart"]) > 0) {
	forEach ($this->vars["members_before_restart"] as $key => $value) {
	  	AOChat::privategroup_kick($key);
		AOChat::privategroup_invite($key);
	}
}

unset($this->vars["members_before_restart"]);
?>