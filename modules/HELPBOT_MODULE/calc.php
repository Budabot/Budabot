<?
   /*
   ** Author: Derroylo (RK2)
   ** Description: A little calculator 
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 14.12.2005
   ** Date(last modified): 07.06.2006
   ** 
   ** Copyright (C) 2005 Carsten Lohmann
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

if(eregi("^calc (.+)$", $message, $arr)) {
    $calc = strtolower($arr[1]);

    //check if the calc string includes not allowed chars
    $calc_check = strspn($calc, "0123456789.,+-*x%()/\\ ");

    //If no wrong char found
    if($calc_check == strlen($calc)) {
        $result = "";
        //Do the calculations
   		$calc = "\$result = ".$calc.";";
        eval($calc);
        //If calculation is succesfull
   		if(is_numeric($result)) {
            $result = round($result, 4);
            $msg = $arr[1]." = <highlight>".$result."<end>";
        } else
            $msg = "Wrong syntax for the calc command!";
    } else
        $msg = "Wrong syntax for the calc command!";

    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
      	bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");
}
?>
