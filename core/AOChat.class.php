<?php

/*
* $Id: aochat.php,v 1.1 2006/12/08 15:17:54 genesiscl Exp $
*
* Modified to handle the recent problem with the integer overflow
*
* Copyright (C) 2002-2005  Oskari Saarenmaa <auno@auno.org>.
*
* AOChat, a PHP class for talking with the Anarchy Online chat servers.
* It requires the sockets extension (to connect to the chat server..)
* from PHP 4.2.0+ and either the GMP or BCMath extension (for generating
* and calculating the login keys) to work.
*
* A disassembly of the official java chat client[1] for Anarchy Online
* and Slicer's AO::Chat perl module[2] were used as a reference for this
* class.
*
* [1]: <http://www.anarchy-online.com/content/community/forumsandchat/>
* [2]: <http://www.hackersquest.org/ao/>
*
* Updates to this class can be found from the following web site:
*   http://auno.org/dev/aochat.html
*
**************************************************************************
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
* USA
*
*/

require_once 'MMDBParser.class.php';
require_once 'AOChatQueue.class.php';
require_once 'AOChatExtMsg.class.php';
require_once 'AOChatPacket.class.php';

if ((float)phpversion() < 5.0) {
	die("AOChat class needs PHP version 5.0.0 or higher in order to work.\n");
}

if (!extension_loaded("sockets")) {
	die("AOChat class needs the Sockets extension to work.\n");
}

if (!extension_loaded("gmp") && !extension_loaded("bcmath") && !extension_loaded("aokex")) {
	die("AOChat class needs either AOkex, GMP or BCMath extension to work.\n");
}

set_time_limit(0);
ini_set("html_errors", 0);

define('AOC_GROUP_NOWRITE',     0x00000002);
define('AOC_GROUP_NOASIAN',     0x00000020);
define('AOC_GROUP_MUTE',        0x01010000);
define('AOC_GROUP_LOG',         0x02020000);

define('AOC_FLOOD_LIMIT',                7);
define('AOC_FLOOD_INC',                  2);

define('AOEM_UNKNOWN',                0xFF);
define('AOEM_ORG_JOIN',               0x10);
define('AOEM_ORG_KICK',               0x11);
define('AOEM_ORG_LEAVE',              0x12);
define('AOEM_ORG_DISBAND',            0x13);
define('AOEM_ORG_FORM',               0x14);
define('AOEM_ORG_VOTE',               0x15);
define('AOEM_ORG_STRIKE',             0x16);
define('AOEM_NW_ATTACK',              0x20);
define('AOEM_NW_ABANDON',             0x21);
define('AOEM_NW_OPENING',             0x22);
define('AOEM_NW_TOWER_ATT_ORG',       0x23);
define('AOEM_NW_TOWER_ATT',           0x24);
define('AOEM_NW_TOWER',               0x25);
define('AOEM_AI_CLOAK',               0x30);
define('AOEM_AI_RADAR',               0x31);
define('AOEM_AI_ATTACK',              0x32);
define('AOEM_AI_REMOVE_INIT',         0x33);
define('AOEM_AI_REMOVE',              0x34);
define('AOEM_AI_HQ_REMOVE_INIT',      0x35);
define('AOEM_AI_HQ_REMOVE',           0x36);

class AOChat {
	var $state, $debug, $id, $gid, $chars, $char, $grp, $buddies;
	var $socket, $last_packet, $last_ping;
	var $serverseed, $chatqueue;

	/* Initialization */
	function __construct() {
		$this->disconnect();
	}

	function disconnect() {
		if (is_resource($this->socket)) {
			socket_close($this->socket);
		}
		$this->socket      = NULL;
		$this->serverseed  = NULL;
		$this->chars       = NULL;
		$this->char        = NULL;
		$this->last_packet = 0;
		$this->last_ping   = 0;
		$this->state       = "connect";
		$this->id          = array();
		$this->gid         = array();
		$this->grp         = array();
		$this->chars       = array();
		$this->chatqueue   = NULL;
	}

