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

/* Prioritized chat message queue. */

define('AOC_PRIORITY_HIGH',           1000);
define('AOC_PRIORITY_MED',             500);
define('AOC_PRIORITY_LOW',             100);

class AOChatQueue {

	var $queue;
	var $qsize;  // the number of items in the queue for any priority
	var $point;  // everytime a message is sent, this is incremented by $increment; if $point > (time() + $limit) metering kicks in
	var $limit;  // the amount of messages that can be sent before metering kicks in
	var $increment;  // the amount of time in seconds to wait after the limit has been reached

	function AOChatQueue($limit, $increment) {
		$this->limit = $limit;
		$this->increment = $increment;
		$this->point = 0;
		$this->queue = array();
		$this->qsize = 0;
	}

	function push($priority, $item) {
		$now = time();

		if (isset($this->queue[$priority])) {
			$this->queue[$priority][] = $item;
		} else {
			$this->queue[$priority] = array($item);
			krsort($this->queue);
		}
		$this->qsize++;
	}

	function getNext() {
		if ($this->qsize === 0) {
			return null;
		}
		$now = time();
		if ($this->point < $now) {
			$this->point = $now;
		} else if ($this->point > ($now + $this->limit)) {
			return null;
		}

		forEach (array_keys($this->queue) as $priority) {
			while (true) {
				$item = array_shift($this->queue[$priority]);
				if ($item === NULL) {
					unset($this->queue[$priority]);
					break;
				}

				$this->point += $this->increment;
				$this->qsize--;
				return $item;
			}
		}
		return null;
	}
}

?>