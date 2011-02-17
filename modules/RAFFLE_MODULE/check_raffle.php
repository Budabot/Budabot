<?php


{
if (!$chatBot->vars["Raffles"]["running"])
{
    // no raffle running, do nothing
    return;
}

// there is a raffle running
$tleft = $chatBot->vars["Raffles"]["time"] - time();
$owner = $chatBot->vars["Raffles"]["owner"];
$item = $chatBot->vars["Raffles"]["item"];
$count = $chatBot->vars["Raffles"]["count"];

$timesincelastmsg = time() - $chatBot->vars["Raffles"]["lastmsgtime"];

// if there is no time left or we even skipped over the time, end raffle
if (0 >= $tleft)
{
    endraffle($this);
    return;
}

// at least 15 seconds should be between messages
if (15 >= $timesincelastmsg)
{
    return;
}

// generate an info window
$blob="<white>Current Members:<end>";
forEach (array_keys($chatBot->vars["Raffles"]["rafflees"]) as $tempName) {
    $blob .= "\n$tempName";
}
if (count($chatBot->vars["Raffles"]["rafflees"]) == 0) {
    $blob .= "No entrants yet.";
}

$blob .= "

Click <a href='chatcmd:///tell <myname> <symbol>raffle join'>here</a> to join the raffle!
Click <a href='chatcmd:///tell <myname> <symbol>raffle leave'>here</a> if you wish to leave the raffle.";

$blob .= "\n\n Time left: $tleft seconds.";

$link = Text::make_link("here", $blob);
if (1 < $count)
{
    $msg = "<yellow>Reminder:<end> Raffle for $item (count: $count) has $tleft seconds left. Click $link to join.";
}
else
{
    $msg = "<yellow>Reminder:<end> Raffle for $item has $tleft seconds left. Click $link to join.";
}
// raffle is running quite some time, post only once a minute
if ((60 <= $tleft) && (60 <= $timesincelastmsg))
{
    $chatBot->vars["Raffles"]["lastmsgtime"] = time();
    $chatBot->send($msg, $chatBot->vars["Raffles"]["sendto"]);
}
elseif ((60 > $tleft) && (15 <= $timesincelastmsg))
{
    $chatBot->vars["Raffles"]["lastmsgtime"] = time();
    $chatBot->send($msg, $chatBot->vars["Raffles"]["sendto"]);
}

}
?>