	/* Network stuff */
	function connect($server = "chat2.d1.funcom.com", $port = 7102) {
		if ($this->state !== "connect") {
			die("AOChat: not expecting connect.\n");
		}

		$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if (!is_resource($s)) { /* this is fatal */
			die("Could not create socket.\n");
		}

		$this->socket = $s;
		$this->state = "auth";

		if (@socket_connect($s, $server, $port) === false) {
			trigger_error("Could not connect to the AO Chat server ($server:$port): " . socket_strerror(socket_last_error($s)), E_USER_WARNING);
			$this->disconnect();
			return false;
		}

		$packet = $this->get_packet();
		if (!is_object($packet) || $packet->type != AOCP_LOGIN_SEED) {
			trigger_error("Received invalid greeting packet from AO Chat server.", E_USER_WARNING);
			$this->disconnect();
			return false;
		}

		$this->chatqueue = new AOChatQueue(AOC_FLOOD_LIMIT, AOC_FLOOD_INC);

		return $s;
	}

	function iteration() {
		$now = time();

		if ($this->chatqueue !== NULL) {
			$packet = $this->chatqueue->getNext();
			while ($packet !== null) {
				$this->send_packet($packet);
				$packet = $this->chatqueue->getNext();
			}
		}

		if (($now - $this->last_packet) > 60 && ($now - $this->last_ping) > 60) {
			$this->send_ping();
		}
	}

	function wait_for_packet($time = 1) {
		$this->iteration();

		$sec = (int)$time;
		if (is_float($time)) {
			$usec = (int)($time * 1000000 % 1000000);
		} else {
			$usec = 0;
		}

		if (!socket_select($a = array($this->socket), $b = null, $c = null, $sec, $usec)) {
			return NULL;
		} else {
			return $this->get_packet();
		}
	}

	function read_data($len) {
		$data = "";
		$rlen = $len;
		while ($rlen > 0) {
			if (($tmp = socket_read($this->socket, $rlen)) === false) {
				$last_error = socket_strerror(socket_last_error($this->socket));
				die("Read error: $last_error\n");
			}
			if ($tmp == "") {
				die("Read error: EOF\n(Someone else logging on to same account?)\n");
			}
			$data .= $tmp;
			$rlen -= strlen($tmp);
		}
		return $data;
	}

	function get_packet() {
		$head = $this->read_data(4);
		if (strlen($head) != 4) {
			return false;
		}

		list(, $type, $len) = unpack("n2", $head);

		$data = $this->read_data($len);

		if (is_resource($this->debug)) {
			fwrite($this->debug, "<<<<<\n");
			fwrite($this->debug, $head);
			fwrite($this->debug, $data);
			fwrite($this->debug, "\n=====\n");
		}

		$packet = new AOChatPacket("in", $type, $data);

		switch ($type) {
			case AOCP_LOGIN_SEED:
				$this->serverseed = $packet->args[0];
				break;

			case AOCP_CLIENT_NAME:
			case AOCP_CLIENT_LOOKUP:
				list($id, $name) = $packet->args;
				$id = "" . $id;
				$name = ucfirst(strtolower($name));
				$this->id[$id]   = $name;
				$this->id[$name] = $id;
				break;

			case AOCP_GROUP_ANNOUNCE:
				list($gid, $name, $status) = $packet->args;
				$this->grp[$gid] = $status;
				$this->gid[$gid] = $name;
				$this->gid[strtolower($name)] = $gid;
				break;

			case AOCP_GROUP_MESSAGE:
				/* Hack to support extended messages */
				if ($packet->args[1] === 0 && substr($packet->args[2], 0, 2) == "~&") {
					$em = new AOExtMsg($packet->args[2]);
					$packet->args[2] = $em->message;
					$packet->args['extended_message'] = $em;
				}
				break;

			case AOCP_CHAT_NOTICE:
				$category_id = 20000;
				$packet->args[4] = MMDBParser::get_message_string($category_id, $packet->args[2]);
				if ($packet->args[4] !== null) {
					$packet->args[5] = AOExtMsg::parse_params($packet->args[3]);
					if ($packet->args[5] !== null) {
						$packet->args[6] = vsprintf($packet->args[4], $packet->args[5]);
					} else {
						print_r($packet);
					}
				}
				break;
		}

		$this->last_packet = time();

		return $packet;
	}

