<?php
$blob = "<header>::::: Locations of Yalms  :::::<end>\n\n
<highlight>-=Yalm Shops=-</end>
OT Advanced Vehicle shops can be found at: 

<font color = #31D6FF>413x289   </font> Rome Green, 
<font color = #31D6FF>545x290   </font> Rome Blue, 
<font color = #31D6FF>862x438   </font> Omni-1 Entertainment, 
<font color = #31D6FF>334x202   </font> Trade District, 
<font color = #31D6FF>576x222   </font> Trade District, 
<font color = #31D6FF>3045x3033 </font> Harry's,
<font color = #31D6FF>653x575   </font> Borealis
<font color = #31D6FF>1526x541  </font> Lush Fields
<font color = #31D6FF>289x315   </font> Newland City (Shop right OTW to Newland Shops) 
<font color = #31D6FF>1190.7, 2352.4</font> Pleasant Meadows (20K) east of the shops";
$msg = bot::makeLink("Locations of Yalms", $blob); 
bot::send($msg, $sendto);
?>