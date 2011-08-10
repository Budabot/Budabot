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

if (preg_match("/^nlline ([0-9]*)$/i", $message, $arr)) {

	$nanoline_id = $arr[1];

	$sql = "SELECT * FROM nanolines WHERE id = $nanoline_id";
	$db->query($sql);

	$msg = '';
	if ($row = $db->fObject()) {

		$header = "$row->profession $row->name Nanos";

		$window = Text::make_header($header, "none");

		$sql = "
			SELECT
				n1.lowid,
				lowql,
				n1.name,
				location
			FROM
				nanos n1
				JOIN nano_nanolines_ref n2
					ON (n1.lowid = n2.lowid)
			WHERE
				n2.nanolineid = $nanoline_id
			ORDER BY
				lowql DESC, name ASC";
		$db->query($sql);
		$data = $db->fObject('all');

		forEach ($data as $row) {
			$window .= "<a href='itemref://" . $row->lowid . "/" . $row->lowid . "/" . $row->lowql . "'>" . $row->name . "</a>";
			$window .= " [$row->lowql] $row->location\n";
		}

		$window .= "\n\nAO Nanos by Voriuste";

		$msg = Text::make_blob($header, $window);

	} else {
		$msg = "No nanoline found.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
