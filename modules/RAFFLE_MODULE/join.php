<?php
if (eregi ("^joinRaffle", $message, $arr))
{
          //check inprog and check not already in raffle
        if($this->vars["Raffles"]["inprog"]and array_search($sender,$this->vars["Raffles"]["rafflees"])===false)
        {
        $this->vars["Raffles"]["rafflees"][]=$sender;
        $msg="$sender has entered the raffle.";
        bot::send($msg, "guild");
        }
        else
        {
        $msg="Either a raffle is not in progress, or you are already in the raffle.";
        bot::send($msg, $sender);
        }
}
?>