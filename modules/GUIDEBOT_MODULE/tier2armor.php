<? 
$tier2armor_txt = "<header>::::: Making Tier2 Armor :::::<end>\n\n"; 
$tier2armor_txt = "Jobe Armor Tier Two

Guide suited for: All Classes
Faction: All
Level Range: 200-220

There are 9 pieces of Tier 2 armor (10 if your an adventurer) 

2 x Sleeve, 2 x Shoulderpad, Gloves, Boots, Pants, Chest and Helmet 

* NOTE : If your an Adventurer you get an extra back piece, so thats 10. 

Ingredients per piece: 

3 x Will of <xxxxxx> for each piece (18 for complete normal set) 
1 x Embryo of Yomi'Arallu for each piece (6 for complete normal set) 
3 x Advent of the <xxxxxx> for each piece (18 for complete normal set) 
1 x Canister of Pure Liquid Notum for each piece (6 for complete normal set) 

Recipe for normal armor pieces (excluding Helmet and Shoulderpads): 

Note: This example is assuming you're an Adventurer 

(a) Red Glyph of Ocra = Trade in 3 x Will of the Reposeful's + 1 Consanguineal Embryo of Yomi'Arallu to your IPS representative. 

(b) Purple Glyph = Trade in 3 x Crystals inscribed 'Advent of the Prudent' to your IPS representative. 

Use Canister of Pure Liquid Notum on Red Gylph (a) to Activate it 
Then combine result with Purple Gylph of Bhotaar(b) 
Then combine result with Tier 1 armour. 

Recipe for upgrading Shoulder Pads, Back Item and Helmet (this part is forAdventurers ONLY) 

Just apply a Jokers Glyph to each piece. The Jokers Glyph drops from Eradicus in Adonis, and you only need 1 Glyph (as it is NODROP UNIQUE) to apply to the shoulderpads, helmet and back item. 

Note: This results in the Advanced Jobe helmet, pads, and back item. 

Profession specific parts: 

Adventurer 
Advent of the Prudent 
Will of the Reposeful 

Agent 
Advent of the Impeteous 
Will of the Tempestuous 

Bureaucrat 
Advent of the Passionate 
Will of the Tempestuous 

Doctor 
Advent of the Benign 
Will of the Reposeful 

Enforcer 
Advent of the Prudent 
Will of the Unshakeable 

Engineer 
Advent of the Industrious 
Will of Unshakable 

Fixer 
Advent of the Impeteous 
Will of the Reposeful 

Keeper 
Advent of the Benign 
Will of the Unshakable 

Martial Artist 
Advent of the Impeteous 
Will of the Unshakeable 

Meta-Physicist 
Advent of the Passionate 
Will of the Reposeful 

Nano-Technician 
Advent of the Benign 
Will of the Tempestuous 

Shade 
Advent of the Passionate 
Will of the Unshakable 

Soldier 
Advent of the Prudent 
Will of the Tempostous 

Trader 
Advent of the Industrious 
Will of the Reposeful 

The visions and embryos drop in the Penumbra and higher zones (Catacombs under each Temple). 
 ";

$tier2armor_txt = bot::makeLink("Making Tier2 Armor", $tier2armor_txt); 
if($type == "msg") 
bot::send($tier2armor_txt, $sender); 
elseif($type == "all") 
bot::send($tier2armor_txt); 
else 
bot::send($tier2armor_txt, "guild"); 
?>