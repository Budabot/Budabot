<?php
if (eregi ("^leaveRaffle", $message, $arr))
{
          //check inprog and check if already in raffle
        if($this->vars["Raffles"]["inprog"])
        {
        $index=array_search($sender,$this->vars["Raffles"]["rafflees"]);
               if($index===false)
               {
                   $msg="You are not currently signed up for the raffle.";
                   bot::send($msg, $sender);
               }
               else
               {
                  array_splice($this->vars["Raffles"]["rafflees"],$index,1);
                  $msg="$sender has left the raffle.";
                  bot::send($msg, "guild");
               }
        }
        else
        {
        $msg="A raffle is not in progress.";
        bot::send($msg, $sender);
        }
}
?>