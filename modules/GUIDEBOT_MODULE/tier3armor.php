<?php
$tier3armor_txt = "<header>::::: Making Tier 3 Armor :::::<end>\n\n"; 
$tier3armor_txt = "Jobe Armor Tier Three

Guide suited for: All Classes
Faction: All
Level Range: 200-220

Confused about how to get your paws on that Faithfull / Choosen armor? Good, then im not alone. Having gathered some information I believe I can present you with a full guide on how to obtain these armor parts. 

Upgrading Tier2 items (including shoulderpads/backitem if your profession has one for Tier2) 

Combine your <font color='#ffff00'> Blue Glyph of X </font>(see below for a list) with <font color='#ffff00'>Finely Refined Notum.</font> This requires <font color='#ffff00'>1210 Quantum and 1210 Computer Literature</font>, resulting in a Blue Glyph of X - Revived 

Combine <font color='#ffff00'>Blue Glyph of X - Revived </font>with an <font color='#ffff00'>Insignia of the Chosen or Faithful</font> depending on your alignment. This requires <font color='#ffff00'>1375 Field Quantum Physics and 1375 Electrical Engineering,</font> resulting in a X Glyph of Judgement (X being Earth, Water or Air) 

Combine your <font color='#ffff00'>x Glyph of Judgement</font> with your Tier2 item to create your Faithful/Chosen item. This final step requires 990 Computer Lit. 

Backitem and Shoulderpads Non Tier2 Steps. 

Only MA, Soldier and Adventurer does NOT have Tier2 Shoulderpads. 
(This is only valid for items that have no Tier2 version) 

Use your <font color='#ffff00'>Joker's Glyph</font> on your <font color='#ffff00'>Basic Jobe Suit Backitem/Shoulderpads</font> to make <font color='#ffff00'>Advanced Jobe Suit Backitem/Shoulderpads.</font> This step requires <font color='#ffff00'>990 Comp lit</font> 

Use your <font color='#ffff00'>Glyph of the Arcana</font> on your <font color='#ffff00'>Advanced Jobe Suit Backitem/Shoulderpads</font> to make <font color='#ffff00'>Excellent Jobe Suit Backitem/Shoulderpads </font>

Combine your <font color='#ffff00'>Blue Glyph of X</font> (see below for a list) with <font color='#ffff00'>Finely Refined Notum.</font> This requires <font color='#ffff00'>1210 Quantum and 1210 Computer Literature</font>, resulting in a <font color='#ffff00'>Blue Glyph of X - Revived</font>

Combine <font color='#ffff00'>Blue Glyph of X - Revived</font> with an <font color='#ffff00'>Insignia of the Chosen or Faithful</font> depending on your alignment. This requires <font color='#ffff00'>1375 Field Quantum Physics and 1375 Electrical Engineering</font>, resulting in a <font color='#ffff00'>X Glyph of Judgement (x being Earth, Water or Air)</font>

Right click your Excellent Jobe Suit support system to make a Jobe X Support System (where X is a profession) if its the backitem your creating. 

Combine your x Glyph of Judgement with your Excellent Jobe Suit Support System or Excellent Jobe Suit Shoulderpads to create your Faithful/Chosen item. This final step requires 990 Computer Lit. 

Not too complicated was it? What about the items involved you say? 

Tier3 in terms of items needed requires the least numerous, but definately involves some of the hardest to find items. For a full set of Faithful/Chosen armor you will need the following items. 

1 x Joker's Glyph(Not consumed, NODROP, only needed for backitem/shoulderpads) - Drops of the Eradicus bosses in Adonis 

1 x Glyph of the Arcana (Not consumed, only needed for backitem/shoulderpads) - Drops from Pandemonium corrupted mobs 

1 x Insignia of the Faithful/Chosen (Not consumed) - Inferno named sided mobs and Inferno Temple boss. (Mordeth/Galahad) Temple bosses are known to drop multiples usually. Also drops in Dark Marshes. 

In the Redeemed temple you can find an NPC named Eccleast Gal Ilad (North room, standing in the middle of the room next to the wall) who will happily trade your Choosen insignias for Faithful ones. 

Somewhere in the Unredeemed temple an NPC named Prophet Abad Mord should be availible to do the same trade for Unredeemed. 

10 x Blue Glyph of X (One for each armor piece) - Drops from Corrupted mobs in Pandemonium, or can be farmed from inferno catacombs (see below) 

10 x Finely Refined Notum (One for each armor piece, Unique) - Dryad bosses in Ely, Adonis and Penumbra, as well as ANY dryad including bosses in inferno. The droprate is very poor, and it's not uncommon to end up killing 30-50 dryad bosses in a row to get a drop. 

Now, there is one other way to obtain the Blue glyphs, however it is not for the faint of heart, it involves venturing into the Inferno catacombs and killing what lies within. I haven't been there myself, but judging by the difficulty of mobs in the Penumbra catacombs, I'd expect that Pandemonium is the easier way of obtaining the glyphs by far. Anyways. 

You will need 3 fruitions and 1 embryo for each glyph. 
The Fruitions drop from general mobs in the catacombs, while the Embryos are random rare drop from minibosses and a decent chance from the main boss (level 270) 

And as always, the following fruition types for the different professions 

<font color='#ffff00'>Fruition of the Reposeful </font>
Doctor 
Fixer 
MP 
Trader 
Adventurer 

<font color='#ffff00'>Fruition of the Tempestuous </font>
Agent 
Crat 
NT 
Soldier 

<font color='#ffff00'>Fruition of the Unshakeable </font>
MA 
Keeper 
Enforcer 
Shade 
Engineer 

And finally, a list of the glyphs the individual professions needs (Thx Nightmecha): 

<font color='#ffff00'>Blue glyph of Enel </font>
Agent 
Bureaucrat 
Nano Technician 
Soldier 

<font color='#ffff00'>Blue glyph of Aban </font>
Engineer 
Martial artist 
Enforcer 
Keeper 
Shade 

<font color='#ffff00'>Blue glyph of Ocra</font>
Meta-Physicist 
Doctor 
Adventurer 
Fixer 
Trader 
 
 ";

$tier3armor_txt = bot::makeLink("Making Tier 3 Chosen/Faithful Armor", $tier3armor_txt); 
if($type == "msg") 
bot::send($tier3armor_txt, $sender); 
elseif($type == "all") 
bot::send($tier3armor_txt); 
else 
bot::send($tier3armor_txt, "guild"); 
?>