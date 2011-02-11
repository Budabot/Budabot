<?php
   /*
   ** Module: NANOLINES
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Shows the nanolines and nanos in each nanoline for each profession
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 31-May-2009
   ** Date(last modified): 9-Mar-2010
   **
   ** Copyright (C) 2009 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos:
   ** This file is an addon to Budabot.
   **
   ** This module is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** This module is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with this module; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */

if (preg_match("/^nlprof (.*)$/i", $message, $arr)) {

	$profession = Util::get_profession_name($arr[1]);
	if ($profession == '') {
		$msg = "Please choose one of these professions: adv, agent, crat, doc, enf, eng, fix, keep, ma, mp, nt, sol, shade, or trader";
		bot::send($msg, $sendto);
		return;
	}

	$sql = "SELECT * FROM aonanos_nanolines WHERE profession LIKE '$profession' ORDER BY name ASC";
	$db->query($sql);

	$count = 0;
	while ($row = $db->fObject()) {

		$count++;
		if ($this->settings["shownanolineicons"] == "1") {
			$window .= "<img src='rdb://$row->image_id'>\n";
		}
		$window .= bot::makeLink("$row->name", "/tell <myname> <symbol>nlline $row->id", 'chatcmd');
		$window .= "\n";
	}

	$msg = '';
	if ($count > 0) {
		$window = Text::make_header("$profession Nanolines", "none") . $window;
		$window .= "\n\nAO Nanos by Voriuste";
		$msg = bot::makeLink("$profession Nanolines", $window, 'blob');
	} else {
		$msg = "Profession not found.";
	}

	bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
