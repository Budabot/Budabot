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

/* New "extended" messages, parser and abstraction.
* These were introduced in 16.1.  The messages use postscript
* base85 encoding (not ipv6 / rfc 1924 base85).  They also use
* some custom encoding and references to further confuse things.
*
* Messages start with the magic marker ~& and end with ~
* Messages begin with two base85 encoded numbers that define
* the category and instance of the message.  After that there
* are an category/instance defined amount of variables which
* are prefixed by the variable type.  A base85 encoded number
* takes 5 bytes.  Variable types:
*
* s: string, first byte is the length of the string
* i: signed integer (b85)
* u: unsigned integer (b85)
* f: float (b85)
* R: reference, b85 category and instance
* F: recursive encoding
* ~: end of message
*
*/

class AOExtMsg {

	public $args, $category, $instance, $message_string, $message;

	function __construct($str) {
		$this->read($str);
	}

	function read($msg) {
		if (empty($msg) || substr($msg, 0, 2) != "~&" || substr($msg, -1) != "~") {
			return false;
		}
		$msg = substr($msg, 2, -1);
		$this->category = $this->b85g($msg);
		$this->instance = $this->b85g($msg);

		$this->args = AOExtMsg::parse_params($msg);
		if ($this->args === null) {
			echo "Error parsing parameters for category: '$this->category' instance: '$this->instance' string: '$msg'\n";
		} else {
			$this->message_string = MMDBParser::get_message_string($this->category, $this->instance);
			if ($this->message_string !== null) {
				$this->message = vsprintf($this->message_string, $this->args);
			}
		}
	}

	public static function parse_params($msg) {
		$args = array();
		while ($msg != '') {
			$data_type = $msg[0];
			$msg = substr($msg, 1); // skip the data type id
			switch ($data_type) {
				case "S":
					$len = ord($msg[0]) * 256 + ord($msg[1]);
					$str = substr($msg, 2, $len);
					$msg = substr($msg, $len + 2);
					$args[] = $str;
					break;

				case "s":
					$len = ord($msg[0]) - 1;
					$str = substr($msg, 1, $len);
					$msg = substr($msg, $len + 1);
					$args[] = $str;
					break;

				case "I":
					$array = unpack("N", $msg);
					$args[] = $array[1];
					$msg = substr($msg, 4);
					break;

				case "i":
				case "u":
					$num = AOExtMsg::b85g($msg);
					$args[] = $num;
					break;

				case "R":
					$cat = AOExtMsg::b85g($msg);
					$ins = AOExtMsg::b85g($msg);
					$str = MMDBParser::get_message_string($cat, $ins);
					if ($str === null) {
						$str = "Unknown ($cat, $ins)";
					}
					$args[] = $str;
					break;

				case "l":
					$array = unpack("N", $msg);
					$msg = substr($msg, 4);
					$cat = 20000;
					$ins = $array[1];
					$str = MMDBParser::get_message_string($cat, $ins);
					if ($str === null) {
						$str = "Unknown ($cat, $ins)";
					}
					$args[] = $str;
					break;

				default:
					echo "Error! could not parse argument: '$data_type'\n";
					return null;
					break;
			}
		}

		return $args;
	}

	public static function b85g(&$str) {
		$n = 0;
		for ($i = 0; $i < 5; $i++) {
			$n = $n * 85 + ord($str[$i]) - 33;
		}
		$str = substr($str, 5);
		return $n;
	}
}

?>
