<?php
$wrangle_txt = "<header>::::: Guide to Wrangles :::::<end>\n\n"; 
$wrangle_txt = "<font color = blue>-= Guide to Wrangles =-</font>

<font color = orange>Dedicated to Ferrell and all other Traders, whose patience is extraordinary, if not legendary.</font>


Ok, you've heard about it, listen to others ask for it, but what IS it exactly?
 
A wrangle is a nanoprogram buff that traders can cast.  They can boost your skills by a certain amount of points for 3 minutes, making it ideal for 'twinking' new weapons that have requirements far above your skill, or casting robot shells/grid armor above your current nanoskill ability.

The normal Skill Wrangler nanos only affect a single target and lower the abilities of the trader while raising the abilities of the target.  There are also Team Skill Wrangler nanos, which boost the abilities of everyone teamed with the trader, while draining some of the Trader's abilities.  Umbral Wranglers, available only to those who possess expansions, are special level-locked Team Wranglers that do not reduce the trader's skills, and act as a long-lasting aura.

Sometimes, a trader needs to 'drain' before casting the wrangle.  Drains are a nanoprogram that traders can use to temporarily raise their abilities, allowing them to cast higher wrangles (or other nanoprograms).  Often, this means that the trader will need to be in a place where there are monsters wandering about.  Popular places include the lawn outside the Subway in Rome Stretch and the Holoworld in the Backyards.

When asking for a wrangle, be sure to note exactly how many points that you need and be sure to check if you have enough NCU for the wrangle that you want. And please, always tip a trader.

<font color = blue>Wrangles boost the following skills:</font>

<font color = green>Martial arts
1h Blunt
1h Edged 
2h Edged 
2h Blunt
Melee energy  
Ranged energy
Piercing
Sharp objects
Grenade
Heavy weapons
Bow 
Pistol
Rifle
Smg 
Shotgun 
Assault rifle
Sensory improvement
Matter metamorphosis
Biological metamorphosis
Psychological modifications
Matter creation 
Time and space
</font>
<font color = blue>Wrangles </font><font color = red>DO NOT</font><font color = blue> boost the following skills:</font>
 
<font color = red>All Abilities (Strength, Agility, etc.)
Aimed Shot
Burst
Fast Attack
First Aid
Fling Shot
Full Auto
Multi-ranged
Multi-melee
Treatment
Sneak Attack
</font> 
<font color = blue>Below is a list of wrangles, with the skill bonus / NCU cost</font>

Regular wrangles are in <font color = yellow>YELLOW</font>
Team wrangles are in <font color = white>WHITE</font> (requires teaming with Trader to have it cast)

<font color = yellow>+3 Bonus / 3 NCU - Skill Wrangler (Weak)</font>
<font color = yellow>+9 Bonus / 5 NCU - Skill Wrangler (Patchy)</font>
<font color = white>+10 Bonus / 6 NCU - Team Skill Wrangler (Weak)</font>
<font color = yellow>+13 Bonus / 7 NCU - Skill Wrangler (Minor)</font>
<font color = white>+18 Bonus / 9 NCU - Team Skill Wrangler (Patchy)</font>
<font color = yellow>+22 Bonus / 11 NCU - Skill Wrangler (Minor)</font>
<font color = white>+24 Bonus / 12 NCU - Team Skill Wrangler (Minor)</font>
<font color = yellow>+27 Bonus / 13 NCU - Skill Wrangler (Lossy)</font>
<font color = white>+32 Bonus / 15 NCU - Team Skill Wrangler (Commonplace)</font>
<font color = yellow>+37 Bonus / 17 NCU - Skill Wrangler (Lesser)</font>
<font color = white>+40 Bonus / 19 NCU - Team Skill Wrangler (Lossy)</font>
<font color = yellow>+46 Bonus / 22 NCU - Skill Wrangler (Inferior)</font>
<font color = white>+50 Bonus / 24 NCU - Team Skill Wrangler (Lesser)</font>
<font color = yellow>+56 Bonus / 26 NCU - Skill Wrangler</font>
<font color = white>+62 Bonus / 29 NCU - Team Skill Wrangler</font>
<font color = yellow>+65 Bonus / 30 NCU - Skill Wrangler (Major)</font>
<font color = yellow>+71 Bonus / 33 NCU - Team Skill Wrangler (Major)</font>
<font color = yellow>+74 Bonus / 35 NCU - Skill Wrangler (Advanced)</font>
<font color = white>+82 Bonus / 38 NCU - Team Skill Wrangler (Advanced)</font>
<font color = yellow>+85 Bonus / 39 NCU - Skill Wrangler (Superior)</font>
<font color = white>+93 Bonus / 43 NCU - Team Skill Wrangler (Superior)</font>
<font color = yellow>+99 Bonus / 46 NCU - Skill Wrangler (Greater)</font>
<font color = white>+102 Bonus / 47 NCU - Team Skill Wrangler (Greater)</font>
<font color = yellow>+106 Bonus / 48 NCU - Skill Wrangler (Sophisticated)</font>
<font color = white>+109 Bonus / 49 NCU - Team Skill Wrangler (Sophisticated)</font>
<font color = yellow>+112 Bonus / 50 NCU - Skill Wrangler (Superb)</font>
<font color = white>+118 Bonus / 52 NCU - Team Skill Wrangler (Exceptional)</font>
<font color = yellow>+121 Bonus / 54 NCU - Skill Wrangler (Exceptional)</font>
<font color = yellow>+131 Bonus / 58 NCU - Skill Wrangler (Premium)</font>
<font color = white>+132 Bonus / 58 NCU - Team Skill Wrangler (Premium)</font> ";

$wrangle_txt = bot::makeLink("Guide to Wrangles", $wrangle_txt); 
if($type == "msg") 
bot::send($wrangle_txt, $sender); 
elseif($type == "all") 
bot::send($wrangle_txt); 
else 
bot::send($wrangle_txt, "guild"); 
?>