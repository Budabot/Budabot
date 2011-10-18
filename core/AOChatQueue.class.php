<?php

/*
* $Id: aochat.php,v 1.1 2006/12/08 15:17:54 genesiscl Exp $
*
* Modified to handle the recent problem with the integer overflow.
*
* Copyright (C) 2002-2005  Oskari Saarenmaa <auno@auno.org>.
*
* AOChat, a PHP class for talking with the Anarchy Online chat servers.
* It requires the sockets extension (to connect to the chat server)
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

class AOChatQueue {

	var $dfunc, $queue, $qsize;
	var $point, $limit, $inc;

	function AOChatQueue($cb, $limit, $inc) {
		$this->dfunc = $cb;
		$this->limit = $limit;
		$this->inc = $inc;
		$this->point = 0;
		$this->queue = array();
		$this->qsize = 0;
	}

	function push($priority) {
		$args = array_slice(func_get_args(), 1);
		$now = time();
		if ($this->point <= ($now + $this->limit)) {
			call_user_func_array($this->dfunc, $args);
			$this->point = (($this->point<$now) ? $now : $this->point) + $this->inc;
			return 1;
		}
		if (isset($this->queue[$priority])) {
			$this->queue[$priority][] = $args;
		} else {
			$this->queue[$priority] = array($args);
			krsort($this->queue);
		}
		$this->qsize++;
		return 2;
	}

	function run() {
		if ($this->qsize === 0) {
			return 0;
		}
		$now = time();
		if ($this->point < $now) {
			$this->point = $now;
		} else if($this->point > ($now + $this->limit)) {
			return 0;
		}
		$processed = 0;
		forEach (array_keys($this->queue) as $priority) {
			while (true) {
				$item = array_shift($this->queue[$priority]);
				if ($item === NULL) {
					unset($this->queue[$priority]);
					break;
				}
				call_user_func_array($this->dfunc, $item);
				$this->point += $this->inc;
				$processed++;
				if ($this->point > ($now + $this->limit)) {
					break(2);
				}
			}
		}
		$this->qsize -= $processed;
		return $processed;
	}
}

?>