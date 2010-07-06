<?php
   /*
   ** Author: Tyrence (RK2)
   ** Description: Shows gas changes for tower sites
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 22.03.2006
   ** Date(last modified): 9-Mar-2010
   ** 
   ** Copyright (C) 2006 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
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

$db->query("SELECT * FROM tower_watch");
$minutes = $this->settings["alarmpreview"];
$seconds = $minutes * 60;

$secondsPastMidnight = time() % 86400;

while($site = $db->fObject()) {

	$msg = "";
	$variance = 2;
	$closeTime = $site->close_time - $seconds + $variance;

	$currentTime = $secondsPastMidnight;
	if ($closeTime > $secondsPastMidnight) {
		$currentTime += 86400;
	}

	$timePastCloseTime = $currentTime - $closeTime;
	
	if($timePastCloseTime > 3600 * 18 - $variance && $timePastCloseTime < 3600 * 18 + $variance) {
	
			$msg = '<red>' . $site->zone . ' ' . $site->base_number .
			' goes to 25% in <highlight>' . $minutes . '<end> minutes!!';
			
		$this->send($msg, "guild", true);
		
	} else if ($timePastCloseTime > 3600 * 23 - $variance && $timePastCloseTime < 3600 * 23 + $variance) {
		
		$msg = '<red>' . $site->zone . ' ' . $site->base_number .
			' goes to 5% in <highlight>' . $minutes . '<end> minutes!!';
			
		$this->send($msg, "guild", true);
	
	} else if ($timePastCloseTime > 3600 * 24 - $variance && $timePastCloseTime < 3600 * 24 + $variance) {
		
		$msg = '<red>' . $site->zone . ' ' . $site->base_number .
			' goes to 75% in <highlight>' . $minutes . '<end> minutes!!';
			
		$this->send($msg, "guild", true);
	}
}

?>