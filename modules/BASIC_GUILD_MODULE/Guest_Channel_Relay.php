<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: Guestchannel (relay to org chat)
   ** Version: 1.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 02.12.2006
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

//Check if the guestchannel relay is enabled
if($this->settings["guest_relay"] == 1 || (isset($this->vars["guestchannel_enabled"]) && $this->vars["guestchannel_enabled"] && $this->settings["guest_relay"] == 2)) {
 	//If the message comes from the privgroup(alias guest channel) and the message was not a command then
	if($type == "priv" && $this->vars["Guest"][$sender] == true && ($args[2][0] != $this->settings["symbol"] || ($args[2][0] == $this->settings["symbol"] && $this->settings["guest_relay_commands"] == 1))) {
		//Relay the message to the guild channel
        $msg = "<end>{$this->settings["guest_color_channel"]}[Guest]<end> {$this->settings["guest_color_username"]}".$sender."<end>: {$this->settings["guest_color_guild"]}".$message."<end>";
        bot::send($msg, "guild", true);
        //If a guildrelay bot is set do
        if(isset($this->settings["relaybot"]) && $this->settings["relaybot"] != "0")
        	bot::send("grc {$this->settings["guest_color"]}[".$this->vars["my guild"]."] ".$msg, $this->settings["relaybot"]);

	//If the message comes from the guild and the message is not a command and the player is not on ignore
	} elseif($type == "guild" && !$this->settings["Ignore"][$sender] && ($args[2][0] != $this->settings["symbol"] || ($args[2][0] == $this->settings["symbol"] && $this->settings["guest_relay_commands"] == 1))) {
		//Relay the message to the guest channel
        $msg = "<end>{$this->settings["guest_color_channel"]}[{$this -> vars["my guild"]}]<end> {$this->settings["guest_color_username"]}".$sender."<end>: {$this->settings["guest_color_guest"]}".$message."<end>";
        bot::send($msg, NULL, true);
	}
}
?>
