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

if (preg_match("/^alts add (.+)$/i", $message, $arr))
{
	/* get all names in an array */
	$names = explode(' ', $arr[1]);
	/* initialize some arrays for different outcomes */
	$names_not_existing = array();
	$names_already_registered = array();
	$names_succeeded = array();
	/* Pop a name from the array until none are left (checking for null) */
	while (null != ($name = array_pop($names)))
	{
		$uid = AoChat::get_uid($name);
		/* check if player exists */
		if (!$uid)
		{
			$names_not_existing[] = $name;
			continue;
		}
		/* check if player is already an alt */
		$db->query("SELECT * FROM alts WHERE `alt` = '$name'");
		if ($db->numrows() != 0)
		{
			$names_already_registered[] = $name.' (as alt)';
			continue;
		}
		/* check if player is already a main */
		$db->query("SELECT * FROM alts WHERE `main` = '$name'");
		if ($db->numrows() != 0)
		{
			$names_already_registered = $name.' (as main)';
			continue;
		}

		/* check to make sure the $sender himself isn't already an alt */
		/* insert into database */
		$db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
		if ($db->numrows() != 0)
		{
			$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name', '$row->main')");
		}
		else
		{
			$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name', '$sender')");
		}
		$names_succeeded[] = $name;
	}
	$window = 'Alts added:<br>';
	foreach ($names_succeeded as $alt)
	{
		$window .= $alt.' ';
	}
	$window .= '<br><br>Alts already registered:<br>';
	foreach ($names_already_registered as $alt)
	{
		$window .= $alt.' ';
	}
	$window .= '<br><br>Alts not existing :<br>';
	foreach ($names_not_existing as $alt)
	{
		$window .= $alt.' ';
	}
	$link = 'Added '.count($names_succeeded).' alts to your list.';
	$failed_count = count($names_already_registered) + count($names_not_existing);
	if ($failed_count > 0)
	{
		$link .= ' Failed adding '.$failed_count.' alts to your list.';
	}
	$msg = $this->makeLink($link, $window);
	$this->send($msg, $sendto);
	return;
}

elseif (preg_match("/^alts (rem|del|remove|delete) (.+)$/i", $message, $arr))
{
	$name = ucfirst(strtolower($arr[2]));

	$db->query("SELECT * FROM alts WHERE `main` = '$sender' AND `alt` = '$name'");
	$row = $db->fObject();
	if ($row->main == $sender)
	{
		$db->query("DELETE FROM alts WHERE `main` = '$sender' AND `alt` = '$name'");
		$msg = "<highlight>$name<end> has been deleted from your alt list.";
	}
	else
	{
		//sender was not found as a main.  checking if he himself is an alt and let him be able to modify his own alts list
		$db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
		$row = $db->fObject();
		//retrieve his main, use the main's name to do searches and modifications with
		$main = $row->main;
		$db->query("SELECT * FROM alts WHERE main = '$main' AND alt = '$name'");
		if ($db->numrows() != 0)
		{
			$db->query("DELETE FROM alts WHERE main = '$main' AND alt = '$name'");
			$msg = "<highlight>$name<end> has been deleted from your ($main) alt list.";
		}
		else
		{
			$msg = "<highlight>$name<end> is not registered as your alt.";
		}
	}

}

elseif (preg_match("/^alts main (.+)$/i", $message, $arr))
{
	$name_alt = $sender;
	$name_main = ucfirst(strtolower($arr[1]));
	$uid = $this->get_uid($name_main);
	if (!$uid)
	{
		$msg .= " Player <highlight>$name_main<end> does not exist.";
	}
	if ($uid)
	{
		$db->query("DELETE FROM alts WHERE `alt` = '$name_alt'");
		$db->query("SELECT * FROM alts WHERE `main` = '$name_alt'");
		if ($db->numrows() != 0)
		{
			$msg = "You are already registered as main from someone.";
		}
		else
		{
			$db->query("SELECT * FROM alts WHERE `alt` = '$name_main'");
			if ($db->numrows() != 0)
			{
				$row = $db->fObject();
				$name_main = $row->main;
			}
			$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$name_alt', '$name_main')");
			$msg = "You have been registered as an alt of $name_main.";
		}

	}
}