	function send_packet($packet)
	{
		$data = pack("n2", $packet->type, strlen($packet->data)) . $packet->data;
		if (is_resource($this->debug)) {
			fwrite($this->debug, ">>>>>\n");
			fwrite($this->debug, $data);
			fwrite($this->debug, "\n=====\n");
		}
		socket_write($this->socket, $data, strlen($data));
		return true;
	}

	/* Login functions */
	function authenticate($username, $password) {
		if ($this->state != "auth") {
			die("AOChat: not expecting authentication.\n");
		}

		$key = $this->generate_login_key($this->serverseed, $username, $password);
		$pak = new AOChatPacket("out", AOCP_LOGIN_REQUEST, array(0, $username, $key));
		$this->send_packet($pak);
		$packet = $this->get_packet();
		if ($packet->type != AOCP_LOGIN_CHARLIST) {
			return false;
		}

		for ($i = 0; $i < sizeof($packet->args[0]); $i++) {
			$this->chars[] = array(
			"id"     => $packet->args[0][$i],
			"name"   => ucfirst(strtolower($packet->args[1][$i])),
			"level"  => $packet->args[2][$i],
			"online" => $packet->args[3][$i]);
		}

		$this->username = $username;
		$this->state    = "login";

		return $this->chars;
	}

	function login($char) {
		if ($this->state != "login") {
			die("AOChat: not expecting login.\n");
		}

		if (is_int($char)) {
			$field = "id";
		} else if(is_string($char)) {
			$field = "name";
			$char  = ucfirst(strtolower($char));
		}

		if (!is_array($char)) {
			if (empty($field)) {
				return false;
			} else {
				forEach($this->chars as $e) {
					if ($e[$field] == $char) {
						$char = $e;
						break;
					}
				}
			}
		}

		if (!is_array($char)) {
			echo "AOChat: no valid character to login.\n";
			return false;
		}

		$pq = new AOChatPacket("out", AOCP_LOGIN_SELECT, $char["id"]);
		$this->send_packet($pq);
		$pr = $this->get_packet();
		if ($pr->type != AOCP_LOGIN_OK) {
			return false;
		}

		$this->char  = $char;
		$this->state = "ok";

		return true;
	}

	/* User and group lookup functions */
	function lookup_user($u) {
		$u = ucfirst(strtolower(trim($u)));

		if ($u == '') {
			return false;
		}

		if (isset($this->id[$u])) {
			return $this->id[$u];
		}

		$this->send_packet(new AOChatPacket("out", AOCP_CLIENT_LOOKUP, $u));
		for ($i = 0; $i < 100 && !isset($this->id[$u]); $i++) {
			//$this->get_packet();
			
			// hack so that packets are not discarding while waiting for char id response
			$this->processNextPacket();
		}

		return isset($this->id[$u]) ? $this->id[$u] : false;
	}

	function get_uid($user) {
		if ($this->is_really_numeric($user)) {
			return $this->fixunsigned($user);
		}

		$uid = $this->lookup_user($user);

		if ($uid === false || $uid == 0 || $uid == -1 || $uid == 0xffffffff || !$this->is_really_numeric($uid)) {
			return false;
		}

		return $uid;
	}

	function fixunsigned($num) {
		if ($this->is_really_numeric($num) && bcdiv("" . $num, "2147483648", 0)) {
			$num2 = -1 * bcsub("4294967296", "" . $num);
			return (int)$num2;
		}

		return (int)$num;
	}

	function is_really_numeric($num) {
		if (preg_match("/^([0-9\-]+)$/", "" . $num)) {
			return true;
		}

		return false;
	}

	function lookup_group($arg, $type = 0) {
		if ($type && ($is_gid = (strlen($arg) === 5 && (ord($arg[0])&~0x80) < 0x10))) {
			return $arg;
		}
		if (!$is_gid) {
			$arg = strtolower($arg);
		}
		return isset($this->gid[$arg]) ? $this->gid[$arg] : false;
	}

	function get_gid($g) {
		return $this->lookup_group($g, 1);
	}

	function get_gname($g) {
		if (($gid = $this->lookup_group($g, 1)) === false) {
			return false;
		}
		return $this->gid[$gid];
	}

