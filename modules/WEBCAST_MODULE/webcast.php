<?php
   /*
   ** Author: Healnjoo (RK2)
   ** Description: Publish the bots Online list to a webpage
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 03/12/2007
   ** Date(last modified): 03/12/2007
   ** 
   ** Licence Infos: 
   ** Same as Budabot.
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
   
if (!isset($chatBot->guildmembers[$sender]) || !$chatBot->is_ready()) {
	return;
}

$info = explode(" ", $message);
list($msg, $command, $other) = $info;
$webpath = Setting::get("webpath");

$webpathhelp  = "<header>Error: No Webpath Found<end>\n\n";
$webpathhelp .= "This error occurs if no webpath is saved in the bot settings.\n";
$webpathhelp .= "The webcast needs to send the bots online details to a webserver\n";
$webpathhelp .= "If the webpath setting is invalid or not existing, the webcast can not update\n\n";
$webpathhelp .= "Solution:\n";
$webpathhelp .= "/tell <myname> webcast setwebpath [web server path]\n";
$webpathhelp .= "For example: /tell <myname> webcast setwebpath http://www.myserver.com/online.php\n";
$webpathhelplink = Text::make_link("::Webcast Error::", $webpathhelp);

if ($command) {
	if (strtolower($command) == "clearcache") {
		//		$chatBot->send($command." / ".$webpath, $sender);
		if ($webpath) {
			$send = file_get_contents($webpath."?clearcache=true");
			if ($send) {
				$chatBot->send($send, $sender);
			} else {
				$chatBot->send("Unable to clear cache",$sender);
			}
		} else {
			$chatBot->send("Unable to find webpath",$sender);
		}
	} else if (strtolower($command == "setwebpath")) {
		if ($other) {
			Setting::save("webpath", $other);

			if (Setting::get("webpath") == $other) {
				$chatBot->send("Webpath Saved.", $sender);
			} else {
				$chatBot->send("Unable to save Webpath",$sender);
			}
		} else {
			$chatBot->send($webpathhelplink, $sender);
		}
	}
} else if ($webpath) {
	$db->query("SELECT o.name, afk FROM online o LEFT JOIN players p ON o.name = p.name WHERE channel_type = 'guild' ORDER BY `profession`, `level` DESC");
	$data = $db->fObject("all");

	forEach ($data as $row) {	
		$afk = "";
		if ($row->afk == "kiting") {
			$afk = "|KITING";
		} else if ($row->afk != '') {
			$afk = "|AFK";
		}
		
		$list .= $row->name.$afk."\r\n";
	} 

	//do guests
	$db->query("SELECT o.name, afk FROM online o LEFT JOIN players p ON o.name = p.name WHERE channel_type = 'priv' ORDER BY `profession`, `level` DESC");
	$data = $db->fObject("all");

	forEach ($data as $row) {	        
		$afk = "";
		if ($row->afk == "kiting") {
			$afk = "|KITING";
		} else if ($row->afk != '') {
			$afk = "|AFK";
		}
		
		$list .= $row->name.$afk."\r\n";
	} 

	if ($list) {
		$send = file_get_contents($webpath."?upload=".rawurlencode($list));
		if ($type == "msg" || $type == 'priv' || $type == 'guild') {
			$chatBot->send("Webcast Updated.", $sender);
		}
	}
} else {
	$chatBot->send($webpathhelplink, $sender);
}

?>