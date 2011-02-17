<?php

if (preg_match("/^raffle start (\d+) (.+)$/i", $message, $arr))
{
    if ("msg" == $type)
    {
        $msg = "You can't start a raffle in tells, please use org-chat or private channel.";
        $chatBot->send($msg, $sendto);
        return;
    }

    if ($chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is already a raffle in progress.";
        $chatBot->send($msg, $sendto);
        return;
    }

    $item = $arr[2];
    $count = $arr[1];
    $minutes = $chatBot->settings["defaultraffletime"];

    $chatBot->vars["Raffles"] = array(
        "running" => true,
        "owner" => $sender,
        "item" => $item,
        "count" => $count,
        "time" => time() +  $minutes * 60,
        "rafflees" => array(),
        "lastresult" => NULL,
        "sendto" => $sendto
    );

    $jnRflMsg = "<header>:::::Raffle Controls:::::<end>
<white>A raffle for $item (count: $count) has been started by $sender!<end>

Click <a href='chatcmd:///tell <myname> <symbol>raffle join'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> <symbol>raffle leave'>here</a> if you wish to leave the raffle.";
        $link = Text::make_link("here", $jnRflMsg);
        $msg = "
-----------------------------------------------------------------------
A raffle for $item (count: $count) has been started by $sender!
Click $link to join the raffle. Raffle will end in $minutes minutes.
-----------------------------------------------------------------------";

        $chatBot->vars["Raffles"]["lastmsgtime"] = time();
        $chatBot->send($msg, $sendto);
}

elseif (preg_match("/^raffle start (.+)$/i", $message, $arr))
{
    if ("msg" == $type)
    {
        $msg = "You can't start a raffle in tells, please use org-chat or private channel.";
        $chatBot->send($msg, $sendto);
        return;
    }
    if ($chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is already a raffle in progress.";
        $chatBot->send($msg, $sendto);
        return;
    }

    $item = $arr[1];
    $count = 1;
    $minutes = $chatBot->settings["defaultraffletime"];

    $chatBot->vars["Raffles"] = array(
        "running" => true,
        "owner" => $sender,
        "item" => $item,
        "count" => $count,
        "time" => time() +  $minutes * 60,
        "rafflees" => array(),
        "lastresult" => NULL,
        "sendto" => $sendto
    );

    $jnRflMsg = "<header>:::::Raffle Controls:::::<end>
<white>A raffle for $item has been started by $sender!<end>

Click <a href='chatcmd:///tell <myname> <symbol>raffle join'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> <symbol>raffle leave'>here</a> if you wish to leave the raffle.";
        $link = Text::make_link("here", $jnRflMsg);
        $msg = "
-----------------------------------------------------------------------
A raffle for $item has been started by $sender!
Click $link to join the raffle. Raffle will end in $minutes minutes'.
-----------------------------------------------------------------------";

        $chatBot->vars["Raffles"]["lastmsgtime"] = time();
        $chatBot->send($msg, $sendto);
}

elseif (preg_match("/^raffle cancel$/i", $message, $arr))
{
    if (!$chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is no active raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    if (($chatBot->vars["Raffles"]["owner"] != $sender) && (!isset($chatBot->admins[$sender])))
    {
         $msg = "<highlight>Only the owner or admins may cancel the raffle.";
         $chatBot->send($msg, $sendto);
         return;
    }
    $sendtobuffer = $chatBot->vars["Raffles"]["sendto"];
    $chatBot->vars["Raffles"] = array(
        "running" => false,
        "owner" => NULL,
        "item" => NULL,
        "count" => NULL,
        "time" => NULL,
        "rafflees" => NULL,
        "lastresult" => "The last raffle was cancelled.",
        "lastmsgtime" => NULL,
        "sendto" => $sendtobuffer
        );

    $msg = "<highlight>The raffle was cancelled.<end>";
    $chatBot->send($msg, $chatBot->vars["Raffles"]["sendto"]);
}

elseif (preg_match("/^raffle end$/i", $message, $arr))
{
    if (!$chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is no active raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    if (($chatBot->vars["Raffles"]["owner"] != $sender) && (!isset($chatBot->admins[$sender])))
    {
         $msg = "<highlight>Only the owner or admins may end the raffle.";
         $chatBot->send($msg, $sendto);
         return;
    }
    
    endraffle($this);
    
}

elseif (preg_match("/^raffle result$/i", $message, $arr))
{
    if (!isset ($chatBot->vars["Raffles"]["lastresult"]))
    {
        $msg = "<highlight>Last raffles result could not be retrieved.";
        $chatBot->send($msg, $sendto);
        return;
    }

    $chatBot->send("Last raffle result: ".$chatBot->vars["Raffles"]["lastresult"], $sendto);
}

elseif (preg_match("/^raffle join$/i", $message, $arr))
{
    if (!$chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is no active raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    if (isset( $chatBot->vars["Raffles"]["rafflees"][$sender])) {
        $msg = "<highlight>You are already in the raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    $chatBot->vars["Raffles"]["rafflees"][$sender] = 0;
    $msg = "$sender has entered the raffle.";
    $chatBot->send($msg, $chatBot->vars["Raffles"]["sendto"]);

}

elseif (preg_match("/^raffle leave$/i", $message, $arr))
{
    if (!$chatBot->vars["Raffles"]["running"])
    {
        $msg = "<highlight>There is no active raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    if (!isset( $chatBot->vars["Raffles"]["rafflees"][$sender])) {
        $msg = "You are not currently signed up for the raffle.";
        $chatBot->send($msg, $sendto);
        return;
    }

    unset($chatBot->vars["Raffles"]["rafflees"][$sender]);
    $msg = "$sender has left the raffle.";
    $chatBot->send($msg, $chatBot->vars["Raffles"]["sendto"]);

}
else {
   $syntax_error = true;
}
?>