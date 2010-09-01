<?php
$blob = 
"<header>Solitus<end><orange>
Ability    Rubi-ka  / Shadowlands<end>
 
Strength        472 / 772  
Agility           480 / 780  
Stamina         480 / 780  
Intelligence    480 / 780  
Sense           480 / 780  
Psychic         480 / 780
Max % Add Nano Cost   -50%
3 max health per 1 point of hp
3 max nano per 1 point of np

<header>Nanomage<end><orange>
Ability    Rubi-ka  / Shadowlands<end>

Strength        464 / 664  
Agility           464 / 664  
Stamina         448 / 748  
Intelligence    512 / 912  
Sense           480 / 780  
Psychic         512 / 912
Max % Add Nano Cost   -55%
2 max health per 1 point of hp
4 max nano per 1 point of np

<header>Opifex<end><orange>
Ability    Rubi-ka  / Shadowlands<end>

Strength       464 / 764  
Agility          544 / 944  
Stamina        480 / 680  
Intelligence   464 / 764  
Sense          512 / 912  
Psychic        448 / 748
Max % Add Nano Cost   -50%
3 max health per 1 point of hp
3 max nano per 1 point of np

<header><b>Atrox<end><orange>
Ability    Rubi-ka  / Shadowlands<end>
 
Strength      512 / 912  
Agility          480 / 780  
Stamina       512 / 912  
Intelligence  400 / 600  
Sense         400 / 600  
Psychic       400 / 600
Max % Add Nano Cost   -45%
4 max health per 1 point of hp
2 max nano per 1 point of np";

if (preg_match("/^breed/i", $message, $arr)) {
	$msg = bot::makeLink("Results of Breedcap Inquiry.", $blob);
	bot::send($msg, $sendto);
}

?>