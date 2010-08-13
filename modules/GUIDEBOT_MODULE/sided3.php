<?php
$sided3_txt = "<header>::::: Guide to Sided Pads Quest Part 3 :::::<end>\n\n"; 
$sided3_txt = "This is where things get rough - part 3 of the sided shoulderpads. With your card II ready, you can head back to your agent of choice and turn it in for the third and final part of this mission. But hold on just a sec - like in the first part, you do not need to have this part running to accomplish the task, and with the normally difficult nature of this, it could be a good idea to wait to return the card until you have the items needed and/or know you can finish in the time given. Should you start this quest and fail to finish in time, you will need another card II - often meaning you have to complete part 1 and 2 all over again. Once turned in, you have 30 days real-time to complete the quest.

With the help of the surveillance in part 2, we know know all about how the army of killbots operates and communicates with eachother. Time to put a stop to it for good. It's not an all out war, but rather an effective counter-attack - what you need to do is get a hold of all the communication chips the robots use to organize themselves with. This will ensure the good guys can always pose as any of the bots and disrupt them from ever devising anything in secret again. Not as easy as it sounds, this will involve dismantling all 8 lackeys and eventually also the Trash King himself to construct this device of miscommunication.

And that's basically what all part 3 is about - killing stuff. Trash King himself is by far the hardest of the bunch, but not overly so. A good team of people around 100ish including some solid healing and firepower should have an ok challenge - in a perfect world. In the real (or animated) world, you are not only competing against Trash King, but also all the other people present to do the same thing. It doesn't matter if you could theoretically beat him - you have to be team or person fighting him who deals the most damage to be able to 'win' the fight and be able to pick up the item you need. Copple that with the highly sporadic spawn time of Trash (#L 'part 2' '/tell chunkbot info sided2'), and you have for many one of the most excruciating experiences in AO.

Sadly that is how it works, and there's no easy way around. Either bring the biggest guns and the toughest fighters to the scene, or try to team up and find a fair way of deciding who gets the item.

Trash King will drop the first part of our comm device - the part that all the other parts fit into, namely a 'Communication Rack with 8 empty slots'.

Next we need to fill this rack with 8 Communication Processing Units, from each of the 8 lackeys. All these are tagged with a short ID for the lackey in question, like 'Communication Processing Unit ID=LM' would be for Live Metal. They are all unique and nodrop so don't worry about getting it messed up - you'll get one of each until you have all eight. Note that all of these and even the Rack can be gathered at any pace you like, in any order, unlike the tagging procedure. Once you  have the Comm Rack, just pop the Comm units in one by one in the correct order (below).

Bringing down the lackeys is the easy part. A team of 60-70+ should have no problem here, and a single person can usually take them down alone around 130+. Watch out for the roots though. Since the lackeys now only drop the comm units, there shouldn't be any trouble finding them alive and well. A 'fun' note here would be that in the early stages of this quest, the lackeys also dropped some really nice stuff on the side. Most notably high-level damage rings, bracers of reflection and similar, in addition to also dropping the robot parts used for the assembly in part 1. Even Trash himself dropped some really unique items that is no longer in the game. You can imagine how increasingly hard and how longer it would take to actually do this quest when these bots were killed on sight for everything but quest-related items.
<FONT COLOR = YELLOW>
ID=TKL
 * Trash King Lackey, West Athen, 1600 x 1000
ID=EU
 * Electro Unique, Wailing Wastes, 600 x 1900
ID=NB
 * Nuts & Bolts, Wartorn Valley, 700 x 650
ID=GJ
 * Greasy Joints, Newland, 900 x 950
ID=LM
 * Live Metal, Greater Tir County, 1650 x 2200
ID=BiB
 * Best in Brass, Rome Stretch, 400 x 800
ID=MM
 * Metalomania, Lush Fields, 3000 x 3200
ID=GV
 * Galvano, Greater Omni Forrest, 2000 x 2300
</FONT>
Once you have them all and the Communication Rack from Trash King, just use the units on it to assemble them in the order above. The finished device will be a Complex Communication Processing Unit - the item we need to return to complete this quest.

For this, like before, you will be given an update syringe - called the Second Update Syringe. Using this on your second pads from part 2 will update them once more.

Now these cool shoulderpads are really starting to show - literally. The third level pads builds on their previous stats and now adds 200 extra health and nano, +20 to First Aid, and 100 to all Armor Classes. Requirements to wear are belonging to the appropriate side, and being level 50 or higher. In addition, when worn, the third level pads also show up on your persona on top of everything else and looks quite nice.

Since the pads can go in each of the shoulderslots, you can also wear two of these - as long as they are of a different type. Meaning if you have the patience and dedication you could work up to get the third pads, then start over again and work up the second level pads to have in your other shoulderslot for a nice 350 hp/nano and +32 to firstaid.

In addition, there is now a tradeskill process to upgrade the pads once more. Just use an Omnifier on them, and BOOM! How easy could that be?
 ";

$sided3_txt = bot::makeLink("Guide to Sided Pads Part 3", $sided3_txt); 
if($type == "msg") 
bot::send($sided3_txt, $sender); 
elseif($type == "all") 
bot::send($sided3_txt); 
else 
bot::send($sided3_txt, "guild"); 
?>