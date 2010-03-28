<? 
$ipreset_txt = "<header>::::: Guide to IP Reset Points :::::<end>\n\n"; 
$ipreset_txt = "<font color = blue>-= IP Resets =-</font>

Before I begin on this section, please read and understand the following:

IP Reset points provide the only method for you to change a character. This specifically relates to high level characters since those require the most work.

The game does change, the rules are rewritten on a regular basis and you cannot say for sure that the choices you make now will still be the best a year from now. If you have used all your IP Reset points you will have no way at all to adapt.

In many instances it is better to re-roll a character than use one of these points (before level 100, for instance). It is also adviseable to save these until you hit TL6 as in end-game, you will be looking to retwink yourself into your choosen equipment.

If you're short in IP for a particular skill it is always better to level if you can rather than use one of these points.

There is no mechanism for getting more of these points.

It is possible to reset skills if you need to. Every character has access to a fixed amount of IP Reset Points, and you'll get a few more every time you hit a title change (but not at level 205).

Title Changes at levels: <font color = red>15, 50, 100, 150, 190 </font>and <font color = red>205</font>

Each point allows you to reset a single skill, and returns all of the IP you have spent in that skill allowing you to spend it elsewhere.

To do this you have to remove all weapons, equipment, armour and implants.

The Map Navigation skill cannot be reset. ";

$ipreset_txt = bot::makeLink("Guide to IP Reset Points", $ipreset_txt); 
if($type == "msg") 
bot::send($ipreset_txt, $sender); 
elseif($type == "all") 
bot::send($ipreset_txt); 
else 
bot::send($ipreset_txt, "guild"); 
?>