elseif (preg_match('/^alts setmain (.+)$/i', $message, $arr))
{
	// check if new main exists
	$new_main = ucfirst(strtolower($arr[1]));
	$uid = $this->get_uid($new_main);
	if (!$uid)
	{
		$msg = "Player <highlight>".$new_main."<end> does not exist.";
		$this->send($msg, $sendto);
		return;
	}
	
	// check for the current main
	$db->query("SELECT * FROM alts WHERE (`alt` = '$sender') OR (`main` = '$sender')");
	if ($db->numrows() == 0)
	{
		$msg = "<highlight>Could not find a main for your char.<end>";
		$this->send($msg, $sendto);
		return;
	}
	$row = $db->fObject();
	$current_main = $row->main;
	
	// get all alts from that main
	$db->query("SELECT * FROM alts WHERE `main` = '$current_main'");
	$all_alts = $db->fObject("all");
	
	// delete all alts from the old main
	$db->query("DELETE FROM alts WHERE `main` = '$current_main'");
	
	// add everything back with the new main
	foreach ($all_alts as $db_entry)
	{
		$alt_name = $db_entry->alt;
		if ($alt_name != $new_main)
		{
			$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$alt_name', '$new_main')");
		}
	}
	$db->query("INSERT INTO alts (`alt`, `main`) VALUES ('$current_main', '$new_main')");
	
	$msg = "Successfully set your new main as <highlight>'$new_main'<end>.";
	$this->send($msg, $sendto);
	return;
}

elseif (preg_match("/^alts (.+)$/i", $message, $arr))
{
	$name = ucfirst(strtolower($arr[1]));
	$uid = AoChat::get_uid($arr[1]);
	if (!$uid)
	{
		$msg = "Player <highlight>".$name."<end> does not exist.";
	}
	else
	{
		$main = false;
		// Check if sender is himelf the main
		$db->query("SELECT * FROM alts WHERE `main` = '$name'");
		if ($db->numrows() == 0)
		{
			// Check if sender is an alt
			$db->query("SELECT * FROM alts WHERE `alt` = '$name'");
			if ($db->numrows() == 0)
				$msg = "No alts are registered for <highlight>$name<end>.";
			else
			{
				$row = $db->fObject();
				$main = $row->main;
			}
		}
		else
			$main = $name;

		// If a main was found create the list
		if ($main)
		{
			$list = "<header>::::: Alternative Character List :::::<end> \n \n";
			$list .= ":::::: Main Character\n";
			$list .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
			$online = $this->buddy_online($main);
			if ($online === null)
			{
				$list .= "No status.\n";
			}
			elseif ($online == 1)
			{
				$list .= "<green>Online<end>\n";
			}
			else
			{
				$list .= "<red>Offline<end>\n";
			}
			$list .= ":::::: Alt Character(s)\n";
			$db->query("SELECT * FROM alts WHERE `main` = '$main'");
			while ($row = $db->fObject())
			{
				$list .= "<tab><tab>".bot::makeLink($row->alt, "/tell ".$this->vars["name"]." whois $row->alt", "chatcmd")." - ";
				$online = $this->buddy_online($row->alt);
				if ($online === null)
				{
					$list .= "No status.\n";
				}
				else if ($online == 1)
				{
					$list .= "<green>Online<end>\n";
				}
				else
				{
					$list .= "<red>Offline<end>\n";
				}
			}
			$msg = bot::makeLink($main."`s Alts", $list);
		}
	}
}

elseif (preg_match("/^alts$/i", $message))
{
	$main = false;
	// Check if $sender is himself the main
	$db->query("SELECT * FROM alts WHERE `main` = '$sender'");
	if ($db->numrows() == 0)
	{
		// Check if $sender is an alt
		$db->query("SELECT * FROM alts WHERE `alt` = '$sender'");
		if ($db->numrows() == 0)
			$msg = "No alts are registered for <highlight>$sender<end>.";
		else
		{
			$row = $db->fObject();
			$main = $row->main;
		}
	}
	else
	{
		$main = $sender;
	}

	// If a main was found create the list
	if ($main)
	{
		$list = "<header>::::: Alternative Character List :::::<end> \n \n";
		$list .= ":::::: Main Character\n";
		$list .= "<tab><tab>".bot::makeLink($main, "/tell ".$this->vars["name"]." whois $main", "chatcmd")." - ";
		$online = $this->buddy_online($main);
		if ($online === null)
		{
			$list .= "No status.\n";
		}
		else if ($online == 1)
		{
			$list .= "<green>Online<end>\n";
		}
		else
		{
			$list .= "<red>Offline<end>\n";
		}

		$list .= ":::::: Alt Character(s)\n";
		$db->query("SELECT * FROM alts WHERE `main` = '$main'");
		while ($row = $db->fObject())
		{
			$list .= "<tab><tab>".bot::makeLink($row->alt, "/tell ".$this->vars["name"]." whois $row->alt", "chatcmd")." - ";
			$online = $this->buddy_online($row->alt);
			if ($online === null)
			{
				$list .= "No status.\n";
			}
			else if ($online == 1)
			{
				$list .= "<green>Online<end>\n";
			}
			else
			{
				$list .= "<red>Offline<end>\n";
			}
		}
		$msg = bot::makeLink($sender."`s Alts", $list);
	}
}


// Send info back
bot::send($msg, $sendto);

?>
