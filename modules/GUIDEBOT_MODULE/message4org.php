<?php
$message1_txt = "<header>::::: A Message From Plugsz  :::::<end>\n\n"; 
$message1_txt = "Newcomers Alliance is dedicated to helping new players and having fun. We may be a growing org, but our reputation does preceed us. Each member is expected to act as a representative of the org, and act accordingly. With that in mind:

Please do not train mobs onto other players intentionally, or do anything that may ruin their fun.

Please do not say something to another person that could be offensive or rude. Think before you type!

If you are having a problem ingame with someone, please ask them to stop what they are doing, then contact a General in the organization immediately. They will mediate the matter and decide what to do. The official position on harassment and objectionable behavior can be found here: http://community.anarchy-online.com/content/corporate/rulesofconduct.html

Do not beg for credits, gear, teams, etc.....Use the general rule of thumb: Send out your request and someone will answer.

It is not my wish to make up a bunch of rules or anything. Just please try to show respect for the other players in the game.  Thank You!
 
                                       -Plugsz , President of Newcomers Alliance


Also, We have been given a place in AOFroobs.com: http://www.aofroobs.com/
to talk about Newcomers Alliance! Signup for the forums and post a hello! .And remember we have guides for AO <a href='chatcmd:///tell <myname> guides'</a>Guides</a>";

$message1_txt = bot::makeLink("A Message from Plugsz", $message1_txt); 
if($type == "msg") 
bot::send($message1_txt, $sender); 
elseif($type == "all") 
bot::send($message1_txt); 
else 
bot::send($message1_txt, "guild"); 
?>