	/* Sending various packets */
	function send_ping() {
		$this->last_ping = time();
		return $this->send_packet(new AOChatPacket("out", AOCP_PING, "AOChat.php"));
	}

	function send_tell($user, $msg, $blob = "\0", $priority = null) {
		if (($uid = $this->get_uid($user)) === false) {
			return false;
		}
		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}
		$this->chatqueue->push($priority, new AOChatPacket("out", AOCP_MSG_PRIVATE, array($uid, $msg, "\0")));
		$this->iteration();
		return true;
	}

	function send_guild($msg, $blob = "\0", $priority = null) {
		$guild_gid = false;
		forEach ($this->grp as $gid => $status) {
			if (ord(substr($gid, 0, 1)) == 3) {
				$guild_gid = $gid;
				break;
			}
		}
		if (!$guild_gid) {
			return false;
		}
		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}
		$this->chatqueue->push($priority, new AOChatPacket("out", AOCP_GROUP_MESSAGE, array($guild_gid, $msg, "\0")));
		$this->iteration();
		return true;
	}

	function send_group($group, $msg, $blob = "\0", $priority = null) {
		if (($gid = $this->get_gid($group)) === false) {
			return false;
		}
		if ($priority == null) {
			$priority = AOC_PRIORITY_MED;
		}
		$this->chatqueue->push(AOC_PRIORITY_MED, new AOChatPacket("out", AOCP_GROUP_MESSAGE, array($gid, $msg, "\0")));
		$this->iteration();
		return true;
	}

	function group_join($group) {
		if (($gid = $this->get_gid($group)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_GROUP_DATA_SET, array($gid, $this->grp[$gid] & ~AOC_GROUP_MUTE, "\0")));
	}

	function group_leave($group) {
		if (($gid = $this->get_gid($group)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_GROUP_DATA_SET, array($gid, $this->grp[$gid] | AOC_GROUP_MUTE, "\0")));
	}

	function group_status($group) {
		if (($gid = $this->get_gid($group)) === false) {
			return false;
		}

		return $this->grp[$gid];
	}

	/* Private chat groups */
	function send_privgroup($group, $msg, $blob = "\0") {
		if (($gid = $this->get_uid($group)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_MESSAGE, array($gid, $msg, $blob)));
	}

	function privategroup_join($group) {
		if (($gid = $this->get_uid($group)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_JOIN, $gid));
	}

	function privategroup_invite($user) {
		if (($uid = $this->get_uid($user)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_INVITE, $uid));
	}

	function privategroup_kick($user) {
		if (($uid = $this->get_uid($user)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_KICK, $uid));
	}

	function privategroup_leave($user) {
		if (($uid = $this->get_uid($user)) === false) {
			return false;
		}

		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_PART, $uid));
	}

	function privategroup_kick_all() {
		return $this->send_packet(new AOChatPacket("out", AOCP_PRIVGRP_KICKALL, ""));
	}

	/* Buddies */
	function buddy_add($uid, $type = "\1") {
		if ($uid == $this->char['id']) {
			return false;
		} else {
			return $this->send_packet(new AOChatPacket("out", AOCP_BUDDY_ADD, array($uid, $type)));
		}
	}

	function buddy_remove($uid) {
		return $this->send_packet(new AOChatPacket("out", AOCP_BUDDY_REMOVE, $uid));
	}

	function buddy_remove_unknown() {
		return $this->send_packet(new AOChatPacket("out", AOCP_CC, array(array("rembuddy", "?"))));
	}

	/* Login key generation and encryption */
	function get_random_hex_key($bits) {
		$str = "";
		do {
			$str .= sprintf('%02x', mt_rand(0, 0xff));
		} while(($bits -= 8) > 0);
		return $str;
	}

	function bighexdec($x) {
		if (substr($x, 0, 2) != "0x") {
			return $x;
		}
		$r = "0";
		for ($p = $q = strlen($x) - 1; $p >= 2; $p--) {
			$r = bcadd($r, bcmul(hexdec($x[$p]), bcpow(16, $q - $p)));
		}
		return $r;
	}

	function bigdechex($x) {
		$r = "";
		while ($x != "0") {
			$r = dechex(bcmod($x, 16)) . $r;
			$x = bcdiv($x, 16);
		}
		return $r;
	}

	function bcmath_powm($base, $exp, $mod) {
		$base = $this->bighexdec($base);
		$exp  = $this->bighexdec($exp);
		$mod  = $this->bighexdec($mod);

		/* PHP5 finally has this */
		if (function_exists("bcpowmod")) {
			$r = bcpowmod($base, $exp, $mod);
			return $this->bigdechex($r);
		}

		$r = 1;
		$p = $base;

		while (true) {
			if (bcmod($exp, 2)) {
				$r = bcmod(bcmul($p, $r), $mod);
				$exp = bcsub($exp, "1");
				if (bccomp($exp, "0") == 0) {
					return $this->bigdechex($r);
				}
			}
			$exp = bcdiv($exp, 2);
			$p = bcmod(bcmul($p, $p), $mod);
		}
	}

	/*
	* This function returns the binary equivalent postive integer to a given negative
	* integer of arbitrary length. This would be the same as taking a signed negative
	* number and treating it as if it were unsigned. To see a simple example of this
	* on Windows, open the Windows Calculator, punch in a negative number, select the
	* hex display, and then switch back to the decimal display.
	* http://www.hackersquest.com/boards/viewtopic.php?t=4884&start=75
	*/
	function NegativeToUnsigned($value) {
		if (bccomp($value, 0) != -1) {
			return $value;
		}

		$value = bcmul($value, -1);
		$higherValue = 0xFFFFFFFF;

		// We don't know how many bytes the integer might be, so
		// start with one byte and then grow it byte by byte until
		// our negative number fits inside it. This will make the resulting
		// positive number fit in the same number of bytes.
		while (bccomp($value, $higherValue) == 1) {
			$higherValue = bcadd(bcmul($higherValue, 0x100), 0xFF);
		}

		$value = bcadd(bcsub($higherValue, $value), 1);

		return $value;
	}



	// On linux systems, unpack("H*", pack("L*", <value>)) returns differently than on Windows.
	// This can be used instead of unpack/pack to get the value we need.
	// http://www.hackersquest.com/boards/viewtopic.php?t=4884&start=75
	function SafeDecHexReverseEndian($value) {
		$result = "";
		$value = (int)$this->ReduceTo32Bit($value);
		$hex   = substr("00000000".dechex($value),-8);

		$bytes = str_split($hex, 2);

		for ($i = 3; $i >= 0; $i--) {
			$result .= $bytes[$i];
		}

		return $result;
	}

	/*
	* Takes a number and reduces it to a 32-bit value. The 32-bits
	* remain a binary equivalent of 32-bits from the previous number.
	* If the sign bit is set, the result will be negative, otherwise
	* the result will be zero or positive.
	* Function by: Feetus of RK1
	* http://www.hackersquest.com/boards/viewtopic.php?t=4884&start=75
	*/
	function ReduceTo32Bit($value) {
		// If its negative, lets go positive ... its easier to do everything as positive.
		if (bccomp($value, 0) == -1) {
			$value = $this -> NegativeToUnsigned($value);
		}

		$bit  = 0x80000000;
		$bits = array();

		// Find the largest bit contained in $value above 32-bits
		while (bccomp($value, $bit) > -1) {
			$bit    = bcmul($bit, 2);
			$bits[] = $bit;
		}

		// Subtract out bits above 32 from $value
		while (NULL != ($bit = array_pop($bits))) {
			if (bccomp($value, $bit) >= 0) {
				$value = bcsub($value, $bit);
			}
		}

		// Make negative if sign-bit is set in 32-bit value
		if (bccomp($value, 0x80000000) != -1) {
			$value  = bcsub($value, 0x80000000);
			$value -= 0x80000000;
		}

		return $value;
	}


	/* This is 'half' Diffie-Hellman key exchange.
	* 'Half' as in we already have the server's key ($dhY)
	* $dhN is a prime and $dhG is generator for it.
	*
	* http://en.wikipedia.org/wiki/Diffie-Hellman_key_exchange
	*/
	function generate_login_key($servkey, $username, $password) {
		$dhY = "0x9c32cc23d559ca90fc31be72df817d0e124769e809f936bc14360ff4bed758f260a0d596584eacbbc2b88bdd410416163e11dbf62173393fbc0c6fefb2d855f1a03dec8e9f105bbad91b3437d8eb73fe2f44159597aa4053cf788d2f9d7012fb8d7c4ce3876f7d6cd5d0c31754f4cd96166708641958de54a6def5657b9f2e92";
		$dhN = "0xeca2e8c85d863dcdc26a429a71a9815ad052f6139669dd659f98ae159d313d13c6bf2838e10a69b6478b64a24bd054ba8248e8fa778703b418408249440b2c1edd28853e240d8a7e49540b76d120d3b1ad2878b1b99490eb4a2a5e84caa8a91cecbdb1aa7c816e8be343246f80c637abc653b893fd91686cf8d32d6cfe5f2a6f";
		$dhG = "0x5";
		$dhx = "0x".$this->get_random_hex_key(256);

		if (extension_loaded("gmp")) {
			$dhN = gmp_init($dhN);
			$dhX = gmp_strval(gmp_powm($dhG, $dhx, $dhN), 16);
			$dhK = gmp_strval(gmp_powm($dhY, $dhx, $dhN), 16);
		} else if(extension_loaded("bcmath")) {
			$dhX = $this->bcmath_powm($dhG, $dhx, $dhN);
			$dhK = $this->bcmath_powm($dhY, $dhx, $dhN);
		} else {
			die("generate_login_key(): no idea how to powm...\n");
		}

		$str = sprintf("%s|%s|%s", $username, $servkey, $password);

		if (strlen($dhK) < 32) {
			$dhK = str_repeat("0", 32-strlen($dhK)) . $dhK;
		} else {
			$dhK = substr($dhK, 0, 32);
		}

		$prefix = pack("H16", $this->get_random_hex_key(64));
		$length = 8 + 4 + strlen($str); /* prefix, int, ... */
		$pad    = str_repeat(" ", (8 - $length % 8) % 8);
		$strlen = pack("N", strlen($str));

		$plain   = $prefix . $strlen . $str . $pad;
		$crypted = $this->aochat_crypt($dhK, $plain);

		return $dhX . "-" . $crypted;
	}

	function aochat_crypt($key, $str) {
		if (strlen($key) != 32 || strlen($str) % 8 != 0) {
			return false;
		}

		$cycle  = array(0, 0);
		$result = array(0, 0);
		$ret    = "";

		$keyarr  = unpack("V*", pack("H*", $key));
		$dataarr = unpack("V*", $str);

		for ($i = 1; $i <= sizeof($dataarr); $i += 2) {
			$now[0] = (int)$this -> ReduceTo32Bit($dataarr[$i]) ^ (int)$this -> ReduceTo32Bit(@$prev[0]);
			$now[1] = (int)$this -> ReduceTo32Bit($dataarr[$i+1]) ^ (int)$this -> ReduceTo32Bit(@$prev[1]);
			$prev   = $this -> aocrypt_permute($now, $keyarr);

			$ret .= $this -> SafeDecHexReverseEndian($prev[0]);
			$ret .= $this -> SafeDecHexReverseEndian($prev[1]);
		}

		return $ret;
	}

	function aocrypt_permute($x, $y) {
		$a = $x[0];
		$b = $x[1];
		$c = 0;
		$d = (int)0x9e3779b9;
		for ($i = 32; $i-- > 0;) {
			$c  = (int)$this -> ReduceTo32Bit($c + $d);
			$a += (int)$this -> ReduceTo32Bit((int)$this -> ReduceTo32Bit(((int)$this -> ReduceTo32Bit($b) << 4 & -16) + $y[1]) ^ (int)$this -> ReduceTo32Bit($b + $c)) ^ (int)$this -> ReduceTo32Bit(((int)$this -> ReduceTo32Bit($b) >> 5 & 134217727) + $y[2]);
			$b += (int)$this -> ReduceTo32Bit((int)$this -> ReduceTo32Bit(((int)$this -> ReduceTo32Bit($a) << 4 & -16) + $y[3]) ^ (int)$this -> ReduceTo32Bit($a + $c)) ^ (int)$this -> ReduceTo32Bit(((int)$this -> ReduceTo32Bit($a) >> 5 & 134217727) + $y[4]);
		}
		return array($a, $b);
	}
}

?>
