<?php
//if (eregi ("^raffle (<a href=\"itemref:\/\/[0-9]+\/[0-9]+\/[0-9]+\">.+<\/a>)$", $message, $arr)) {
if (eregi("^raffle (.+) ([0-9]+)$", $message, $arr) || eregi("^raffle (.+)$", $message, $arr)) {
	if (!$this->vars["Raffles"]["inprog"]) {
	    $minutes = $this->settings["defaultraffletime"];
	    if ($arr[2]) {
			$minutes = $arr[2];
	    }
	    
        $item = str_replace('"', "'", $arr[1]);
        $this->vars["Raffles"] = array(
            "inprog" => 1,
            "owner" => $sender,
            "item" => $item,
            "time" => time() +  $minutes * 60,
            "rafflees" => array()
        );
        
        $jnRflMsg = "<header>:::::Raffle Controls:::::<end>
<white>A raffle for $item has been started by $sender!<end>

Click <a href='chatcmd:///tell <myname> joinRaffle'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> leaveRaffle'>here</a> if you wish to leave the raffle.";
        $link = bot::makeLink("here", $jnRflMsg);
        $msg = "
-----------------------------------------------------------------------
A raffle for $item has been started by $sender!
Click $link to join the raffle. Raffle will end in '$minutes Minutes'.
-----------------------------------------------------------------------";
        bot::send($msg, "guild");
    }
    else {
        $msg = "There is already a raffle in progress.";
        if($type == "msg")
            bot::send($msg, $sender);
        elseif($type == "priv")
	        bot::send($msg, "priv");
        elseif($type == "guild")
            bot::send($msg, "guild");
    }
}
else {
    $msg = "You need to specify an item to be raffled!";
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
        bot::send($msg, "priv");
    elseif($type == "guild")
        bot::send($msg, "guild");
}
?>