<?php
$sided1_txt = "<header>::::: Guide to Sided Pads Quest Part 1 :::::<end>\n\n"; 
$sided1_txt = " It wouldn't be quite right to do this quest so late after it showing up without going into a few of the nightmares that has followed it's birth and seeing how the designers went back and forth before everything was finally in place.

First came the little robots, and there was much rejoyce. Popping up around major settlements, these little fun fellas would throw snide remarks at you before suddenly attacking, and besides just being something out of the ordinary, they also provided a good fight and some neat loot back then. It was not uncommon for each of them to drop high-level bracers and rings (and also the parts needed for the first quest), and for those who could, these bots would be hunted down on a regular basis. At first they didn't do anything though, as the quest wasn't even implemented yet.

Then came Alvin and Dodga, the two characters that would eventually give out the missions. They caused some major headaches like no other. First off, they would dissapear after anyone talked to them, and have a 2 hour despawn before they showed up again. Back then, talking to NPC's was still done in vicinity, so it was first come first served. With thousands of players trying to talk to these at the same time, with 2 hours inbetween, you can imagine the ridiculus amount of 'camp-hours' and anger that stockpiled every day.

At first, only the first of three parts worked and people really looked high and low to figure out what came next. Looking back, it was chaotic times in the world of Rubi-Ka - but enough of the 'good old', let's cut to the chase and show you how it's all done these days, in a much prefferable setting.

First off, the special agents responsible for setting it all off - Alvin Odeleder (Omni-Tek) and Dodga Demercel (Clan). If you happen to be one of the undecided yet, and still lingering around the neutral zones, you can just pick either one and go with it.

Although you do not need to visit these before starting your quest, you will have to go see the one for your side once you get rolling. Alvin Odeleder operates out of the Omni-Tek outpost in the middle of Lush Fields, at 1540x2500 and Dodga currently hangs around a place called The Rising Sun, a small plateu south-east of Athen (on the zone border)

Neither of these dissapear anymore, so just strike up a conversation with them as you usually would any npc. There's a slew of questions to be asked and answered, but it's all fairly obvious so you shouldn't have any trouble going through it and getting the first step of this grand quest uploaded to your interface.

		  	Dismantle Robots and other Animated Machinery...
Credits: 20.000 - Exp: 20.000
Pads of Interest or Shoulderpads for the suspicious
1 Mission Token & 1 Bravery Token

Onto the task at hand. We need 8 separate pieces of strange robotic parts. These can be found on several different places on Rubi-Ka (see a compilation of suggestions at the end of part 1 below) but have one thing in common - yep, they drop from robots, where robotic creatures frequent. Being that there are so many parts involved, this can often be a mind-numbling process as with a low drop rate and chances of finding plenty of duplicates, you're often looking at kiling hundreds and hundres of mobs to assemble all pieces. The last missing piece of course being the hardest.

The 8 pieces you need are as follows:

Suspicious Looking Parts of Biotechnology - Interconnected knots of faintly stirring, rubbery material

Liquid Based Conductors - Rigid, tough and very thin tubes, used to conduct weak currents in biotechnology systems

Scrap of Notum - A thin flake of what appears to be very pure notum

Large and partly fused lump of strange tetrahedral CPU bundles - A bit charred lump that looks like several weird shaped microprocessors wedged together

Microscopic Pulsating Nano Crystals - Condensed droplets of swirling nanobots

Ancient Looking Lamp Relay - A small oblong light-bulb, really ancient looking

Bundle of Nano-Tubes - Oily to the touch, and incredible minute bundles of what seems to be hollow tubes

Highly plastic mother-circuit array - A motherboard of some sort, but when you pick it up you realise it is not rigid like most of its kind, but is highly plastic

Once you have them all, or even the first few in the correct assembly order, you can start putting this strange contraption together. The pieces must be assembled in the correct order - by using piece #2 on piece #1, then piece #3 on the combined #1 and #2 etc - from top to bottom of the listing starting with the Suspicious Looking Parts of Biotechnology and ending with the Highly plastic mother-circuit array. Combining these pieces yield a low xp return too.

All done, you will end up with this strange contraption:

Suspicious looking polyhedral casing (emitting strange sounds) - A very suspicious looking polyhedral casing emitting warmth and occasionally some strange sounds, like in 'I am sure he was trying to say something' sounds. 

Problem solved - this is just the thing Alvin and Dodga are looking for, and returning this to them will complete the first part of the mission. Although you can find and assemble this without having been told to, be very sure you actually have the mission running when you try and return this, as otherwise you will only loose it. Upon completion, you will be awarded with the nice shoulderpads we're after:

Pads of Interest (Omni-Tek)
Shoulderpads for the Suspicious (Clan) 

In addition to the first level pads, you'll also be rewarded with two tokens - one regular token (giving x amount of tokens based on your level/titlelevel) and one bravery token.

And last, besides the new pads you'll also find in your inventory a card - very important, keep this one and don't loose it as this will unlock the quest for the second level pads.

Hunting for robot-parts:
- Mech Dogs & Waste Collectors, slightly north and north-east of Rome (Rome Stretch, 500 x 1000)
- Buzzsaws & Junkbots in the area around Trash King (West Athen, 1600 x 1000)
- Elite A-500 & A-500 Soldiers in Omni Forrest (Greater Omni Forrest, 1600 x 2200)
- Buzzsaws by the acid pool south of the Oasis (Newland Desert, 2850 x 700)
- Buzzsaws & Technoscavengers at the Bot Mountain (Greater Tir County, 1650 x 2200)
- A4000's along the northern border of Greater Tir County
- Probes in Harrys Outpost (Lush Fields)

Mechdogs are typically around level 40, Probes around 30, Buzzsaws around level 10. The levels on the A-500's to A-4000's vary greatly but the lower end of the scale starts around level 60 and they end up at around level 130. Mechdogs are considered to be the ones most 'worth the time and effort' though.
 ";

$sided1_txt = bot::makeLink("Guide to Sided Pads Part 1", $sided1_txt); 
if($type == "msg") 
bot::send($sided1_txt, $sender); 
elseif($type == "all") 
bot::send($sided1_txt); 
else 
bot::send($sided1_txt, "guild"); 
?>