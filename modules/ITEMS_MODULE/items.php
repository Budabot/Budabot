<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Items DB search
   ** Version: 0.8
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 23.11.2005
   ** Date(last modified): 22.11.2006
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
   
if (preg_match("/^items ([0-9]+) (.+)$/i", $message, $arr)) {
    $ql = $arr[1];
    if (!($ql >= 1 && $ql <= 500)) {
        $msg = "Invalid Ql specified(1-500)";
        $chatBot->send($msg, $sendto);
        return;
    }
    $search = $arr[2];
} else if (preg_match("/^items (.+)$/i", $message, $arr)) {
    $search = $arr[1];
    $ql = false;
} else {
  	$syntax_error = true;
	return;
}

// ao automatically converts '&' to '&amp;', so we convert it back
$search = str_replace("&amp;", "&", $search);

if ($chatBot->settings["itemdb_location"] == 'Xyphos.com') {
	$msg = find_items_from_xyphos($search, $ql);
} else {
	// default to local
	$msg = find_items_from_local($search, $ql);
}
$chatBot->send($msg, $sendto);

?>