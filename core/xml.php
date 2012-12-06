<?php
/*
** Author: Sebuda, Derroylo (RK2)
** Description: AO xml abstaction layer for guild info, whois, player history and server status.
** Version: 1.1
**
** Developed for: Budabot(http://sourceforge.net/projects/budabot)
**
** Date(created): 01.10.2005
** Date(last modified): 16.01.2007
**
** Copyright (C) 2005, 2006, 2007 Carsten Lohmann and J. Gracik
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

//class provide some basic function to splice XML Files or getting an XML file from a URL
class xml {
	//Extracts one entry of the XML file
	public function spliceData($sourcefile, $start, $end) {
		$data = explode($start, $sourcefile, 2);
		if (!$data || (is_array($data) && count($data) < 2)) {
			return "";
		}
		$data = $data[1];
		$data = explode($end, $data, 2);
		if (!$data || (is_array($data) && count($data) < 2)) {
			return "";
		}
		return $data[0];
	}

	//Extracts more then one entry of the XML file
	public function spliceMultiData($sourcefile, $start, $end) {
		$targetdata = array();
		$sourcedata = explode($start, $sourcefile);
		array_shift($sourcedata);
		forEach ($sourcedata as $indsplit) {
		$target = explode($end, $indsplit, 2);
			$targetdata[] = $target[0];
		}
		return $targetdata;
	}

	//Tries to download a file from a URL
	public function getUrl($url, $timeout = null) {
		$url = strtolower($url);

		if ($timeout === null) {
			$settingManager = Registry::getInstance('settingManager');
			$timeout = $settingManager->get('xml_timeout');
		}

		$http = Registry::getInstance('http');
		$response = $http->get($url)->withTimeout($timeout)->waitAndReturnResponse();

		if ($response->error) {
			return null;
		}
		return $response->body;
	}
} //end class xml

?>
