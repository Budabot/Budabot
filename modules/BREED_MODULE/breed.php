<?php
$s="<header>".'     Solitus'."<end><red>".' 
Ability    Rubi-ka  / Shadowlands'."<end>".'
 
Strength        472 / 772  
Agility           480 / 780  
Stamina         480 / 780  
Intelligence    480 / 780  
Sense           480 / 780  
Psychic         480 / 780';

$n="<header>".'    Nanomage'."<end><red>".'  
Ability    Rubi-ka  / Shadowlands'."<end>".'

Strength        464 / 664  
Agility           464 / 664  
Stamina         448 / 748  
Intelligence    512 / 912  
Sense           480 / 780  
Psychic         512 / 912';

$o="<header>".'    Opifex '."<end><red>".'
Ability    Rubi-ka  / Shadowlands'."<end>".'

Strength       464 / 764  
Agility          544 / 944  
Stamina        480 / 680  
Intelligence   464 / 764  
Sense          512 / 912  
Psychic        448 / 748';

$a="<header><b>".'    Atrox '."<end><red>".'
Ability    Rubi-ka  / Shadowlands'."<end>".'
 
Strength      512 / 912  
Agility          480 / 780  
Stamina       512 / 912  
Intelligence  400 / 600  
Sense         400 / 600  
Psychic       400 / 600';

if(eregi ("^breed",$message ,$arr)) {
	$list = "               .:| Breed Caps |:.\n\n";
	$list.=" $s \n\n $n \n\n $o \n\n $a ";
	$msg = bot::makeLink("Results of Breedcap Inquiry.", $list);
}

bot::send($msg, $sendto);

?>