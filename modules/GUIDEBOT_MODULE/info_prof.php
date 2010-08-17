<?php
$blob = "<header>:::::: Professions Guides :::::<end>\n\n
(note: all this information was taken from the official AO forums and should not be taken as a absolute rulebook. These are people's opinions,and you should play your character however you want. Just have fun. :P )

<a href='chatcmd:///tell <myname> advys'>Guide To Adventurers</a> 
<a href='chatcmd:///tell <myname> agents'>Guide To Agents</a> 
<a href='chatcmd:///tell <myname> bureaucrats'>Guide To Bureaucrats</a> 
<a href='chatcmd:///tell <myname> doctors'>Guide To Doctors</a> 
<a href='chatcmd:///tell <myname> enforcers'>Guide To Enforcers</a> 
<a href='chatcmd:///tell <myname> engineers'>Guide To Engineers</a> 
<a href='chatcmd:///tell <myname> fixers'>Guide To Fixers</a> 
<a href='chatcmd:///tell <myname> mas'>Guide To Martial Artists</a> 
<a href='chatcmd:///tell <myname> metaps'>Guide To Meta Physicist</a> 
<a href='chatcmd:///tell <myname> nts'>Guide To Nano Technicians</a> 
<a href='chatcmd:///tell <myname> soldiers'>Guide To Soldiers</a> 
<a href='chatcmd:///tell <myname> traders'>Guide To Traders</a>  ";

$msg = bot::makeLink("Guide to Professions", $blob); 
bot::send($msg, $sendto);
?>