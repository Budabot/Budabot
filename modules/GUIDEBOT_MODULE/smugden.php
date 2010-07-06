<? 
$smugden_txt = "<header>::::: Guide to Smuggler's Den :::::<end>\n\n"; 
$smugden_txt = "Located in the south east corner of Rubi-Ka, we have the Smuggler's Den. Home to the Den Mantis species and the smugglers - humanoids who like to hoard great treasure and smuggle stuff from and to Rubi-Ka. The dungeon is located in a small 25% area, so you need to enter quickly, in case someone is waiting for you outside ;)

Location: Southern Foul Hills (South East Rubi-Ka), 1755 x 872

The dungeon can be found in the south eastern zone of Rubi-Ka. East of Omni-1. The shortest way is to zone out of Omni-1 and fly north east (running would take *very long*), pass the zone border in the north east part of Greater Omni Forest (the east is blocked with an unpassable zonewall) and then fly south east to the dungeon. Pass through the small 25% zone around the dungeon entrance and enter the dungeon through a small mantis hive entrance.

The back-door entrance from Borealis has been declared an exploit by the Funcom Exploit Team Lead 'Genevra'. Thus it is not advised to use another way to enter Smuggler's Den, other than the main entrance in Southern Fouls Hills or by being beacon warped by an engineer.

An engineer that is already in the dungeon and teamed with you, could also beacon warp you to him, if he/she has the nano 'Beacon Warp'. Thats the 2nd and last way to enter this dungeon. Please be aware that you *will* need a team for the dungeon (except if you are a Bureaucrat with area calm and lots of HP). The dungeon is packed with monsters, which you can see on the dungeon map. But here also drop some of the best items available in Anarchy Online.

Level Recommendation: Team, level 120-200

Monsters that you will encounter in this dungeon:

in the Den Mantis part (through Southern Foul Hills entrance):

Den Mantis Worker
Den Mantis Ravager
Den Mantis Forager
Den Mantis Digger
Den Mantis Drone
Den Mantis Runner
Den Nano-Mantis
Den Mantis Burrower
Den Mantis Breeder
Den Mantis Queen
Den Mantis Baby
Den Hive Guardian
Den Mantis Scout
Den Mantis Earthmelder

in the Smugglers part of the dungeon:

Den Loot Controller
Den Loot Warden
Den Smuggler Thug
Evolved Outcast
Ancient Clawfinger
Ancient Outcast
Frother
Den Protector MK II
Den Smuggler Brute
Den Smuggler Pilot
Den Smuggler Technician
Den Adamant Hound

NPCs that you will encounter in this dungeon (in the smugglers part):

Ash Andersen
Robin Raag 

Drops of Smugglers  	 
	DeCranum's Corona MK I: Armor (all body parts) 	ql 140
	DeCranum's Corona MK II: Armor (all body parts) 	ql 170
	Small Titan Message Container (drops of Smuggler Pilot, for creation of Coffee Machine) ql 100
 
Drops of Den Mantis 	 
	Mantis Scissors (drops of Den Mantis Breeders) 	ql 185
	Mantis Egg (drops of Den Mantis Breeders) 	ql 190
  	various lower level usual loot drops - nano clusters, instruction discs, weapons, armor, monster parts up to QL 220, gems up to QL 230, etc. 	 

Drops of Den Mantis Queen 	 
	Queen Blade 	ql 200
	Mantis Egg (drops of Den Mantis queen) 	ql 190

The Mantis Egg is used for the very valuable Virral Triumvirate Egg

The DeCranum's armor drops rarely and getting a whole set is difficult. It has level requirements and is one of the best armors ingame (adds NCU and HP) for medium level players. ";

$smugden_txt = $this->makeLink("Guide to Smuggler's Den", $smugden_txt); 
if($type == "msg") 
$this->send($smugden_txt, $sender); 
elseif($type == "all") 
$this->send($smugden_txt); 
else 
$this->send($smugden_txt, "guild"); 
?>