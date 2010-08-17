<?php
$blob = "<header>::::: Guide to Unique NPCs :::::<end>\n\n
These NPCs may not be the easiest to find, but their wares are out of the ordinary and certainly worth the trip,if at least to get a refreshing change of scenery.

<a href='chatcmd:///tell <myname> zoftig'>'Zoftig Blimp'</a> 
<a href='chatcmd:///tell <myname> meleesmith'>'The Tsunayoshi Smith'</a> 
<a href='chatcmd:///tell <myname> fshop'>'The Fixer Shop'</a> 
<a href='chatcmd:///tell <myname> bernice'>'Thin Bernice'</a> 
<a href='chatcmd:///tell <myname> tshop'>'Trader Shops'</a>  
";
$msg = bot::makeLink("Guide to Unique NPCs", $blob); 
bot::send($msg, $sendto);
?>