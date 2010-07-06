<? 
$jacksrings_txt = "<header>::::: Information about LE Alien Missions :::::<end>\n\n"; 
$jacksrings_txt ="<font color = yellow>::::: Information about LE Alien Missions :::::</font>


<font color = #31D6FF>Jack's rings</font>

Guide suited for: All Classes
Faction: All
Level Range: 100-150

There are 12 NPCs scattered around Rubi-Ka, that give out unique quests. They are profession specific, at least NPCs and rewards are. Every profession can talk to any of them, but unless it's their designated NPC, they won't get very far with dialog. But they have all similar requests. They want your help in retrieving specific item from Jack 'Leg-Chooper' Mendelez. Unfortunately this part of dialog is misleading, since the real Jack doesn't drop any of requested items. Instead you should aim for one of his 5 clones, scattered around Varmint Woods. In a way that's better, since Clones spawn with place-holders - Soul Swappers(15min spawn) and have close to 100 % drop on one of items. Those drops are random, so you might spend long time looking for right one. Good thing is that you can do this quest while teamed or just follow some high level char who's killing Clones for phats. 

Couple of tips: Get item prior to pulling quest. Only center mob in a camp is real place-holder, you can ignore rest of them if they don't agro. Try to circle around several camps, killing place-holders, you'll get more chance of spawning Clone. 


Jack Legchopper Clones locations: 
3750 x 1005 
4080 x 350 
2515 x 440 
2170 x 1100 
2830 x 2720 

Beside droping items needed for quest, Clones also have chance to drop: 


Silken Legchopper Gloves
This Axe Belongs to Jack
Supporting Carbonan Holster
Disposal Unit Electrical Toolset
Ambidextrous Plasteel Gloves

Clones are no push-over. They spawn in ql 150-160 range. If you plan to do this quest at intended level, you definitively need a team. Or an end-game friend. But the reward, at least for some professions, is worth the troubles. With different basic abilities and even treatment, those rings are interesting even to players with expansion. For NPCs, items needed and reward, please consult lists below (first list is just refference for quick info). 


Profession
 Item needed
 Reward
 
Adventurer
 
Jack's Head
 
Ring of the Falcon Talon
 
Agent
 
Proof of what happened to Joslyn
 
Ringlet of Black Panther Whiskers
 
Bureaucrat
 
Important looking briefcase
 
Band of Snake Skin 
 
Doctor
 
DNA Sample
 
Ring of the Dolphin Spine
 
Enforcer
 
Jack's Reinforced Gloves
 
Band of the Bear Claw
 
Engineer
 
Remains of a long lost pet
 
Ring of Crawling Ants
 
Fixer
 
Jack's Head
 
Curl of Weasel Whiskers
 
Martial Artist
 
Severed leg of the Grand Master
 
Ring of the Monkey Tail
 
Meta Physicist
 
Glowing Amygdaloid Nucleus
 
Band of the Frog Tongue
 
Nanotechnician
 
Glowing Amygdaloid Nucleus
 
Band of Beeswax
 
Soldier
 
Bloodstained Dog-tag
 
Band of Dog Molars
 
Trader
 
Jack's Head
 
Ring of Magpie Tail Feathers
 


Adventurer 




Name of NPC: Kendric Kuzio 
Location: 1550x1000, Deep Artery Valley 
Item needed: 

Jack's Head 
Reward: 

Ring of the Falcon Talon 

Agent 




Name of NPC: Susan Furor 
Location: 1220x1940, Galway County 
Item needed: 

Proof of what happened to Joslyn 
Reward: 

Ringlet of Black Panther Whiskers 

Bureaucrat 




Name of NPC: Nolan Deslandes 
Location: inside Neuter'r'us, Newland City 
Item needed: 

Important looking briefcase 
Reward: 

Band of Snake Skin 

Doctor 




Name of NPC: Quintus Romulus 
Location: 2115x795 (in front of Foreman's), The Longest Road 
Item needed: 

DNA Sample 
Reward: 

Ring of the Dolphin Spine 

Enforcer 




Name of NPC: Bonzo 
Location: inside Beer and Booze, Hope, Mort 
Item needed: 

Jack's Reinforced Gloves 
Reward: 

Band of the Bear Claw 

Engineer 




Name of NPC: Janella Gheron 
Location: inside Cyborg Barracks, Greater Tir County 
Item needed: 

Remains of a long lost pet 
Reward: 

Ring of Crawling Ants 

Fixer 




Name of NPC: Gridman 
Location: inside Fixer's grid, top floor 
Item needed: 

Jack's Head 
Reward: 

Curl of Weasel Whiskers 

Martial Artist 




Name of NPC: Daedra Iberra 
Location: inside Versales tower, Pleasant Meadows 
Item needed: 

Severed leg of the Grand Master 
Reward: 

Ring of the Monkey Tail 

Meta Physicist 




Name of NPC: Elmer Ragg 
Location: 1730x940, Mort 
Item needed: 

Glowing Amygdaloid Nucleus 
Reward: 

Band of the Frog Tongue 

Nanotechnician 




Name of NPC: Robin Raag 
Location: inside Smuggler's Den, Southern Foul Hills 
Item needed: 

Glowing Amygdaloid Nucleus 
Reward: 

Band of Beeswax 

Soldier 




Name of NPC: Captain Lewison 
Location: inside Reet's Retreat, Street West Bank 
Item needed: 

Bloodstained Dog-tag 
Reward: 

Band of Dog Molars 

Trader 




Name of NPC: Monday Kline 
Location: 1300x885, Street East Bank 
Item needed: 

Jack's Head 
Reward: 

Ring of Magpie Tail Feathers 
";

 
$jacksrings_txt = $this->makeLink("Jacks Professionals Rings Quest RK", $jacksrings_txt); 
if($type == "msg") 
$this->send($jacksrings_txt, $sender); 
elseif($type == "priv") 
$this->send($bs_text); 
else 
$this->send($jacksrings_txt, "guild");
?>