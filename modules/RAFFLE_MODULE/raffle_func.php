<?php
function endraffle(&$bot)
{
    // just to make sure there is a raffle to end
    if (!$bot->vars["Raffles"]["running"])
    {
        return;
    }
    // indicate that the raffle is over
    $bot->vars["Raffles"]["running"] = false;

    $item = $bot->vars["Raffles"]["item"];
    $count = $bot->vars["Raffles"]["count"];
    $rafflees = array_keys($bot->vars["Raffles"]["rafflees"]);
    $rafflees_num = count($rafflees);

    if (0 == $rafflees_num)
    {
        $msg = "<highlight>No one joined the raffle, $item is free for all.";
        $bot->vars["Raffles"]["lastresult"] = $msg;

        $bot->send($msg, $bot->vars["Raffles"]["sendto"]);
        return;
    }

    // first shuffle the names
    for ($i = 0; $i < 5; $i++)
    {
            shuffle($rafflees);
    }
    // roll multiple times to generate a list of winners
    $rollcount = 1000 * $rafflees_num;

    for ($i = 0; $i < $rollcount; $i++)
    {
        // roll a name out of the rafflees and add a rollcount
        $random_name = $rafflees[mt_rand(0, $rafflees_num -1)];
        $bot->vars["Raffles"]["rafflees"][$random_name] ++;
    }

    // sort the list depending on roll results
    arsort($bot->vars["Raffles"]["rafflees"]);

    $blob = "<header>Raffle results<end>\n";
    if (1 == $count)
    {
        $blob .= "Rolled $rollcount times for $item.\n \n Winner:";
    }
    else
    {
        $blob .= "Rolled $rollcount times for $item (count: $count).\n \n Winners:";
    }

    $i = 0;
    foreach ($bot->vars["Raffles"]["rafflees"] as $char => $rolls) {
        $i++;
        $blob .= "\n$i. $char got $rolls rolls.";
        if ($i == $count)
        {
            $blob .= "\n-------------------------\n Unlucky:";
        }
    }
    $results = $bot->makeLink("Detailed results", $blob);

    if (1 == $count)
    {
        $msg = "The raffle for $item is over. Winner: ";
    }
    else
    {
       $msg = "The raffle for $item (count: $count) is over. Winners: ";
    }

    $i = 0;
    foreach ($bot->vars["Raffles"]["rafflees"] as $char => $rolls) {
        $i++;
        $msg .= "$char";
        if ($i != $count)
        {
            $msg .= ", ";
        }
    }
    $msg .= " Congratulations. $link";
    $bot->vars["Raffles"]["lastresult"] = $msg;
    $bot->send($msg, $bot->vars["Raffles"]["sendto"]);
}

?>
