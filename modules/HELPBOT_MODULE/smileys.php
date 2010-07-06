<?php
   /*
   ** Author: Neksus (RK2)
   ** Description: Makes a blob with smileys
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13.09.2006
   ** Date(last modified): 03.03.2007
   ** 
   */
$basic="<header>::::: Smiley Dictionary: Basic Smileys :::::<end>
<yellow>:-)<end> <green>Your basic smiley<end>
This smiley is used to inflect a sarcastic or joking statement since we can't hear voice inflection over e-mail

<yellow>;-)<end> <green>Winky smiley<end>
User just made a flirtatious and/or sarcastic remark. More of a 'don't hit me for what I just said' smiley

<yellow>:-(<end> <green>Frowning smiley<end>
User did not like that last statement or is upset or depressed about something

<yellow>:-I<end> <green>Indifferent smiley<end>
Better than a <yellow>`:-(<end> but not quite as good as a <yellow>`:-)<end>

<yellow>:-><end>
User just made a really biting sarcastic remark. Worse than a <yellow>`;-)<end>

<yellow>>:-><end>
User just made a really devilish remark

<yellow>>;-><end> <green>Winky and devil combined<end>
A very lewd remark was just made";

$wide="<header>::::: Smiley Dictionary: Widely used Smileys :::::<end>
<yellow>(-:<end>
User is left handed. 

<yellow>%-)<end>
User has been staring at a green screen for 15 hours straight. 

<yellow>:*)<end>
User is drunk. 

<yellow>[:]<end>
User is a robot. 

<yellow>8-)<end>
User is wearing sunglasses. 

<yellow>::-)<end>
User wears normal glasses. 

<yellow>8:-)<end>
User is a little girl. 

<yellow>:-)-8<end>
User is a Big girl. 

<yellow>:-[<end>
User is a vampire. 

<yellow>:-E<end>
Bucktoothed vampire. 

<yellow>:-F<end>
Bucktoothed vampire with one tooth missing. 

<yellow>:-7<end>
User just made a wry statement. 

<yellow>:-*<end>
User just ate something sour. 

<yellow>:-)~<end>
User drools. 

<yellow>:-~)<end>
User has a cold. 

<yellow>:'-(<end>
User is crying. 

<yellow>:'-)<end>
User is so happy, s/he is crying. 

<yellow>:-@<end>
User is screaming. 

<yellow>|-I<end>
User is asleep. 

<yellow>|-O<end>
User is yawning/snoring. 

<yellow>O :-)<end>
User is an angel (at heart, at least). 

<yellow>:-S<end>
User just made an incoherent statement. 

<yellow>:-D<end>
User is laughing (at you!) 

<yellow>:-X<end>
User's lips are sealed. 

<yellow>:-C<end>
User is really bummed. 

<yellow>:-/<end>
User is skeptical. 

<yellow>:-o<end>
Uh oh! 

<yellow>:-9<end>
User is licking his/her lips. 

<yellow>%-6<end>
User is braindead. 

<yellow>X-(<end>
User just died. ";
	
if(preg_match("/^smileys$/i", $message)) {
	$basiclink = $this->makeLink("Basic Smileys", $basic) ;
	$widelink = $this->makeLink("Widely Used Smileys", $wide);

	$this->send($basiclink, $sendto);
	$this->send($widelink, $sendto);
}
?>