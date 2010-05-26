<?php
//if (eregi ("^raffle (<a href=\"itemref:\/\/[0-9]+\/[0-9]+\/[0-9]+\">.+<\/a>)$", $message, $arr)) {
if (preg_match("/^raffle (.+) ([0-9]+)$/i", $message, $arr) || preg_match("/^raffle (.+)$/i", $message, $arr)) {
	if (!$this->vars["Raffles"]["inprog"]) {
		$item = $arr[1];
	    $minutes = $this->settings["defaultraffletime"];
	    if ($arr[2]) {
			$minutes = $arr[2];
	    }
	    
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
        bot::send($msg, "org");
    }
    else {
        $msg = "There is already a raffle in progress.";
        bot::send($msg, $sendto);
    }
}
else {
    $msg = "You need to specify an item to be raffled!";
    bot::send($msg, $sendto);
}
?>