<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Adding a new Member
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 04.04.2006
   ** Date(last modified): 21.11.2006
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

$postFields = array();
$postFields['lowql'] = 'MinQL';
$postFields['highql'] = 'MaxQL';
$postFields['search'] = '';
$postFields['dimension'] = 'rk' . $this->vars["dimension"];
$postFields['submit'] = 'Search';

if (preg_match("/^wtb (\\d+) (\\d+) (.+)$/i", $message, $arr)) {
	$postFields['lowql'] = $arr[1];
	$postFields['highql'] = $arr[2];
	$postFields['search'] = $arr[3];
} else if (preg_match("/^wtb (\\d+) (.+)$/i", $message, $arr)) {
	$postFields['lowql'] = $arr[1];
	$postFields['highql'] = $arr[1];
	$postFields['search'] = $arr[2];
} else if (preg_match("/^wtb (.+)$/i", $message, $arr)) {
	$postFields['search'] = $arr[1];
} else {
	$syntax_error = true;
}

if (!$syntax_error) {
	$myCurl = new MyCurl("http://www.aojunkyard.com/suche.php");
	$myCurl->setPost($postFields);
	$myCurl->createCurl();

	$doc = new DOMDocument();
	$doc->prevservWhiteSpace = false;
	$doc->loadHTML($myCurl->__tostring());
	
	$tables = $doc->getElementsByTagName('table');
	$rows = $tables->item(0)->getElementsByTagName('tr');
	
	$title = 'Shopping Results for ' . $postFields['search'];
	$items = '';
	forEach ($rows as $row){
		$childNodes = $row->childNodes;
		
		$ql = trim($childNodes->item(0)->nodeValue);
		$item = trim($childNodes->item(1)->nodeValue);
		$seller = trim($childNodes->item(2)->nodeValue);
		$time = trim($childNodes->item(3)->nodeValue);
		
		// skip the first row
		if ($ql == "QL") {
			continue;
		}
		
		//echo $childNodes->item(1)->getElementsByTagName('a')->item(0)->getAttribute('href') . "\n\n";
		
		$lookup = bot::makeLink('Lookup', "/tell <myname> items $ql $item", 'chatcmd');

		$items .= bot::makeLink($seller, "/tell $seller", 'chatcmd') . ": $item (ql $ql) [" . $time . "] $lookup \n";
	}
	
	if ($items != '') {
		$items = $title . "\n\n" . $items . "\n\nSearch results provided by http://www.aojunkyard.com/";
		$msg = bot::makeLink($title, $items, 'blob');
	} else {
		$msg = 'No items found. Maybe try fewer keywords.';
	}
	
	bot::send($msg, $sendto);
}

?>