<?php
/*
** Author: Mindrila (RK1)
** Credits: Legendadv (RK2)
** BUDABOT IRC NETWORK MODULE
** Version = 0.1
** Developed for: Budabot(http://budabot.com)
**
*/

/*
 * This is the main parse function for incoming messages other than
 * IRC related stuff
 */
function parse_incoming_bbin($bbinmsg, $nick) {
	$db = DB::get_instance();
	global $chatBot;
	global $bbinSocket;

	if (preg_match("/^\[BBIN:LOGON:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
		// a user logged on somewhere in the network
		// first argument is name, second is dimension, third indicates a guest
		$name = $arr[1];
		$dimension = $arr[2];
		$isguest = $arr[3];

		// get character information
		$character = Player::get_by_name($name, $dimension);
		
		$guild = str_replace("'", "''", $character->guild);

		// add user to bbin_chatlist_<myname>
		$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
			"VALUES ('$name', $isguest, '$nick', '$character->faction', '$character->profession', '$guild', '$character->breed', '$character->level', '$character->ai_level', $dimension, '')";
		$db->exec($sql);

		// send notification to channels
		$msg = "<highlight>$name<end> (<highlight>{$character->level}<end>/<green>{$character->ai_level}<end>, <highlight>{$character->profession}<end>, {$character->faction})";
		if ($character->guild != "") {
			$msg .=	" {$character->guild_rank} of {$character->guild}";
		}
		$msg .= " has joined the network";
		if ($isguest == 1) {
			$msg .= " as a guest";
		}
		$msg .= ".";

		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> $msg", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[BBIN]<end> $msg", "priv", true);
		}

	} else if (preg_match("/^\[BBIN:LOGOFF:(.*?),(.),(.)\]/", $bbinmsg, $arr)) {
		// a user logged off somewhere in the network
		$name = $arr[1];
		$dimension = $arr[2];
		$isguest = $arr[3];

		// delete user from bbin_chatlist table
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE (`name` = '$name') AND (`dimension` = $dimension) AND (`ircrelay` = '$nick')");

		// send notification to channels
		$msg = "";
		if ($isguest == 1) {
			$msg = "Our guest ";
		}
		$msg .= "<highlight>$name<end> has left the network.";


		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> $msg", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[BBIN]<end> $msg", "priv", true);
		}

	} else if (preg_match("/^\[BBIN:SYNCHRONIZE\]/",$bbinmsg)) {
		// a new bot joined and requested a full online synchronization

		// send actual online members
		$msg = "[BBIN:ONLINELIST:".$chatBot->vars["dimension"].":";
		$data = $db->query("SELECT name FROM online WHERE channel_type = 'guild' AND added_by = '<myname>'");
		$numrows = count($data);
		forEach ($data as $row) {
			$msg .= $row->name . ",0,";
		}

		$data = $db->query("SELECT * FROM online WHERE channel_type = 'priv' AND added_by = '<myname>'");
		$numrows += count($data)
		forEach ($data as $row) {
			$msg .= $row->name . ",1,";
		}
		if ($numrows != 0) {
			// remove trailing , if there is one
			$msg = substr($msg,0,strlen($msg)-1);
		}

		$msg .= "]";

		// send complete list back to bbin channel
		fputs($bbinSocket, "PRIVMSG ".Setting::get('bbin_channel')." :$msg\n");

	} else if (preg_match("/^\[BBIN:ONLINELIST:(.):(.*?)\]/", $bbinmsg, $arr)) {
		// received a synchronization list
		
		$db->begin_transaction();
		
		// delete all buddies from that nick
		$db->exec("DELETE FROM bbin_chatlist_<myname> WHERE `ircrelay` = '$nick'");
		
		// Format: [BBIN:ONLINELIST:dimension:name,isguest,name,isguest....]
		$dimension = $arr[1];
		$listplode = explode(',', $arr[2]);

		// listplode should be: {name,isguest,name,isguest ...}
		while (true) {
			// as using array_pop will lead to null some time,
			// this loop will exit when all chars are parsed
				
			// pop last value off array (isguest of last member)
			$isguest = array_pop($listplode);
				
			// pop next value off array (name of last member)
			$name = array_pop($listplode);
				
			if ($isguest == null || $name == null) {
				// we popped all items of the array, break
				break;
			}
				
			// get character information
			$character = Player::get_by_name($name, $dimension);
			
			$guild = str_replace("'", "''", $character->guild);

			// add user to bbin_chatlist_<myname>
			$sql = "INSERT INTO bbin_chatlist_<myname> (`name`, `guest`, `ircrelay`, `faction`, `profession`, `guild`, `breed`, `level`, `ai_level`, `dimension`, `afk`) " .
				"VALUES ('$name', $isguest, '$nick', '$character->faction', '$character->profession', '$guild', '$character->breed', '$character->level', '$character->ai_level', $dimension, '')";
			$db->exec($sql);
		}
		
		$db->commit();
	} else {
		// normal message
		if ($chatBot->vars['my_guild'] != "") {
			$chatBot->send("<yellow>[BBIN]<end> $bbinmsg", "guild", true);
		}
		if ($chatBot->vars['my_guild'] == "" || Setting::get("guest_relay") == 1) {
			$chatBot->send("<yellow>[BBIN]<end> $bbinmsg", "priv", true);
		}
	}
}

function bbinConnect() {
	global $bbinSocket;
	$db = DB::get_instance();

	IRC::connect($bbinSocket, Setting::get('bbin_nickname'), Setting::get('bbin_server'), Setting::get('bbin_port'), Setting::get('bbin_password'), Setting::get('bbin_channel'));
	if (IRC::isConnectionActive($bbinSocket)) {
		Setting::save("bbin_status", "1");
		$db->exec("DELETE FROM bbin_chatlist_<myname>");
		fputs($bbinSocket, "PRIVMSG ".Setting::get('bbin_channel')." :[BBIN:SYNCHRONIZE]\n");
		parse_incoming_bbin("[BBIN:SYNCHRONIZE]", '');
		return true;
	} else {
		return false;
	}
}

?>
