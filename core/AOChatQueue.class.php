<?php

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