<?php

if (preg_match("/^flip$/i", $message)) {
	$row = $db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - 30);
	if ($row === null) {
	  	$flip = rand(1, 2);
		$db->exec("INSERT INTO roll (`time`, `name`, `type`, `result`) VALUES (?, ?, ?, ?)", time(), $sender, 0, $flip);
		$ver_num = $db->lastInsertId();
	  	if ($flip == 1) {
	  		$msg = "The coin landed <highlight>heads<end>, to verify do /tell <myname> verify $ver_num";
	  	} else {
		  	$msg = "The coin landed <highlight>tails<end>, to verify do /tell <myname> verify $ver_num";
		}
	} else {
  		$msg = "You can only flip or roll once every 30 seconds.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^roll ([0-9]+)$/i", $message, $arr)) {
  	if ($arr[1] > getrandmax()) {
		$msg = "The maximum number that the roll number can be is <highlight>".getrandmax()."<end>";
	} else {
		$row = $db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - 30);
		if ($row === null) {
		  	$num = rand(1, $arr[1]);
			$db->exec("INSERT INTO roll (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (?, ?, ?, ?, ?, ?)", time(), $sender, 1, 1, $arr[1], $num);
		  	$ver_num = $db->lastInsertId();
			$msg = "Between 1 and $arr[1] I rolled a $num, to verify do /tell <myname> verify $ver_num";
		} else {
	  		$msg = "You can only flip or roll once every 30 seconds.";
		}
	}
	  	
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^roll ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
  	if ($arr[2] >= getrandmax()) {
		$msg = "The maximum number that the roll number can be is <highlight>".getrandmax()."<end>";
	} else if ($arr[1] >= $arr[2]) {
		$msg = "The first number can't be higher than or equal to the second number.";
	} else {
		$row = $db->queryRow("SELECT * FROM roll WHERE `name` = ? AND `time` >= ? LIMIT 1", $sender, time() - 30);
		if ($row === null) {
		  	$num = rand($arr[1], $arr[2]);
			$db->exec("INSERT INTO roll (`time`, `name`, `type`, `start`, `end`, `result`) VALUES (?, ?, ?, ?, ?, ?)", time(), $sender, 1, $arr[1], $arr[2], $num);
			$ver_num = $db->lastInsertId();
			$msg = "Between $arr[1] and $arr[2] I rolled a $num, to verify do /tell <myname> verify $ver_num";
		} else {
	  		$msg = "You can only flip or roll once every 30 seconds.";
		}
	}
	
    $chatBot->send($msg, $sendto);
} else if (preg_match("/^verify ([0-9]+)$/i", $message, $arr)) {
	$row = $db->queryRow("SELECT * FROM roll WHERE `id` = ?", $arr[1]);
	if ($row === null) {
		$msg = "That verify number doesn't exist.";
	} else {
	  	$time = time() - $row->time;
	  	$msg = "$time seconds ago I told <highlight>$row->name<end>: ";
	  	if ($row->type == 0) {
		    if ($row->result == 1) {
		    	$msg .= "The coin landed <highlight>heads<end>";
		    } else {
		    	$msg .= "The coin landed <highlight>tails<end>";
			}
		} else {
	  		$msg .= "Between $row->start and $row->end I rolled a <highlight>$row->result<end>";
		}
	}
	
    $chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>