<? 
$grid_txt = "<header>::::: Guide to The Grid  :::::<end>\n\n"; 
$grid_txt = "<font color = blue>-= The Grid =-</font>

The grid is a fast way to teleport to other locations within Rubi-Ka.  Fixers have their own grid, but the public grid is available for all characters to use.  The Grid requires a certain amount of Computer Literacy to exit or enter the various grid terminals.  There are three floors to the Grid, each with different ranges of Computer Literacy needed:

<font color = blue>First Floor</font>
<font color = red>*</font>0 : Emergency Exit
<font color = red>*</font>0 : Organization Exit
<font color = red>*</font>75 : Omni-1 Trade
<font color = red>*</font>80 : Old Athens
<font color = red>*</font>80 : Tir
<font color = red>*</font>90 : Borealis
<font color = red>*</font>90 : Newland

<font color = blue>Second Floor</font>
<font color = red>*</font>75 : Omni-1 Entertainment
<font color = red>*</font>80 : 2HO (Stret East Bank)
<font color = red>*</font>90 : Meetmedere (Newland Desert)
<font color = red>*</font>100 : Rome Red
<font color = red>*</font>120 : Galway
<font color = red>*</font>120 : Harry's
<font color = red>*</font>120 : Lush Hills (Resort)
<font color = red>*</font>130 : (West) Athens
<font color = red>*</font>150 : Clondyke
<font color = red>*</font>180 : 4 Holes
<font color = red>*</font>250 : Broken Shores

<font color = blue>Third Floor</font>
<font color = red>*</font>150 Omni-1 HQ
<font color = red>*</font>450 Camelot (Avalon)
<font color = red>*</font>440 Sentinels (Mort)

<font color = red>*</font> denotes CompLit needed to use the exit.
 ";

$grid_txt = bot::makeLink("Guide to The Grid", $grid_txt); 
if($type == "msg") 
bot::send($grid_txt, $sender); 
elseif($type == "all") 
bot::send($grid_txt); 
else 
bot::send($grid_txt, "guild"); 
?>