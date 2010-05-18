<?php
   /*
   ** Author: Neksus (RK2)
   ** Original IGNbot version  by: Drevi1
   ** Description: Inspects "Christmas Gift", "Light Perennium Container", and "Expensive Gift from Earth"
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 14.09.2006
   ** Date(last modified): 14.09.2006
   **
   ** Copyright (C) 2006 Kian Andersen
   **
   */

if (preg_match("/^inspect (.+)$/i", $message, $arr)) {
	$realword = $arr[1];
	if (
		preg_match("/<a href=\"itemref://[0-9]{6}/[0-9]{6}/[0-9]{1,3}\">Christmas Gift</a>/i",$realword) ||
		preg_match("/<a href=\"itemref://[0-9]{6}/[0-9]{6}/[0-9]{1,3}\">Expensive Gift from Earth</a>/i",$realword) ||
		preg_match("/<a href=\"itemref://[0-9]{6}/[0-9]{6}/[0-9]{1,3}\">Light Perennium Container</a>/i",$realword)) {
		$idql = str_replace("<a href=\"itemref://","",$realword);
		$idql = str_replace("\">Christmas Gift</a>","",$idql);
		$idql = str_replace("\">Expensive Gift from Earth</a>","",$idql);
		$idql = str_replace("\">Light Perennium Container</a>","",$idql);
		$splitidql = split('/',$idql);
		$lid = $splitidql[0];
		$hid = $splitidql[1];
		$ql = $splitidql[2];
		
		switch ($hid) {
		 case 205842:
			$type = "Funny Arrow";
			break;
		 case 205843:
			$type = "Monster Sunglasses";
			break;
		 case 205844:
			$type = "Karlsson Propellor Cap";
			break;
		 case 216286:
			$type = "Funk Flamingo Sunglasses or Disco Duck Sunglasses or Electric Boogie Sunglasses or Gurgling River Sprite";
			break;
		 case 245658:
			$type = "Blackpack";
			break;
		 case 245596:
			$type = "Doctor's Pill Pack";
			break;
		 case 245594:
			$type = "Syndicate Shades";
			break;
		 default:
			$type = "Unidentified";
		}
		$msg = "QL ".$ql." of ".$type;
		bot::send($msg, $sendto);
	}
} else {
	$syntax_error = true;
}
?>