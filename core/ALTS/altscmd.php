<?php
/*
 ** Author: Derroylo (RK2)
 ** Description: Alt Char Handling
 ** Version: 1.0
 **
 ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
 **
 ** Date(created): 23.11.2005
 ** Date(last modified): 21.11.2006
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

if (preg_match("/^alts add ([a-z0-9- ]+)$/i", $message, $arr)) {
	/* get all names in an array */
	$names = explode(' ', $arr[1]);
	
	$sender = ucfirst(strtolower($sender));
	
	$senderAltInfo = Alts::get_alt_info($sender);
	$main = $senderAltInfo->main;
	
	$self_registered = array();
	$other_registered = array();
	$names_succeeded = array();
	
	/* Pop a name from the array until none are left (checking for null) */
	foreach ($names as $name) {
		$name = ucfirst(strtolower($name));
		
		$altinfo = Alts::get_alt_info($name);
		if ($altinfo->main == $sender) {
			// Already registered to self
			$self_registered []= $name;
			continue;
		}
		
		if (count($altinfo->alts) > 0) {
			// Already registered to someone else
			$other_registered []= $name;
			continue;
		}
		
		$validated = 0;
		
		if ($sender == $main || (Setting::get("validate_from_validated_alt") == 1 && $senderAltInfo->currentValidated)) {
			$validated = 1;
		}
		
		/* insert into database */
		Alts::add_alt($main, $name, $validated);
		$names_succeeded []= $name;
		
		// update character info
		Player::get_by_name($name);
	}
	
	$window = '';
	if ($names_succeeded) {
		$window .= "Alts added:\n" . implode(' ', $names_succeeded) . "\n\n";
	}
	if ($self_registered) {
		$window .= "Alts already registered to yourself:\n" . implode(' ', $self_registered) . "\n\n";
	}
	if ($other_registered) {
		$window .= "Alts already registered to someone else:\n" . implode(' ', $other_registered) . "\n\n";
	}
	
	/* create a link */
	if (count($names_succeeded) > 0) {
		$link = 'Added '.count($names_succeeded).' alts to your list. ';
	}
	$failed_count = count($other_registered) + count($self_registered);
	if ($failed_count > 0) {
		$link .= 'Failed adding '.$failed_count.' alts to your list.';
	}
	$msg = Text::make_blob($link, $window);

	$chatBot->send($msg, $sendto);
} else if (preg_match("/^alts (rem|del|remove|delete) ([a-z0-9-]+)$/i", $message, $arr)) {
	$name = ucfirst(strtolower($arr[2]));
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!in_array($name, $altInfo->alts)) {
		$msg = "<highlight>{$name}<end> is not registered as your alt.";
	} else {
		Alts::rem_alt($altInfo->main, $name);
		$msg = "<highlight>{$name}<end> has been deleted from your alt list.";
	}
	$chatBot->send($msg, $sendto);
} else if (preg_match('/^alts setmain ([a-z0-9-]+)$/i', $message, $arr)) {
	// check if new main exists
	$new_main = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($new_main);
	if (!$uid) {
		$msg = "Player <highlight>{$new_main}<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	$altInfo = Alts::get_alt_info($sender);
	
	if (!in_array($new_main, $altInfo->alts)) {
		$msg = "<highlight>{$new_main}<end> must first be registered as your alt.";
		$chatBot->send($msg, $sendto);
		return;
	}

	$db->beginTransaction();

	// remove all the old alt information
	$db->exec("DELETE FROM `alts` WHERE `main` = '{$altInfo->main}'");

	// add current main to new main as an alt
	Alts::add_alt($new_main, $current_main);
	
	// add current alts to new main
	forEach ($altInfo->alts as $alt) {
		if ($alt != $new_main) {
			Alts::add_alt($new_main, $alt);
		}
	}
	
	$db->commit();

	$msg = "Successfully set your new main as <highlight>{$new_main}<end>.";
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^alts ([a-z0-9-]+)$/i", $message, $arr) || preg_match("/^alts$/i", $message, $arr)) {
	if (isset($arr[1])) {
		$name = ucfirst(strtolower($arr[1]));
	} else {
		$name = $sender;
	}

	$msg = Alts::get_alts_blob($name);
	
	if ($msg === null) {
		$msg = "No alts are registered for <highlight>{$name}<end>.";
	}

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>
