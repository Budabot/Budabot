<?php
   /*
   ** Author: Neksus (RK2)
   ** Description: Makes a blob with Title levels and the IP gained
   ** Version: 0.2
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 02.09.2006
   ** Date(last modified): 05.09.2006
   ** 
   */

$title="<header>::::: Title Levels :::::<end>
<red>1:<end> Level 1 (5K IP/level)
<red>2:<end> Level 15 (10K IP/level)
<red>3:<end> Level 50 (20K IP/level)
<red>4:<end> Level 100 (40K IP/level)
<red>5:<end> Level 150 (80K IP/level)
<red>6:<end> Level 190 (150K IP/level)
<red>7:<end> Level 205 (400K IP/level)";
	
if (preg_match("/^title$/i", $message)) {
	$text = $title;
	$windowlink = bot::makeLink("Title levels", $text);
	bot::send($windowlink, $sendto);
}
?>