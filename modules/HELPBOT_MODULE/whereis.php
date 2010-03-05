<?
/*
   ** Author: Blackruby (RK2)
   ** Description: Where Is Command that shows you where places / uniques are on RK.
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 20.10.2006
   ** Date(last modified): 24.10.2006
   ** 
   ** Copyright (C) 2006 Sarah H
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */
//|help|
//Find information about locations and people around Rubi-Ka
//
//example: /tell (bot) (syntax)(command) borealis
//
//|help|
//////////////// make your code below this point

if(eregi("^whereis (.+)$", $message, $arr)) {
	$query = strtolower($arr[1]);
	$reply = "";

	$whereisanswer["castle"]=
		"Galway Castle or Camelot Castle?";
	
	$whereisanswer["4"]=
	$whereisanswer["4 ho"]=
	$whereisanswer["4 hole"]=
	$whereisanswer["4 holes"]=
	$whereisanswer["4 holes grid"]=
	$whereisanswer["4 holes trade"]=
	$whereisanswer["4ho"]=
	$whereisanswer["4holes"]=
	$whereisanswer["fourholes"]=
		"4 Holes (aka 4HO) is located in the west central part of the world. It has a grid access point at 893x1754.  Whompas at 1200x1225 to 2HO, 20K, and Broken Shores.  North of 4Holes is Stret West Bank, south is Andromeda, east is Stret East Bank, no zone avail to the west.";
	
	$whereisanswer["fixer"]=
	$whereisanswer["fixer shop"]=
	$whereisanswer["fixer sohp"]=
	$whereisanswer["fixer store"]=
	$whereisanswer["fixershop"]=
		"The fixer shop is at 440 400 in Borealis zone. It looks like a pile of junk and you need 180 B&E to use it as well as being a fixer :)";
	
	$whereisanswer["abmouth"]=
	$whereisanswer["abmouth supremus"]=
		"Abmouth Supremus is at Subway Dungeon, in the bottom, at the very end.  He spawns two 'infectors' when agro'ed.";
	
	$whereisanswer["aegaen"]=
	$whereisanswer["aegan"]=
	$whereisanswer["aegean"]=
	$whereisanswer["aegeon"]=
	$whereisanswer["agean"]=
	$whereisanswer["ageon"]=
	$whereisanswer["aegon"]=
		"Aegean is located in the northeast part of the world. No grid access point and no whompa. East of Aegean is Varmint Woods, to the west is Athen Shire, southeast is Upper Stret East Bank, southwest is Wartorn Valley and Stret West Bank, no zone available to the north.";
	
	$whereisanswer["alvin"]=
	$whereisanswer["alvin odeleder"]=
	$whereisanswer["alvin odeler"]=
	$whereisanswer["alvinodeleder"]=
		"Alvin Odeleder is in the Lush Fields Outpost (1538,2500)";
	
	$whereisanswer["655"]=
	$whereisanswer["andromada"]=
	$whereisanswer["andromed"]=
	$whereisanswer["andromeda"]=
	$whereisanswer["andromedia"]=
	$whereisanswer["andromida"]=
	$whereisanswer["andromeda wastelands"]=
	"Andromeda is located in the southwest part of the world.  Whompas to Tir, Newland, and OmniTrade at 3250 900.  To the north (east) of Andromeda is 4 Holes, to the north (west) is Stret East Bank. To the east is Milky Way, south (east) is Lush Fields, south (west) is Clondyke. No zone avail to the west.";
	
	$whereisanswer["architect striker"]=
		"Architect Striker is at Subway Dungeon, usually on the bridge.";
	
	$whereisanswer["athen"]=
	$whereisanswer["athen fault"]=
	$whereisanswer["athen forest"]=
	$whereisanswer["athen forrest"]=
	$whereisanswer["athen grid"]=
	$whereisanswer["athen grotto"]=
	$whereisanswer["athen hill"]=
	$whereisanswer["athen old"]=
	$whereisanswer["athen shops"]=
	$whereisanswer["athenold"]=
	$whereisanswer["athens"]=
	$whereisanswer["athens grid"]=
	$whereisanswer["athen whompaa"]=
	$whereisanswer["old athen"]=
	$whereisanswer["old athen'"]=
	$whereisanswer["old athen clothing shop"]=
	$whereisanswer["old athen east"]=
	$whereisanswer["old athen east's closest whompa"]=
	$whereisanswer["old athen grid"]=
	$whereisanswer["old athen grid access"]=
	$whereisanswer["old athens"]=
	$whereisanswer["old ather"]=
		"Athen Old is located in the northwest part of the world. Grid access at 512x573.  It has a whompa to Tir, Wailing Wastes and Bliss at 445,318. Out the east gate is Wartorn Valley and out West Gate is West Athen.";
	
	$whereisanswer["athen shir"]=
	$whereisanswer["athen shire"]=
	$whereisanswer["athen shite"]=
	$whereisanswer["athen shrine"]=
	$whereisanswer["athen sire"]=
	$whereisanswer["athens shire"]=
	$whereisanswer["athenshire"]=
		"Athen Shire is located in the northwest part of the world. It has no grid access or whompa. To the north is Wailing Wastes, to the east is Wartorn Valley and Aegean, to the west is The Longest Road, to the south is Holes in the Wall.  The War Academy is at 1740x1970";
	
	$whereisanswer["athen west"]=
	$whereisanswer["athens west"]=
	$whereisanswer["athenwest"]=
	$whereisanswer["old athen weat"]=
	$whereisanswer["old athen west"]=
	$whereisanswer["old athens west"]=
	$whereisanswer["west athen"]=
	$whereisanswer["west athen grid"]=
	$whereisanswer["west athens"]=
		"Athen West is located in the northwest part of the world. It has a grid access point at 472x410 and no whompa. To the north, south and west is Athen Shire, to the east is Athen Old.";
	
	//Arackis	Apr 23rd, 2003 3:46:48pm Pacific Standard Time	RK1	feedback the wampa from avalon goes to wailing wastes and Bliss, not athens
	$whereisanswer["avalon"]=
	$whereisanswer["avolon"]=
	$whereisanswer["omni outpost avalon"]=
		"Avalon is located in the north west part of the world. It has a grid entry at 2070x3760 and a whompa to Bliss and Wailing Wastes at 2175x3815. There is no zone available to the north, east and west. Southeast is Wailing Wastes.  Camelot Castle (dungeon) is at 2090x3820.  Clan OP with scanner at 1540x3730 and 2140x3110.  Omni OP with scanner at 800x1630 and at 1870x1230";
	
	$whereisanswer["dancing fool"]=
	$whereisanswer["a dancing fool"]=
		"A Dancing Fool is located in Baboons (located in Omni Entertainment at 766x766)";
	
	$whereisanswer["babbons"]=
	$whereisanswer["baboons"]=
	$whereisanswer["babboons"]=
	$whereisanswer["baboon's"]=
	$whereisanswer["baboons club"]=
		"Baboons is located in Omni Entertainment at 766x766.";
	
	$whereisanswer["605"]=
	$whereisanswer["belial"]=
	$whereisanswer["belial fores"]=
	$whereisanswer["belial forest"]=
	$whereisanswer["belial forrest"]=
	$whereisanswer["belial forset"]=
	$whereisanswer["belialforest"]=
	$whereisanswer["belial artery valley"]=
		"Belial Forest is located in the southeast part of the world. No grid access.  Whompas in Wine lead to Broken Shores and Varmint Woods at 2150x2319. To the north of Belial Forest is Deep Artery Valley, south is Eastern Foul Plains, west is S.Artery Valley and Milky Way is south and southwest. No zone to east.";
	
	$whereisanswer["best"]=
	$whereisanswer["best in brass"]=
	$whereisanswer["best in brast"]=
	$whereisanswer["bestinbrass"]=
		"Best In Brass is in Galway Shire, Rome Stretch (400,750)";
	
	$whereisanswer["biomare"]=
	$whereisanswer["biomare dungeon"]=
	$whereisanswer["foreman"]=
	$whereisanswer["foreman dungeon"]=
	$whereisanswer["foreman mines"]=
	$whereisanswer["foremans"]=
	$whereisanswer["foreman's"]=
	$whereisanswer["foremans den"]=
	$whereisanswer["foremans dungeon"]=
	$whereisanswer["foreman's dungeon"]=
	$whereisanswer["foremans office"]=
	$whereisanswer["foreman's office"]=
	$whereisanswer["omni med"]=
	$whereisanswer["omnimedbiofacility"]=
	$whereisanswer["t.i.m"]=
	$whereisanswer["t.im"]=
	$whereisanswer["tim"]=
		"Biomare (Biological materials research) (aka Omni-Med Bio Facility) (aka Foremans Office) is located in The Longest Road at 1930x775. Recommended for teams of lvl 40-70. Look for a door that says Foreman above the doorway.";
	
	
	$whereisanswer["bilss"]=
	$whereisanswer["blis"]=
	$whereisanswer["bliss"]=
	$whereisanswer["bliss worms"]=
		"Bliss is located in The Longest Road at 3700x1615. Bliss has shops, clan insure terms, banks and mission terms. Bliss has whompas to Athen Old, Avalon, and Broken Shores";
	
	$whereisanswer["bonzo"]=
		"Bonzo is at Beer and Booze Bar, Mort at 2835 x 1930";
	
	$whereisanswer["borealis"]=
	$whereisanswer["borealis approach"]=
	$whereisanswer["borealis city"]=
	$whereisanswer["borealis grid"]=
		"Borealis is located in the central-west part of the world. Grid access at 635x727.  Whompa to Stret West Bank at 682x531. Whompa to Newland right next to that. East of Borealis is Holes in the Wall. No zones currently available to the north, south, and west.";
	
	$whereisanswer["broken"]=
	$whereisanswer["broken shord"]=
	$whereisanswer["broken shore"]=
	$whereisanswer["broken shores"]=
	$whereisanswer["broken shores whompa"]=
	$whereisanswer["broken shores whompah"]=
	$whereisanswer["broken shores."]=
	$whereisanswer["brokenshores"]=
	$whereisanswer["brokenshores grd"]=
	$whereisanswer["bs"]=
	$whereisanswer["bs grid"]=
		"Broken Shores is located in the southwest corner of the world. It has a grid access point in City of Home at 644x1313.  In the north is a Whompah at 1000x3760 to Bliss and to Wine.  There is a Whompah to Rome Red and 4 Holes Trade at 2340x2250";
	
	$whereisanswer["camelot dungeon"]=
	$whereisanswer["camelot dungeons"]=
	$whereisanswer["camelotdungeon"]=
		"Camelot Castle (dungeon) can be found in Avalon. As you step out of the whompa, the entrance to the wonderfull and quite deadly castle of Camelot looms directly in front of you.  Just step up the small hill and pass the gate, at 2090x3820";
	
	
	
	$whereisanswer["camalot"]=
	$whereisanswer["camelot"]=
	$whereisanswer["camolot"]=
	$whereisanswer["camelot castle"]=
	$whereisanswer["camelotcastle"]=
		"Camelot is located in the northwest part of the world, in the center of the city of Avalon and can be reached by whompa from Old Athens or Wailing Wastes.";
	
	$whereisanswer["capt lewison"]=
	$whereisanswer["capt. lewison"]=
	$whereisanswer["captain lewison"]=
	$whereisanswer["lewison"]=
		"Captain Lewison is at Reet retreat, Stret West Bank";
	
	$whereisanswer["590"]=
	$whereisanswer["cav"]=
	$whereisanswer["cav base"]=
	$whereisanswer["central"]=
	$whereisanswer["central art"]=
	$whereisanswer["central artery"]=
	$whereisanswer["central artery valley"]=
	$whereisanswer["central artery vally"]=
	$whereisanswer["central artery walley"]=
	$whereisanswer["central arthery valley"]=
	$whereisanswer["central artrey valley"]=
	$whereisanswer["central aterial valley"]=
	$whereisanswer["central atery valley"]=
	$whereisanswer["central atrery valley"]=
	$whereisanswer["central atrery vally"]=
	$whereisanswer["centralarteryvalley"]=
	$whereisanswer["central artery vlaeey"]=
	$whereisanswer["central atery"]=
	$whereisanswer["central valley region"]=
		"Central Artery Valley is located in the mideastern part of the world. No grid or whompa. North of Central Artery Valley is Varmint Woods, south is Southern Artery Valley, east is Deep Artery Valley, west is Upper Stret East Bank. NE corner is Greater Tir Cnty and SW corner is Stret East Bank.";
	
	$whereisanswer["city of jobe"]=
		"There is no City of Jobe.... yet.";
	
	
	$whereisanswer["city"]=
	$whereisanswer["city of home"]=
	$whereisanswer["city of homes"]=
	$whereisanswer["cityofhome"]=
	$whereisanswer["home"]=
		"City of Home is located in the Broken Shores zone, at 735x1459. It has shops, clan/neut insure terms, banks.";
	
	$whereisanswer["clan modified a 4000"]=
	$whereisanswer["clan modified a4000"]=
	$whereisanswer["clan modified a-4000"]=
		"Clan modified A-4000 is at Avalon at 1105 x 2520";
	
	$whereisanswer["claw finger"]=
	$whereisanswer["claw finger forefather"]=
	$whereisanswer["clawfinger"]=
	$whereisanswer["clawfinger fore father"]=
	$whereisanswer["clawfinger forefather"]=
	$whereisanswer["fore father"]=
	$whereisanswer["forefather"]=
		"Clawfinger Forefather is at Smuggler's Den, Southern Fouls Hills at 1749 x 869";
	
	$whereisanswer["clondike"]=
	$whereisanswer["clondyke function"]=
	$whereisanswer["clondyke gold"]=
	$whereisanswer["clondyke mine"]=
	$whereisanswer["clondyke"]=
	$whereisanswer["coldyke"]=
	$whereisanswer["condyke"]=
	$whereisanswer["klondyke"]=
		"Clondyke is located in the southwest part of the world. It has a grid access point at 1054x4023, no whompa. North of Clondyke is Andromeda, west is Galway County, east is Lush Fields. No zone avail to the south.";
	
	$whereisanswer["colonel frank kaehler"]=
	$whereisanswer["colonel frank keahler"]=
	$whereisanswer["colonel frank kehler"]=
	$whereisanswer["frank kaehler"]=
	$whereisanswer["frank keahler"]=
	$whereisanswer["frank kehler"]=
	$whereisanswer["kaehler"]=
	$whereisanswer["keahler"]=
	$whereisanswer["kehler"]=
		"Colonel Frank Kaehler is at Omni-Forest at 700 x 2000";
	
	$whereisanswer["commander jocasta"]=
	$whereisanswer["jocasta"]=
		"Commander Jocasta is at Cyborg Barracks in GTC at 3232 x 2340";
	
	
	$whereisanswer["commander kelly frederickson"]=
	$whereisanswer["commander kelly fredrickson"]=
	$whereisanswer["frederickson"]=
	$whereisanswer["fredrickson"]=
	$whereisanswer["kelly frederickson"]=
	$whereisanswer["kelly fredrickson"]=
	$whereisanswer["kelly"]=
		"Commander Kelly Frederickson is at Tir County at 1750 x 1100";
	
	$whereisanswer["subway old athens"]=
	$whereisanswer["rome blue subway"]=
	$whereisanswer["abandoned subway"]=
	$whereisanswer["athen subway"]=
	$whereisanswer["athens subway"]=
	$whereisanswer["borealis subway"]=
	$whereisanswer["clan subway"]=
	$whereisanswer["codemned subway"]=
	$whereisanswer["comdemned subway"]=
	$whereisanswer["condemned subway"]=
	$whereisanswer["condemned subway dungeon"]=
	$whereisanswer["condemnedsubway"]=
	$whereisanswer["old subway"]=
	$whereisanswer["subway"]=
	$whereisanswer["subway in tir"]=
	$whereisanswer["subway old athenes"]=
	$whereisanswer["subway station"]=
		"Condemned Subway is a dungeon for L 1-24 players.  There are 3 Subway dungeons.  In Borealis at 637x463 (south part of city).  In Galyway Shire at 168x874, just outside the east gates of Rome Blue.  And in Old Athen at 487x439, just NE of town center.";
	
	$whereisanswer["cuty"]=
		"Cuty is at Tir County Area, Crater Farm Region at 1500 x 600";
	
	
	$whereisanswer["borgs"]=
	$whereisanswer["cyborg"]=
	$whereisanswer["cyborg baracks"]=
	$whereisanswer["cyborg barrack"]=
	$whereisanswer["cyborg barrackes"]=
	$whereisanswer["cyborg barracks"]=
	$whereisanswer["cyborg barracksccccc"]=
	$whereisanswer["cyborg barracs"]=
	$whereisanswer["cyborg barraks"]=
	$whereisanswer["cyborg camp"]=
	$whereisanswer["cyborg campp"]=
	$whereisanswer["cyborg camps"]=
	$whereisanswer["cyborg domain"]=
	$whereisanswer["cyborg dungeon"]=
	$whereisanswer["cyborgbarracks"]=
	$whereisanswer["borg baracks"]=
	$whereisanswer["borg barrack"]=
	$whereisanswer["borg barrackes"]=
	$whereisanswer["borg barracks"]=
	$whereisanswer["borg barracksccccc"]=
	$whereisanswer["borg barracs"]=
	$whereisanswer["borg barraks"]=
	$whereisanswer["borg camp"]=
	$whereisanswer["borg campp"]=
	$whereisanswer["borg camps"]=
	$whereisanswer["borg domain"]=
	$whereisanswer["borg dungeon"]=
	$whereisanswer["borgbarracks"]=
		"Cyborg Barracks is located in the northeast part of the world, in Greater Tir County at 3230x2340. Recommended for teams of lvl 70-90.";
	
	$whereisanswer["daedra"]=
	$whereisanswer["daedra iberra"]=
	$whereisanswer["iberra"]=
		"Daedra Iberra is at Pleasent Meadows at  1510 x 720";
	
	$whereisanswer["daria"]=
		"Daria is at Lush Fields at 1782 x 2062";
	
	$whereisanswer["595"]=
	$whereisanswer["dav"]=
	$whereisanswer["deep"]=
	$whereisanswer["deep arteral valley"]=
	$whereisanswer["deep arterial valley"]=
	$whereisanswer["deep artery"]=
	$whereisanswer["deep artery valle"]=
	$whereisanswer["deep artery valley"]=
	$whereisanswer["deep artery vally"]=
	$whereisanswer["deep arthey valley"]=
	$whereisanswer["deep artilery vally"]=
	$whereisanswer["deep artillery valley"]=
	$whereisanswer["deep arty walley"]=
	$whereisanswer["deep aterial valley"]=
	$whereisanswer["deeparteryvalley"]=
		"Deep Artery Valley is located in the mideastern part of the world. No grid and no whompa. To the north of Deep Artery Valley is Greater Tir County (but a force field blocks you from zoning north), south is Belial Forest, west is Central Artery Valley and Southern Artery Valley, no zone to east.";
	
	$whereisanswer["diamondine trainee"]=
	$whereisanswer["diamond trainee"]=
	$whereisanswer["dt"]=
		"Diamondine Trainee is at Eastern Fouls Plains at 1925 x 1450";
	
	$whereisanswer["diamondine soldier"]=
	$whereisanswer["diamond soldier"]=
	$whereisanswer["ds"]=
		"Diamondine Soldier is at Eastern Fouls Plains at 1925 x 1450";
	
	$whereisanswer["ding"]=
		"Ding is at Greater Tir County";
	
	$whereisanswer["dungeon"]=
	$whereisanswer["dungeons"]=
	$whereisanswer["static"]=
		"Dungeons I know about: Temple of the Three Winds, Will to Fight, Condemned Subway, Steps of Madness, BioMaRe, Cyborg Barracks, Smugglers Den, Camelot Dungeon";
	
	
	$whereisanswer["620"]=
	$whereisanswer["east foul plains"]=
	$whereisanswer["easten foul plains"]=
	$whereisanswer["eastern foul hills"]=
	$whereisanswer["eastern foul plain"]=
	$whereisanswer["eastern foul plains"]=
	$whereisanswer["eastern foul planes"]=
	$whereisanswer["eastern foul"]=
	$whereisanswer["eastern foulplains"]=
	$whereisanswer["eastern fouls plain"]=
	$whereisanswer["eastern fouls plains"]=
	$whereisanswer["eastern fouls"]=
	$whereisanswer["eastern fowl plains"]=
	$whereisanswer["eastern"]=
	$whereisanswer["easternfoulsplain"]=
	$whereisanswer["efp"]=
		"Eastern Fouls Plain is located in the southeast part of the world. No grid or whompa. North of E.Fouls Plain is Belial Forest (but a force field keeps you from zoning north), south is Southern Fouls Hills, north west is Milky Way, southwest is Pleasant Meadow. No zone avail to the east.";
	
	$whereisanswer["electro"]=
	$whereisanswer["electro umique"]=
	$whereisanswer["electro uneak"]=
	$whereisanswer["electro unique"]=
	$whereisanswer["electro uniqui"]=
	$whereisanswer["electrounique"]=
		"Electro Unique is in Wailing Wastes, north of Athens Shire (650,1950)";
	
	$whereisanswer["elian"]=
	$whereisanswer["elian zuwadza"]=
	$whereisanswer["zuwadza"]=
		"Elian Zuwadza is at Galway at 450x1300";
	
	$whereisanswer["elmer"]=
	$whereisanswer["elmer ragg"]=
	$whereisanswer["ragg"]=
		"Elmer Ragg is at Mort at 1731x942";
	
	$whereisanswer["deimos"]=
	$whereisanswer["demos"]=
	$whereisanswer["eradicater"]=
	$whereisanswer["eradicater deimos"]=
	$whereisanswer["eradicater demos"]=
	$whereisanswer["eradicator deimos"]=
	$whereisanswer["eradicator demos"]=
		"Eradicator Deimos is at Cyborg Barracks in GTC at 3232 x 2340";
	
	$whereisanswer["ericmendelsonoutpost"]=
	$whereisanswer["varmint woods outpost"]=
		"Eric Mendelson Outpost is located in Varmint Woods at 2450x2100. There are mission terms, insure term and shopping terms.";
	
	$whereisanswer["escaped"]=
	$whereisanswer["escaped gargantula"]=
	$whereisanswer["gargantula"]=
		"Escaped Gargantula is at Galway, Central Galway County at 2160 x 1150";
	
	$whereisanswer["eumenides"]=
		"Eumenides is at Subway Dungeon, at the end of the one rail way hall, past the slum runners but before the infectors.";
	
	$whereisanswer["fiery"]=
	$whereisanswer["fiery soldier"]=
	$whereisanswer["firey soldier"]=
	$whereisanswer["soldier"]=
		"Fiery Soldier is at Eastern Fouls Plains at 365 x 2060";
	
	$whereisanswer["freedom outpost"]=
	$whereisanswer["freedomoutpost"]=
		"Freedom Outpost is located in Athen Shire at 1553x370. Insure terms, shops and mission terms and banks are available here. Directly beside the outpost is the zone to Wailing Wastes.";
	
	
	
	
	$whereisanswer["galivino"]=
	$whereisanswer["galvano"]=
	$whereisanswer["galven"]=
	$whereisanswer["galvino"]=
		"Galvano is in Greater Omni-Forest, Grassland (2020,2190)";
	
	
	$whereisanswer["galway castle"]=
	$whereisanswer["galway conty"]=
	$whereisanswer["galway castle"]=
	$whereisanswer["galaway castle"]=
	$whereisanswer["galaway country"]=
	$whereisanswer["galaway county"]=
	$whereisanswer["galway caslte"]=
	$whereisanswer["galway castle"]=
	$whereisanswer["galway count"]=
	$whereisanswer["galway country"]=
	$whereisanswer["galway county"]=
	$whereisanswer["galway county gold"]=
	$whereisanswer["galway county grid"]=
	$whereisanswer["galway gold"]=
	$whereisanswer["galwaycounty"]=
		"Galway County is located in the southwest part of the world. It has a grid access point at 1416x1091 and whompas at 2530x1175 to Outpost 10-3, Omni-1 Trade, and Rome . West is Galway Shire, east is Clondyke, no zones avail to north and south.";
	
	$whereisanswer["galaway"]=
	$whereisanswer["galway"]=
	$whereisanswer["galway hills"]=
	$whereisanswer["galway view"]=
	$whereisanswer["galway vounty gold"]=
		"Galway County or Galway Shire? Galway Castle is in Galway County. Please try again.";
	
	$whereisanswer["gallowy shire"]=
	$whereisanswer["galoway shire"]=
	$whereisanswer["galaway shire"]=
	$whereisanswer["galway connty grid"]=
	$whereisanswer["galway grid"]=
	$whereisanswer["galway shire"]=
	$whereisanswer["galway shire gold"]=
	$whereisanswer["galwayshire"]=
		"Galway Shire is located in the southwest part of the world. No grid access or whompa. East is Galway County, west is Broken Shores, no zones avail to north and south. Rome is located midway in Galway Shire on far west side.";
	
	$whereisanswer["general serverus"]=
	$whereisanswer["serverus"]=
		"General Serverus is at Cyborg Barracks in GTC at 3232 x 2340";
	
	$whereisanswer["genghis"]=
	$whereisanswer["genghis pan"]=
	$whereisanswer["pan"]=
		"Genghis Pan is at Mongol Meat, Tir";
	
	$whereisanswer["george"]=
		"George is at Greater Tir County at 3200 x 2300";
	
	$whereisanswer["good time party mixer"]=
	$whereisanswer["party mixer"]=
		"Good Time Party Mixer is at Newland City at 463 x 339.  And one for Clans is located at Reets Retreat";
	
	$whereisanswer["greasy gears"]=
	$whereisanswer["greasy jints"]=
	$whereisanswer["greasy joings"]=
	$whereisanswer["greasy joint"]=
	$whereisanswer["greasy joints now"]=
	$whereisanswer["greasy joints"]=
	$whereisanswer["greasy jones"]=
	$whereisanswer["greasy"]=
	$whereisanswer["greasyjoints"]=
	$whereisanswer["greazy gears"]=
	$whereisanswer["greazy jints"]=
	$whereisanswer["greazy joings"]=
	$whereisanswer["greazy joint"]=
	$whereisanswer["greazy joints now"]=
	$whereisanswer["greazy joints"]=
	$whereisanswer["greazy jones"]=
	$whereisanswer["greazy"]=
	$whereisanswer["greazyjoints"]=
		"Greasy Joints is in Newland Desert (890,950)";
	
	$whereisanswer["greater omni"]=
	$whereisanswer["greater omni foreset"]=
	$whereisanswer["greater omni forest"]=
	$whereisanswer["greater omni forrest"]=
	$whereisanswer["greateromniforest"]=
		"Greater Omni Forest is located in the southeast part of the world. No grid or whompa.";
	
	$whereisanswer["greater tir"]=
	$whereisanswer["greater tir conty"]=
	$whereisanswer["greater tir country"]=
	$whereisanswer["greater tir county"]=
	$whereisanswer["greatertircounty"]=
		"Greater Tir County is located in the northeast part of the world. Grid access in Tir City at 555x527 and a whompa in Tir City at 475x466 to Newland City. To the south of Greater Tir County is Tir County, west is Varmint Woods, no zones avail to north or east.";
	
	$whereisanswer["grid man"]=
	$whereisanswer["gridman"]=
		"Gridman is at Fixer Grid, at the top";
	
	$whereisanswer["brock"]=
	$whereisanswer["commander brock"]=
	$whereisanswer["high commander brock"]=
		"High Commander Brock is at SE OP of Tir, 2861 x 738";
	
	$whereisanswer["hole"]=
	$whereisanswer["hole in"]=
	$whereisanswer["hole in the wall"]=
	$whereisanswer["hole in wall"]=
	$whereisanswer["hole s in the wall"]=
	$whereisanswer["holes"]=
	$whereisanswer["holes in"]=
	$whereisanswer["holes in the"]=
	$whereisanswer["holes in the wall"]=
	$whereisanswer["hole's in the wall"]=
	$whereisanswer["holes in the wall wall"]=
	$whereisanswer["holes in the wallwave"]=
	$whereisanswer["holesinthewall"]=
		"Holes in the Wall is located in the west central part of the world. No grid access and no whompa. North of Holes in the Wall is Athen Shire, south and east is Stret West bank, Borealis to the west.";
	
	$whereisanswer["hope"]=
		"Hope is a neutral city in Mort.  It has whompas to Stret West Bank and Newland Desert at 2888x1909";
	
	$whereisanswer["ian"]=
	$whereisanswer["ian war"]=
	$whereisanswer["ian warr"]=
	$whereisanswer["warr"]=
		"Ian Warr is at Eastern Fouls Plains at 720 x 1380";
	
	$whereisanswer["icc"]=
		"ICC is located in Andromeda at 3250x900.  Whompas to ICC come from Newland, Omni Trade and Tir.";
	
	$whereisanswer["bobic"]=
	$whereisanswer["inventor"]=
	$whereisanswer["inventor bobic"]=
		"Inventor Bobic is at Tir Country at 1910 x 1398";
	
	$whereisanswer["jack legchopper"]=
	$whereisanswer["jacklegchooper"]=
	$whereisanswer["jack"]=
	$whereisanswer["jack legchopper clone"]=
	$whereisanswer["jack the legchoopper"]=
	$whereisanswer["jack the legchopper"]=
	$whereisanswer["jack the leggchopper"]=
	$whereisanswer["jackthelegchopper"]=
	$whereisanswer["legchopper"]=
	$whereisanswer["legchopper jack"]=
		"Jack and his clones have been spotted in Varmint Woods near 2828x2727, 2135x1300, 2177x1111, 2500x433, 3200x1400, 3750x1000, 4100x350";
	
	$whereisanswer["gheron"]=
	$whereisanswer["janella"]=
	$whereisanswer["janella gheron"]=
		"Janella Gheron is at Cyborg Barracks, GTC at 3200 x 2300";
	
	$whereisanswer["joo"]=
		"Joo is at Omni Forest Area, Sunken Swamps at 500 x 2700";
	
	$whereisanswer["kendric"]=
	$whereisanswer["kendric kuzio"]=
	$whereisanswer["kuzio"]=
		"Kendric Kuzio is at Deep Artery Valley at 1532, 999";
	
	$whereisanswer["lab director"]=
		"Lab Director is at Longest Road, The Foremans Office at 1940 x 775";
	
	$whereisanswer["leet"]=
	$whereisanswer["leet crater"]=
	$whereisanswer["leetcrater"]=
		"Leet Crater is just south east of Omni-Pol Barracks region in Omni Forrest, which is just south outside Omni Entertainment's City Gates.";
	
	$whereisanswer["live"]=
	$whereisanswer["live metal"]=
	$whereisanswer["livemetal"]=
		"Live Metal is in Greater Tir County, Rocky Outcrops (1520,2210)";
	
	$whereisanswer["ljotur"]=
		"Ljotur is at Deep Artery Valley at 1103 x 722";
	
	$whereisanswer["ghasap"]=
	$whereisanswer["lord ghasap"]=
		"Lord Ghasap is at Avalon Dungeon, Avalon at 2092 x 3822";
	
	$whereisanswer["lush feilds"]=
	$whereisanswer["lush fhields"]=
	$whereisanswer["lush field"]=
	$whereisanswer["lush fields outpost at"]=
	$whereisanswer["lush fields outpost"]=
	$whereisanswer["lush fields resort"]=
	$whereisanswer["lush fields"]=
	$whereisanswer["lush forest"]=
	$whereisanswer["lush hill"]=
	$whereisanswer["lush hills resort"]=
	$whereisanswer["lush hills"]=
	$whereisanswer["lush meadows"]=
	$whereisanswer["lush op"]=
	$whereisanswer["lush outpost"]=
	$whereisanswer["lush resort"]=
	$whereisanswer["lush woods"]=
	$whereisanswer["lush"]=
	$whereisanswer["lushfields"]=
	$whereisanswer["north west mines"]=
	$whereisanswer["nw mines"]=
	$whereisanswer["omni mine"]=
	$whereisanswer["omni mines"]=
	$whereisanswer["omni outpost"]=
	$whereisanswer["omni prime"]=
	$whereisanswer["omni resort"]=
		"Lush Fields is located in the south central part of the world. Lush Fields has grid access at 1443x667 (Lush Hills Resort)and at Harry's at 3115x3183. Ferrys to Harry's at 3563x916, ferrys to PM OT outpost at 3391x797 and 3195x3178 and ferry to Omni Trade at 3295x2917. No whompa. NW is Andromeda, northeast is Milky Way, west is Clondyke, east is Pleasant Meadows. MutantDomain is located centrally on eastern border.";
	
	$whereisanswer["harries"]=
	$whereisanswer["harry's"]=
	$whereisanswer["harry;s"]=
	$whereisanswer["harrys grid"]=
	$whereisanswer["harrys outpost"]=
	$whereisanswer["harrys"]=
		"Harry's Outpost is located in Lush Feilds.  You can grid to Harrys or take the west exit from Omni-Trade.  Or take the teleportal from the west side of Pleasent Meadows.";
	
	$whereisanswer["mantis queen"]=
		"Mantis Queen is at Smuggler's Den, Southern Fouls Hills at 1749 x 869";
	
	$whereisanswer["marcus"]=
	$whereisanswer["marcus poet laureate"]=
		"Marcus Poet Laureate is at Broken Shores at 1500 x 2900";
	
	$whereisanswer["metalomania"]=
		"Metalomania is in Lush Fields, Harry's Outpost (2980,3125)";
	
	$whereisanswer["nuggets"]=
	$whereisanswer["mick nugget"]=
	$whereisanswer["mc nugget"]=
	$whereisanswer["mcnugget"]=
	$whereisanswer["mick nugget mcmullet"]=
	$whereisanswer["nugget"]=
		"Mick Nugget McMullet is at Clondyke at 1100 x 3700";
	
	$whereisanswer["milky"]=
	$whereisanswer["milky valley"]=
	$whereisanswer["milky war"]=
	$whereisanswer["milky way"]=
	$whereisanswer["milky way'"]=
	$whereisanswer["milkyway"]=
		"Milky Way is located in the southeast part of the world. No grid access or whompa. North of Milky Way is S.Artery Valley, nw is Stret East Bank, west is Andromeda, sw is Lush Fields, south is Pleasant Meadows, SE is Eastern Foul Plains, NE is Belial Forest.";
	
	$whereisanswer["molested molecules"]=
		"Molested Molecules is at Subway Dungeon";
	
	$whereisanswer["kline"]=
	$whereisanswer["monday"]=
	$whereisanswer["monday kline"]=
		"Monday Kline is at Stret E at 1300 x 890 at TRA only";
	
	$whereisanswer["lafaye"]=
	$whereisanswer["morgan"]=
	$whereisanswer["morgan lafaye"]=
		"Morgan LaFaye is at Avalon Dungeon, Avalon at 2092 x 3822, down the stairs where the PvP zone starts, and to the right.";
	
	$whereisanswer["mort"]=
	$whereisanswer["mort crater"]=
	$whereisanswer["mort dungeon"]=
	$whereisanswer["mort small"]=
	$whereisanswer["mort static dungeon (small)"]=
	$whereisanswer["sentinel"]=
	$whereisanswer["sentinel base"]=
	$whereisanswer["sentinel outpost"]=
	$whereisanswer["sentinels"]=
		"Mort is located in the northeast part of the world.  It has whompas to Stret West Bank and Newland Desert at 2888x1909. Grid access is at 1928x1255 (Sentinels). There is no zones available to the north or west of Mort, to the south is Newland, to the east is Perpetual Wastelands.";
	
	$whereisanswer["morty"]=
		"Morty is at Tir County Area, Kuroshio Forest at 300 x 1200";
	
	$whereisanswer["mutant"]=
	$whereisanswer["mutant domain"]=
	$whereisanswer["mutant swamp"]=
	$whereisanswer["mutant swamp village"]=
	$whereisanswer["mutantdomain"]=
		"Mutant Domain is located in the west central part of the world. No grid access or whompa. To the north, south and west of Mutant Domain is Lush Fields, east is Pleasant Meadows. This zone lies centrally on the eastern border of Lush Fields.";
	
	$whereisanswer["neleb"]=
	$whereisanswer["neleb the deranged"]=
	$whereisanswer["nelib"]=
		"Neleb the Deranged is at Omni Forest, at the very end of the Steps of Madness dungeon at 800 x 2844";
	
	$whereisanswer["johnson"]=
	$whereisanswer["nelly"]=
	$whereisanswer["nelly johnson"]=
		"Nelly Johnson is at Eastern Fouls Plains, 720 x 1380";
	
	$whereisanswer["netrom"]=
		"Netrom is a small outpost type ruins in Southern Artery Valley at coords 1988 x 606";
	
	$whereisanswer["neuters"]=
	$whereisanswer["neuters r us"]=
	$whereisanswer["neutsrus"]=
		"Neuts R Us is a club located in Newland City at 447x340.";
	
	
	$whereisanswer["nlc"]=
	$whereisanswer["newland city"]=
	$whereisanswer["newland whompa"]=
	$whereisanswer["newland wompa"]=
	$whereisanswer["newlandcity"]=
	$whereisanswer["newlandwompa"]=
		"Newland City is located in the north east part of the world. It has a grid access point outside city west gate at 1172x482 and whompas to the ICC, Tir, and Borealis at 390x300. To the north, east, south and west of \"Newland City\" is \"Newland\".";
	
	$whereisanswer["meetmedeer"]=
	$whereisanswer["meetmeder"]=
	$whereisanswer["meetmedere"]=
	$whereisanswer["metmedere"]=
	$whereisanswer["mmd"]=
	$whereisanswer["newland desert"]=
	$whereisanswer["newland desert city"]=
	$whereisanswer["newland desert whompa"]=
	$whereisanswer["newland desert whoompa"]=
	$whereisanswer["newland desetr"]=
	$whereisanswer["newland dessert"]=
	$whereisanswer["newlanddesert"]=
		"Newland Desert is located in the northeast part of the world. It has a grid access point at 1172x482 and whompas to \"Newland City\" and Hope at 2200x1575.  To the north is \"Newland\", to the south is Varmint Woods. There are currently no zones available to the east or west.";
	
	$whereisanswer["newland"]=
	$whereisanswer["newland grid"]=
	$whereisanswer["newlands"]=
		"Newland is located in the northeast part of the world. Grid access at 1527,x2767 (Meetmedere), whompas inside \"Newland City\" to ICC, \"Newland Desert\", and Borealis at 390x300. To the north of Newland is Mort, to the south is \"Newland Desert\". Currently no zones available to the east or west.";
	
	$whereisanswer["gregg"]=
	$whereisanswer["nodda"]=
	$whereisanswer["nodda gregg"]=
		"Nodda Gregg is at Tir Country/OP at 1933 x 1494";
	
	$whereisanswer["deslandes"]=
	$whereisanswer["nolan"]=
	$whereisanswer["nolan deslandes"]=
		"Nolan Deslandes is in Neuters R Us, Newland City";
	
	$whereisanswer["notum profundis"]=
	$whereisanswer["profundis"]=
		"Notum Profundis is at Eastern Fouls Plains at 773 x 1430";
	
	$whereisanswer["notum soldier"]=
		"Notum Soldier is at Eastern Fouls Plains at 2000 x 2400";
	
	$whereisanswer["notum trainee"]=
		"Notum Trainee is at Eastern Fouls Plains at 2000 x 2400";
	
	$whereisanswer["nuts"]=
	$whereisanswer["nuts $ bolts"]=
	$whereisanswer["nuts & blots"]=
	$whereisanswer["nuts & bolts"]=
	$whereisanswer["nuts &bolts"]=
	$whereisanswer["nuts and"]=
	$whereisanswer["nuts and bolts"]=
	$whereisanswer["nut's and bolts"]=
	$whereisanswer["nuts and boltz"]=
	$whereisanswer["nuts bolts"]=
	$whereisanswer["nuts n bolt"]=
	$whereisanswer["nuts n bolts"]=
	$whereisanswer["nuts&bolts"]=
	$whereisanswer["nutsandbolts"]=
		"Nuts & Bolts is in Aegan, Wartorn Valley (790,680)";
	
	$whereisanswer["oi"]=
	$whereisanswer["o.i."]=
	$whereisanswer["o i"]=
	$whereisanswer["obediency inspector"]=
		"Obediency Inspector is at Eastern Fouls Plains, at the lake, near 1225 x 2800";
	
	$whereisanswer["omni forest"]=
	$whereisanswer["omni forist"]=
	$whereisanswer["omni forrest"]=
	$whereisanswer["omni froest"]=
	$whereisanswer["omniforest"]=
		"Omni Forest is located in the southeast part of the world. No grid access or whompa. North of Omni Forest is Pleasant Meadows, northeast is Eastern Foul Plains, east us Southern Foul Plains.  To the west is Omni Entertainment.";
	
	
	$whereisanswer["ent district"]=
	$whereisanswer["ent docks"]=
	$whereisanswer["ent grid"]=
	$whereisanswer["ent park"]=
	$whereisanswer["ent sewers"]=
	$whereisanswer["ent west"]=
	$whereisanswer["ent"]=
	$whereisanswer["entertainment district"]=
	$whereisanswer["entertainment docks"]=
	$whereisanswer["entertainment park"]=
	$whereisanswer["entertainment sewers"]=
	$whereisanswer["entertainment west"]=
	$whereisanswer["entertainment"]=
	$whereisanswer["omni 1 ent sewers"]=
	$whereisanswer["omni 1 ent whompa"]=
	$whereisanswer["omni 1 ent"]=
	$whereisanswer["omni 1 entaaaa"]=
	$whereisanswer["omni 1 entertainmen"]=
	$whereisanswer["omni 1 entertainment sewers"]=
	$whereisanswer["omni 1 entertainment whompa"]=
	$whereisanswer["omni 1 entertainment"]=
	$whereisanswer["omni 1 entertainmentaaaa"]=
	$whereisanswer["omni 1 sewers"]=
	$whereisanswer["omni ent backyard 1"]=
	$whereisanswer["omni ent south"]=
	$whereisanswer["omni ent whompa"]=
	$whereisanswer["omni ent"]=
	$whereisanswer["omni enter"]=
	$whereisanswer["omni entertainemtn"]=
	$whereisanswer["omni entertainment south"]=
	$whereisanswer["omni entertainment"]=
	$whereisanswer["omni1ent"]=
	$whereisanswer["omni1entertainment"]=
		"Omni-1 Entertainment is located in the southeast part of the world. It has a grid access point at 879x579 and 582x337.  Whompa in the north east of town at 890x671 lead to 20K.  Whompas in south east at 900x470 lead to Omni-1 Trade and Rome Red.";
	
	$whereisanswer["hq"]=
	$whereisanswer["omni 1 hw"]=
	$whereisanswer["omni admin"]=
	$whereisanswer["omni headquarters"]=
	$whereisanswer["omni hq"]=
	$whereisanswer["omni tek main base"]=
	$whereisanswer["omni1 hq"]=
	$whereisanswer["omni1hq"]=
		"Omni-1 HQ is located in the southeast part of the world. It has a grid access point at 602x468.";
	
	$whereisanswer["omni 1 trade"]=
	$whereisanswer["omni 1"]=
	$whereisanswer["omni approach"]=
	$whereisanswer["omni barracks"]=
	$whereisanswer["omni one"]=
	$whereisanswer["omni superior"]=
	$whereisanswer["omni teade"]=
	$whereisanswer["omni trade grid"]=
	$whereisanswer["omni trade"]=
	$whereisanswer["omni whompa"]=
	$whereisanswer["omni"]=
	$whereisanswer["omni1 trade"]=
	$whereisanswer["omni1"]=
	$whereisanswer["omni1trade"]=
	$whereisanswer["omnitrade"]=
	$whereisanswer["onmi"]=
	$whereisanswer["trade district"]=
	$whereisanswer["trade gridpoint"]=
	$whereisanswer["trade n stuff"]=
	$whereisanswer["trade statue"]=
	$whereisanswer["trade"]=
	//Allix	Mar 20th, 2003 6:00:47am Pacific Standard Time	RK1	feedback omni trade grid access is actually at 407.9 575.9...
		"Omni-1 Trade is located in the southeast part of the world. It has a grid access point at 407 x 575.   Whompas to ICC, Omni Entertainment, and Galway Castle at 370x380.  Out the east gate is Omni1 HQ.  Out the west gate is Lush Fields.";
	
	$whereisanswer["oscar"]=
		"Oscar is at Greater Omni Forest";
	
	$whereisanswer["10 3"]=
	$whereisanswer["10 3 outpost"]=
	$whereisanswer["outpost 10 13"]=
	$whereisanswer["outpost 10 3"]=
	$whereisanswer["outpost103"]=
		"Outpost 10-3 is located in Southern Artery Valley with whompas to Galway Castle, 2HO, and 20K at 1150x2340.";
	
	$whereisanswer["ownz"]=
		"Ownz is at Tir County Area, Crownhead Forrest at 2200 x 700";
	
	$whereisanswer["johnston"]=
	$whereisanswer["patricia"]=
	$whereisanswer["patricia johnston"]=
		"Patricia Johnston is at Eastern Foul Plains at 720 x 1380";
	
	$whereisanswer["penultimate ditch"]=
	$whereisanswer["penultimateditch"]=
		"Penultimate Ditch, follow the road from Borealis to Stret West Bank. Zoning. Then take straight to North (no more zoning). Penultimate Ditch: Pos: 428 x 1401";
	
	$whereisanswer["570"]=
	$whereisanswer["perpetual"]=
	$whereisanswer["perpetual wast"]=
	$whereisanswer["perpetual waste"]=
	$whereisanswer["perpetual waste land"]=
	$whereisanswer["perpetual wasteland"]=
	$whereisanswer["perpetual wastelands"]=
	$whereisanswer["perpetual wastes"]=
	$whereisanswer["perpetual wastlands"]=
	$whereisanswer["perpetualwastelands"]=
	$whereisanswer["pw"]=
		"Perpetual Wastelands is located in the northeast part of the world. It has no grid access point and no whompa. To the west is Mort and there are currently no zones available to the east, north or south.";
	
	
	$whereisanswer["pleasnt meadows"]=
	$whereisanswer["20k"]=
	$whereisanswer["20k op"]=
	$whereisanswer["20k uotpost"]=
	$whereisanswer["2ok"]=
	$whereisanswer["pleasant"]=
	$whereisanswer["pleasant fields"]=
	$whereisanswer["pleasant meadow"]=
	$whereisanswer["pleasant meadows"]=
	$whereisanswer["pleasant medows"]=
	$whereisanswer["pleasant meedows"]=
	$whereisanswer["pleasantmeadows"]=
	$whereisanswer["pleasent"]=
	$whereisanswer["pleasent meadow"]=
	$whereisanswer["pleasent meadows"]=
	$whereisanswer["pleasent medors"]=
	$whereisanswer["pleasent medows"]=
	$whereisanswer["plesant meadows"]=
	$whereisanswer["plesent meadows"]=
		"Pleasant Meadows (aka 20K) is located in the southeast part of the world. It has a ferry grid to Harry's at 360x1568, grid ferry to Omni Outpost in Lush Fields at 360x1565.  Whompas at 1261x2300 to Outpost 10-3, OmniEntertainment, and 4HOles . North is Milky Way, south is Omni Forest, east is Eastern Foul Plains, west is Lush Fields.";
	
	$whereisanswer["polly"]=
		"Polly is at Omni Forest Area, Swamp River Delta and Northern Drylands at 450x1280";
	
	$whereisanswer["powa"]=
		"Powa is at Greater Tir County at 800x2400";
	
	$whereisanswer["professer van horn"]=
	$whereisanswer["professer vanhorn"]=
	$whereisanswer["professor van horn"]=
	$whereisanswer["professor"]=
	$whereisanswer["pvh"]=
	$whereisanswer["van horn"]=
		"Professor Van Horn is at Newland Desert at 2900 x 1600";
	
	$whereisanswer["quintus"]=
	$whereisanswer["quintus romulus"]=
	$whereisanswer["romulus"]=
		"Quintus Romulus is at Foremans Office";
	
	$whereisanswer["r 2000 vermin disposal unit"]=
	$whereisanswer["r2000 vermin disposal unit"]=
	$whereisanswer["r-2000 vermin disposal unit"]=
		"R-2000 Vermin Disposal Unit is at Greater Tir County, Brainy Ant Woods at 800x1800";
	
	$whereisanswer["reet retreet"]=
	$whereisanswer["leet retreat"]=
	$whereisanswer["reet"]=
	$whereisanswer["reet retrat"]=
	$whereisanswer["reet retreat"]=
	$whereisanswer["reet s retreat"]=
	$whereisanswer["reetretreat"]=
	$whereisanswer["reets"]=
	$whereisanswer["reets paradise"]=
	$whereisanswer["reets retreat"]=
	$whereisanswer["reet's retreat"]=
		"Reet Retreat is a club located in Last Ditch in Stret West Bank at 1206x2807.";
	
	$whereisanswer["rhino"]=
	$whereisanswer["rhino cockpit"]=
	$whereisanswer["rhino cockpit at x"]=
	$whereisanswer["rhino cocpit"]=
	$whereisanswer["rhino cracked valley"]=
	$whereisanswer["rhino man camp"]=
	$whereisanswer["rhino pit"]=
	$whereisanswer["rhino valley"]=
	$whereisanswer["rhinocockpit"]=
	$whereisanswer["rhinoman cockpit"]=
	$whereisanswer["rhinoman mother"]=
	$whereisanswer["rhinoman village"]=
		"Rhino Cockpit is at 3140x1920 in Newland Desert. You can get there by going NE from Newland City, zone and continue NE once in Newland Desert. Rhino Cockpit has become a pretty popular site for L 25-35 groups";
	
	$whereisanswer["rhompas"]=
	$whereisanswer["rompah bar"]=
	$whereisanswer["ropmpa"]=
	$whereisanswer["rhompa"]=
	$whereisanswer["rhompa bar"]=
	$whereisanswer["rhompa room"]=
	$whereisanswer["rhompabar"]=
	$whereisanswer["rompa"]=
	$whereisanswer["rompa bar"]=
	$whereisanswer["rompa room"]=
	$whereisanswer["rompas"]=
	$whereisanswer["rompas bar"]=
		"Rhompa Bar is located in Omni Entertainment at 714x698.";
	
	$whereisanswer["rising"]=
	$whereisanswer["rising sun"]=
	$whereisanswer["rising temple"]=
	$whereisanswer["risingsun"]=
		"Rising Sun is located south of Wartorn Valley. It is a neighborhood in the south part of the Aegean zone.";
	
	$whereisanswer["raag"]=
	$whereisanswer["robin"]=
	$whereisanswer["robin raag"]=
		"Robin Raag is at Smuggler's Den, behind Ash at 21 x 217";
	
	$whereisanswer["omni blue"]=
	$whereisanswer["rome blue"]=
	$whereisanswer["rome blue advanced shop"]=
	$whereisanswer["rome blue backyard"]=
	$whereisanswer["rome blue highrise 7"]=
	$whereisanswer["rome blue house"]=
	$whereisanswer["rome blue wompa"]=
	$whereisanswer["romeblue"]=
		"Rome Blue is located in the southwest part of the world.  Out the west gates is Rome Red where the whompas are.  Out the east gate is Galway Shire.";
	
	$whereisanswer["rome green"]=
	$whereisanswer["romegreen"]=
		"Rome Green is located in the southwest part of the world. Out the east gate is Rome Red where the whompas are.";
	
	
	$whereisanswer["rome sup shop"]=
	$whereisanswer["omni red"]=
	$whereisanswer["red rome"]=
	$whereisanswer["rome bay"]=
	$whereisanswer["rome green grid terminal"]=
	$whereisanswer["rome house"]=
	$whereisanswer["rome park"]=
	$whereisanswer["rome park area"]=
	$whereisanswer["rome red"]=
	$whereisanswer["rome redd"]=
	$whereisanswer["rome steich"]=
	$whereisanswer["rome steitch"]=
	$whereisanswer["rome stretch"]=
	$whereisanswer["rome trade district"]=
	$whereisanswer["rome whompa"]=
	$whereisanswer["rome wompa"]=
	$whereisanswer["romered"]=
		"Rome Red is located in the southwest part of the world. It has a grid access point at 251x318.  Whompas at 350x315 to Omni Entertainment, Galway Castle, and Broken Shores.  Rome Blue is to the east and Rome Green is to the west.";
	
	$whereisanswer["rome"]=
	$whereisanswer["romes"]=
		"Rome Red, Rome Blue, or Rome Green? Please try again.";
	
	$whereisanswer["610"]=
	$whereisanswer["sav"]=
	$whereisanswer["sav vendor"]=
	$whereisanswer["southern artery"]=
	$whereisanswer["southern artery valley"]=
	$whereisanswer["southern artery vally"]=
	$whereisanswer["southern artery walley"]=
	$whereisanswer["southernarteryvalley"]=
		"S.Artery Valley is located in the mideastern part of the world. No grid access. Whompas to Galway Castle, 2HO, and 20K at 1150x2340. North of S.Artery Valley is Central Artery Valley, south is Milky Way (but a force field will not allow you to zone south), west is Stret East Bank and east is (ne) Deep Artery Valley (se) Belial Forest.";
	
	$whereisanswer["615"]=
	$whereisanswer["foul"]=
	$whereisanswer["fouls hills"]=
	$whereisanswer["foul hills"]=
	$whereisanswer["foul plains"]=
	$whereisanswer["fouls"]=
	$whereisanswer["south foul hiklls"]=
	$whereisanswer["south foul hills"]=
	$whereisanswer["south foul plain"]=
	$whereisanswer["south foul plains"]=
	$whereisanswer["southen foul plains"]=
	$whereisanswer["souther foul hills"]=
	$whereisanswer["souther fouls"]=
	$whereisanswer["souther fouls hills"]=
	$whereisanswer["southern foul"]=
	$whereisanswer["southern foul hill"]=
	$whereisanswer["southern foul hills"]=
	$whereisanswer["southern foul plains"]=
	$whereisanswer["southern foul plains."]=
	$whereisanswer["southern fouls"]=
	$whereisanswer["southern fouls hill"]=
	$whereisanswer["southern fouls hills"]=
	$whereisanswer["southern fouls plains"]=
	$whereisanswer["southen foul hills"]=
	$whereisanswer["southern foot hills"]=
		"S.Fouls Hills is located in the southeast corner of the world. No grid and no whompa.North of S.Fouls Hills is Eastern Foul Plains, west is Omni Forest, northwest is Pleasant Meadows. No zones avail to south or east.";
	
	$whereisanswer["all"]=
		"See http://kuren.org/ao/helpbot for a complete list of all whereis answers.";
	
	$whereisanswer["smuggler"]=
	$whereisanswer["smuggler den"]=
	$whereisanswer["smuggler dungeon"]=
	$whereisanswer["smugglers"]=
	$whereisanswer["smuggler's"]=
	$whereisanswer["smuggler's backdoor"]=
	$whereisanswer["smugglers den"]=
	$whereisanswer["smuggler's den"]=
	$whereisanswer["smugglers en"]=
	$whereisanswer["smugglersden"]=
	$whereisanswer["smuglers den"]=
	$whereisanswer["smugler's den"]=
		"Smuggler's Den is located in the southeast corner of the world, in Southern Foul Hills at 1755x872.";
	
	$whereisanswer["artery"]=
	$whereisanswer["artery vallies"]=
	$whereisanswer["arteryvalleys"]=
		"Southern, Northern, Deep, or Central Artery Valley? Please try again.";
	
	$whereisanswer["dungeon steps of madness"]=
	$whereisanswer["som"]=
	$whereisanswer["step of madness"]=
	$whereisanswer["step of"]=
	$whereisanswer["step"]=
	$whereisanswer["stepa of"]=
	$whereisanswer["steps cyborg barracks"]=
	$whereisanswer["steps of maddness"]=
	$whereisanswer["steps of madess"]=
	$whereisanswer["steps of madnes"]=
	$whereisanswer["steps of madness"]=
	$whereisanswer["steps of madnessw"]=
	$whereisanswer["steps"]=
	$whereisanswer["stepsofmadness"]=
		"Steps of Madness is located in the southeast part of the world, in Omni Forest at 800x2800. Leave Omni Ent by the east gate to reach the dungeon. Recommended for teams of lvl 35-45.";
	
	$whereisanswer["stolts"]=
	$whereisanswer["stoltzoutpost"]=
		"Stoltz Outpost is located just across the Newland Desert zone line in Newland at 2172x1543. It has food machines only.";
	
	
	
	$whereisanswer["street"]=
	$whereisanswer["stret"]=
		"Stret East Bank or Stret West Bank or Upper Stret East Bank?  Please try again.";
	
	
	$whereisanswer["stret east bnk"]=
	$whereisanswer["2h0"]=
	$whereisanswer["2ho"]=
	$whereisanswer["east bank"]=
	$whereisanswer["street east"]=
	$whereisanswer["street east back"]=
	$whereisanswer["street east bank"]=
	$whereisanswer["stret east"]=
	$whereisanswer["stret east bank"]=
	$whereisanswer["stret east bank gold"]=
	$whereisanswer["stret east banl"]=
	$whereisanswer["stret east gold"]=
	$whereisanswer["streteastbank"]=
		"Stret East Bank is located in the mid-west part of the world. Grid access in 2HO at 667x1638, whompas at 783x1599 to Outpost 10-3 and 4 Holes.  Ferry to 4 HOles at 820x1977. To the north of Stret East Bank is Upper Stret East Bank, to the northwest is Stret West Bank, to the northeast is Central Artery Valley, to the southwest is Andromeda, to the southeast is Milky Way, to the east is Southern Artery Valley and to the west is 4 Holes.";
	
	$whereisanswer["west street"]=
	$whereisanswer["west stret"]=
	$whereisanswer["stret west ban"]=
	$whereisanswer["street west abnk"]=
	$whereisanswer["streetwest"]=
	$whereisanswer["street west"]=
	$whereisanswer["street west bank"]=
	$whereisanswer["stret west"]=
	$whereisanswer["stret west bank"]=
	$whereisanswer["stret west bank gold"]=
	$whereisanswer["stret west banl"]=
	$whereisanswer["stret west kank"]=
	$whereisanswer["stretwestbank"]=
	$whereisanswer["upper stret west bank"]=
	$whereisanswer["upper stret west bank bank"]=
		"Stret West Bank is located in the west central part of the world. It has a ferry to Stret East Bank at 1141x529 and whompa at 1279x2894 (in Last Ditch) to Borealis and to Hope. North is Aegean(ne) and Athen Shire(nw), south is 4HOles, east is Upper Stret East Bank, west is Borealis.";
	
	$whereisanswer["strike foreman"]=
		"Strike Foreman is at Subway Dungeon, usually on the bridge.";
	
	$whereisanswer["striking ant tir outpost"]=
	$whereisanswer["strikinganttiroutpost"]=
		"Striking Ant Tir Outpost is located northwest of Tir at 1915x1492. The outpost has mission terms, shops, banks and insure terms.";
	
	$whereisanswer["stumpy"]=
		"Stumpy is at Greater Omni Forest at 2000 x 1300";
	
	$whereisanswer["furor"]=
	$whereisanswer["susan"]=
	$whereisanswer["susan furor"]=
		"Susan Furor is at Poole/Galway County at 1219 x 1940 at AGT only";
	
	
	$whereisanswer["terrasque"]=
	$whereisanswer["tarasque"]=
		"Tarasque is at Avalon Dungeon, Avalon at 2092 x 3822";
	
	
	$whereisanswer["west wind temple"]=
	$whereisanswer["3 winds"]=
	$whereisanswer["4 winds"]=
	$whereisanswer["four winds"]=
	$whereisanswer["tempel 3 winds"]=
	$whereisanswer["tempel 4 winds"]=
	$whereisanswer["tempel four winds"]=
	$whereisanswer["tempel of 3 winds"]=
	$whereisanswer["tempel of 4 winds"]=
	$whereisanswer["tempel of four winds"]=
	$whereisanswer["tempel of three winds"]=
	$whereisanswer["tempel the 3 winds"]=
	$whereisanswer["tempel the 4 winds"]=
	$whereisanswer["tempel the four winds"]=
	$whereisanswer["tempel the three winds"]=
	$whereisanswer["tempel three winds"]=
	$whereisanswer["tempel"]=
	$whereisanswer["temple 3 winds"]=
	$whereisanswer["temple of 3 winds"]=
	$whereisanswer["temple of the 3 winds"]=
	$whereisanswer["temple of the 4 winds"]=
	$whereisanswer["temple of the three winds"]=
	$whereisanswer["temple of the threewinds"]=
	$whereisanswer["temple of three winds"]=
	$whereisanswer["temple of three wishes"]=
	$whereisanswer["temple of threee winds"]=
	$whereisanswer["temple of west wind"]=
	$whereisanswer["temple og three winds"]=
	$whereisanswer["temple the 3 winds"]=
	$whereisanswer["temple the three winds"]=
	$whereisanswer["temple three winds"]=
	$whereisanswer["temple three"]=
	$whereisanswer["temple"]=
	$whereisanswer["three towers"]=
	$whereisanswer["three winds"]=
	$whereisanswer["to3w"]=
	$whereisanswer["totw"]=
		"Temple of the Three Winds out the Tir west gate and go north .  There is a shortcut teleportal in the SE part of Rome Green near 420 x 240 (behind some red boxes).  You must be L 60 or below to enter.";
	
	$whereisanswer["cup"]=
	$whereisanswer["thecup"]=
		"The Cup is a quiet little club located in West Athen directly beside the grid access at 452x415.";
	
	$whereisanswer["fixer grid"]=
		"The Fixer Grid is accessable from any grid post.  You must first complete the Fixer quest, or get a L100+ fixer to help you get inside.";
	
	$whereisanswer["iron reet"]=
		"The Iron Reet is at Lush Hill Mutant Domain at 857 x 986";
	
	
	$whereisanswer["logest road"]=
	$whereisanswer["longest"]=
	$whereisanswer["longest raod"]=
	$whereisanswer["longest road"]=
	$whereisanswer["longest roaed"]=
	$whereisanswer["longest rode"]=
	$whereisanswer["longest rood"]=
	$whereisanswer["longestroad"]=
		"The Longest Road is located in the northwest part of the world. No grid access, has woompa access to Avalon, Athen Old, and Broken Shores at 3700x1615 in the town of Bliss.  Athen Shire is to the east. No zones to the north, south, or west. There is a neutral outpost at 3650x560 with an ICC scanner and more.  Biomare (dungeon) is located at 1930x775";
	
	$whereisanswer["oe"]=
	$whereisanswer["o.e."]=
	$whereisanswer["o e"]=
	$whereisanswer["obediency enforcer"]=
		"The Obediency Enforcer is at Eastern Fouls Plains, at the lake, near 1225 x 2800";
	
	$whereisanswer["baby face"]=
	$whereisanswer["babyface"]=
	$whereisanswer["one"]=
	$whereisanswer["one (babyface)"]=
		"The One (Babyface) is at Southern Fouls Hills at 2250 x 1810";
	
	$whereisanswer["pest"]=
		"The Pest is at Deep Artery Valley at 1360 x 2300";
	
	$whereisanswer["junk yard"]=
	$whereisanswer["junkyard"]=
	$whereisanswer["trash"]=
	$whereisanswer["trash king"]=
	$whereisanswer["trashking"]=
		"The Trash King is in Athens Shire, west of Athens (1600,940)";
	
	$whereisanswer["tower"]=
	$whereisanswer["tower research facility"]=
	$whereisanswer["tower shop"]=
	$whereisanswer["tower shop rome"]=
	$whereisanswer["tower shops"]=
	$whereisanswer["tower store"]=
	$whereisanswer["towershop"]=
	$whereisanswer["towershops"]=
		"There are 3 tower shops.  One in Borealis at 730x650, one in Rome Blue at 670x350, one in a Old Athen at 479x284";
	
	$whereisanswer["tir conty"]=
	$whereisanswer["tir country"]=
	$whereisanswer["tir county"]=
	$whereisanswer["tir desert"]=
	$whereisanswer["tircounty"]=
		"Tir County is located in the northeast part of the world. Grid access and whompa in Tir City. To the north is Greater Tir County, south is Deep Artery Valley (but a force field blocks you from zoning south), west is Varmint Woods, southeast is Central Artery Valley, no zones avail to east.";
	
	$whereisanswer["tir"]=
	$whereisanswer["tir advanced shop"]=
	$whereisanswer["tir back yard 1"]=
	$whereisanswer["tir backyard 1"]=
	$whereisanswer["tir city"]=
	$whereisanswer["tir fort"]=
	$whereisanswer["tir grid"]=
	$whereisanswer["tir highrise one"]=
	$whereisanswer["tir ring"]=
	$whereisanswer["tir whompa"]=
		"Tir is located in the north east part of the world. It has a grid access point at 555x527 and a whompas at 475x466 to Varmint Woods, ICC, and Athen. To the north, south, east and west of Tir is Tir County.";
	
	$whereisanswer["ancient"]=
	$whereisanswer["torrith"]=
	$whereisanswer["torrith the ancient"]=
		"Torrith the Ancient is at Greater Tir County at 400 x 2200";
	
	$whereisanswer["trash king lackey"]=
	$whereisanswer["trashkinglackey"]=
		"Trash King Lackey is in Athens Shire, west of Athens (1600,940)";
	
	$whereisanswer["plumbo"]=
	$whereisanswer["tri"]=
	$whereisanswer["tri plumbo"]=
	$whereisanswer["triplumbo"]=
		"Tri Plumbo is at Longest Road, The Foremans Office at 1940 x 775";
	
	$whereisanswer["rat"]=
	$whereisanswer["catcher"]=
	$whereisanswer["rat catcher"]=
	$whereisanswer["ratcatcher"]=
	$whereisanswer["tribo"]=
	$whereisanswer["tribo rat catcher"]=
	$whereisanswer["tribo ratcatcher"]=
		"Tribo Ratcatcher is at West Athen at 325 x 362  at (low lvl quest)";
	
	$whereisanswer["upper"]=
	$whereisanswer["upper east street bank"]=
	$whereisanswer["upper east stret bank"]=
	$whereisanswer["upper street"]=
	$whereisanswer["upper street east bank"]=
	$whereisanswer["upper stret"]=
	$whereisanswer["upper stret bank"]=
	$whereisanswer["upper stret east"]=
	$whereisanswer["upper stret east bank"]=
	$whereisanswer["upperstreteastbank"]=
		"Upper Stret East Bank is located in the central part of the world. No grid access or whompa. To the north (w) of Upper Stret East Bank is Aegean and north (e) Varmint Woods, to the east is Central Artery Valley, to the west is Stret West Bank, southeast is 4Holes, south is Stret East Bank, se is S.Artery Valley.";
	
	//$whereisanswer["600"]=
	$whereisanswer["varmint wood"]=
	$whereisanswer["varmint woods whompa"]=
	$whereisanswer["varmint woods"]=
	$whereisanswer["varmint woos"]=
	$whereisanswer["varmint"]=
	$whereisanswer["varmintwoods"]=
	$whereisanswer["varmit wood"]=
	$whereisanswer["varmit woods grid"]=
	$whereisanswer["varmit woods whompa"]=
	$whereisanswer["varmit woods whoompa"]=
	$whereisanswer["varmit woods woompa"]=
	$whereisanswer["varmit woods"]=
	$whereisanswer["varmit"]=
	$whereisanswer["varmitn woods"]=
	$whereisanswer["varnmint woods"]=
	$whereisanswer["vermint woods"]=
	$whereisanswer["vermit woods"]=
	$whereisanswer["warmint wood"]=
	$whereisanswer["warmint woods"]=
		"Varmint Woods is located in the southeast part of the world. No grid but there are woompa's to Tir, Wine, and Wailing Wastes at 2484x2106. To the north of Varmnit Woods is Newland Desert, to the south is Central Artery Valley and Stret East bank, east is Greater Tir County, west is Aegean";
	
	$whereisanswer["you"]=
	$whereisanswer["you now"]=
		"I am hanging out at the bronto burger, munching on some cheetos with some friends.";
	
	$whereisanswer["your mother"]=
	$whereisanswer["your mom"]=
		"My mother is living it up on the dance floor in Reets, last time I saw her.";
	
	$whereisanswer["your anus"]=
		"Sick!  When will you grow up?!";
	
	$whereisanswer["waldo"]=
		"Oh, aren't you the smart ass today.";
	
	$whereisanswer["helpbot"]=
		"You might as well as where is Waldo?";
	
	
	$whereisanswer["aeid"]=
	$whereisanswer["vergil"]=
	$whereisanswer["vergil aeid"]=
		"Vergil Aeid is at Subway Dungeon";
	
	$whereisanswer["wailing"]=
	$whereisanswer["wailing waits"]=
	$whereisanswer["wailing waste"]=
	$whereisanswer["wailing waste land"]=
	$whereisanswer["wailing wastelands"]=
	$whereisanswer["wailing waster"]=
	$whereisanswer["wailing wastes"]=
	$whereisanswer["wailing wastes desert"]=
	$whereisanswer["wailing wastes whompa"]=
	$whereisanswer["wailing wastesland feom me"]=
	$whereisanswer["wailingwastes"]=
	$whereisanswer["walling wastes"]=
	$whereisanswer["walling wates"]=
		"Wailing Wastes is located in the northwest part of the world. No grid access, but has whompas  at 1370x1735 to Athens, Avalon, and Varmit Woods. To the north is Avalon, to the south is Athen Shire, no zones to east and west.  Clan OP with scanner at 2430x3380";
	
	
	$whereisanswer["wortin"]=
	$whereisanswer["war torn valley"]=
	$whereisanswer["warton valley"]=
	$whereisanswer["warton vally"]=
	$whereisanswer["warton walley"]=
	$whereisanswer["warton"]=
	$whereisanswer["wartorn valle"]=
	$whereisanswer["wartorn valley"]=
	$whereisanswer["wartorn vally"]=
	$whereisanswer["wartorn walley"]=
	$whereisanswer["wartorn"]=
	$whereisanswer["wartornvalley"]=
	$whereisanswer["worton valley"]=
	$whereisanswer["worton"]=
	$whereisanswer["wortorn valle"]=
	$whereisanswer["wortorn valley"]=
	$whereisanswer["wortorn vally"]=
	$whereisanswer["wortorn walley"]=
	$whereisanswer["wortorn"]=
	$whereisanswer["wortornvalley"]=
		"Wartorn Valley is located in the northeast part of the world. No grid or whompa. To the north, east and west of Wartorn Valley is Aegean, to the south is gate to Athen Old.";
	
	$whereisanswer["pvp dungeon"]=
	$whereisanswer["pvping dungeon"]=
	$whereisanswer["dungeon of will"]=
	$whereisanswer["dungeon wil to fight"]=
	$whereisanswer["dungeon will to fight"]=
	$whereisanswer["will"]=
	$whereisanswer["will of fight"]=
	$whereisanswer["will to"]=
	$whereisanswer["will to fight"]=
	$whereisanswer["will to fight dungeon"]=
	$whereisanswer["will to right"]=
	$whereisanswer["willtofight"]=
		"Will To Fight is in Stret West Bank, east of Reet Retreet, at 2245 x 3124.  You must be L75 or above to enter.";
	
	$whereisanswer["bar in wine wine"]=
	$whereisanswer["wine"]=
	$whereisanswer["wine wompa"]=
		"Wine is a clan town in Belial Forest (east side of the world) with whompas to Broken Shores and Varmint Woods at 2150x2319.";
	
	$whereisanswer["wompa"]=
	$whereisanswer["whompas"]=
	$whereisanswer["grid"]=
	$whereisanswer["grid terminal"]=
	$whereisanswer["grid access"]=
		"There are whompas and grid terminals in most major cities.  Try /tell <me> where is <city>";
	
	$whereisanswer["southern"]=
		"Southern Atery Valley or Souther Foul Hills?  Please try again.";
	
	$whereisanswer["qi qiao jie"]=
	$whereisanswer["qiao jie"]=
	$whereisanswer["jie"]=
	$whereisanswer["qiao"]=
	$whereisanswer["valentine"]=
	$whereisanswer["valentines"]=
	$whereisanswer["flower"]=
	$whereisanswer["flowers"]=
	$whereisanswer["chocolate"]=
	$whereisanswer["chocolates"]=
		"Qi Qiao Jie is in Borealis at 720 x 675.  He sells valentines items.";
	
	$whereisanswer["eddie"]=
	$whereisanswer["eddy"]=
		"Eddie is in Stret West Bank (outside Reets Retreat) at 1212 x 2828.  He sells some random items, it seems.";
	
	$whereisanswer["gnuff"]=
	$whereisanswer["gnuf"]=
		"Gnuff is located in the Will to Fight dungeon, in the center at 1111 x 833.  He drops a Crystal of Rift Power, which will give you a random buff, like when you open a shrine.";
	
	$whereisanswer["information officer stiller"]=
	$whereisanswer["officer stiller"]=
	$whereisanswer["stiller"]=
		"Information Officer Stiller is in Avalon at the main OTAF base, at 1800 x 1288";
	
	$whereisanswer["primus"]=
	$whereisanswer["primus camp"]=
	$whereisanswer["ace camp"]=
	$whereisanswer["ace"]=
	$whereisanswer["aces"]=
		"Ace Camp is in Eastern Fouls Plains somewhere.";
	
	$whereisanswer["help"]=
		$canned_answer["help whereis"];
	
	//Goldoz	Mar 2nd, 2003 10:13:21pm Pacific Standard Time	RK1	feedback Klapam Foest is in Stret East Bank
	$whereisanswer["klapam foest"]=
	$whereisanswer["klapam forest"]=
		"Klapam Forest is in Stret East Bank";
	
	//Vortek	Mar 2nd, 2003 10:33:06pm Pacific Standard Time	RK1	feedback Sabulum is neutral town located near 1050, 2400.
	$whereisanswer["sabulum"]=
		"Sabulum is neutral town located near 1050 x 2400 in Perpetual Wastelands.";
	
	//Blindsider	Mar 5th, 2003 1:47:41pm Pacific Standard Time	RK1	feedback Sam Chin is located in Inquisitive Wasp, southeast of Tir City, at 2725,620. Deliver the supply crate from Genghis Pan in Tir.
	$whereisanswer["sam chin"]=
	$whereisanswer["chin"]=
	$whereisanswer["sam"]=
		"Sam Chin is located in Inquisitive Wasp, southeast of Tir City, at 2725,620. Deliver the supply crate from Genghis Pan in Tir.";
	
	//Bende	Mar 6th, 2003 10:52:56pm Pacific Standard Time	RK1	feedback area y is beyond galway view next to arthers pass
	$whereisanswer["area y"]=
		"Area Y is beyond Galway View next to Arthers Pass";
	
	
	
	$whereisanswer["beer"] =
	$whereisanswer["beer and booze bar"] =
	$whereisanswer["beer and booze"] =
	$whereisanswer["beer n booze"] =
	$whereisanswer["beer & booze"] =
	$whereisanswer["dancing atrox bar"] =
	$whereisanswer["dancing atrox"] =
	$whereisanswer["dancing"] =
		"Beer and Booze Bar is in Mort at 2840 x 1920, in the city called Hope.  The bar has a 5% supression gas.";
	
	$whereisanswer["relax"] =
	$whereisanswer["relax bar"] =
	$whereisanswer["relax club"] =
		"Dancing Atrox Bar is at 300 x 1850, Omni-1 Screening Area (Omni Forest).  You may know this place as called Relax Bar.";
	
	$whereisanswer["enjoy it while it lasts"] =
	$whereisanswer["enjoy it while it lasts bar"] =
	$whereisanswer["enjoy it while it lasts club"] =
		"Enjoy it While it Lasts is at 650 x 400, Tir City";
	
	$whereisanswer["happy rebel"] =
	$whereisanswer["rebel"] =
		"The Happy Rebel is at 550 x 550 in Tir City";
	
	$whereisanswer["lookout tree"] =
		"The Lookout Tree 1700 x 2500 Central Artery Valley ";
	
	$whereisanswer["carbon crystal"] =
		"The Carbon Crystal is at 2600 x 2900 in Southern Artery Valley";
	
	$whereisanswer["enigma house"] =
		"The Enigma House is at 1900 x 1400 in Central Artery Valley ";
	
	$whereisanswer["eremite statue"] =
		"The Eremite Statue is at 1300 x 2300 in Deep Artery Valley";
	
	$whereisanswer["face in the sand"] =
	$whereisanswer["face"] =
		"A Face in the Sand is at 1400 x 2750 in Aegean";
	
	$whereisanswer["galway castle model"] =
	$whereisanswer["castle model"] =
	$whereisanswer["model"] =
		"Galway Castle Model is at 1110 x 1000 in Galway County ";
	
	$whereisanswer["hotsprings"] =
	$whereisanswer["hot springs"] =
	$whereisanswer["hot spring"] =
		"Greater Tir County Hotsprings is at 2850 x 1650 in Greater Tir County";
	
	$whereisanswer["milky way spaceship crash site"] =
	$whereisanswer["spaceship crash site"] =
	$whereisanswer["crash site"] =
		"Milky Way Spaceship Crash Site is at 3300 x 700 in Milky Way";
	
	$whereisanswer["another face in the sand"] =
	$whereisanswer["another face"] =
		"Another Face in the Sand is at 1300 x 2650 in Mort";
	
	$whereisanswer["miner beetles"] =
		"Miner Beetles is at 1100 x 2300 in Pleasant Meadows";
	
	$whereisanswer["notum tree"] =
		"Notum Tree is at 2450 x 1300 in Avalon";
	
	$whereisanswer["notum cannons"] =
	$whereisanswer["notum cannon"] =
	$whereisanswer["cannon"] =
		"Notum Cannons is at 1200 x 3400 in Clondyke";
	
	$whereisanswer["satellite dish"] =
	$whereisanswer["dish"] =
	$whereisanswer["satellite"] =
		"The Satellite Dish is at 350 x 350 in Borealis";
	
	$whereisanswer["twin altars"] =
	$whereisanswer["altars"] =
		"Twin Altars is at 400 x 2250 in Broken Shores";
	
	$whereisanswer["forestwatch trees"] =
	$whereisanswer["forest watch trees"] =
		"The Forestwatch Trees is at 1650 x 1650 in Southern Fouls Hills";
	
	$whereisanswer["broken falls"] =
		"The Broken Falls is at 1450 x 3100 in Broken Shores";
	
	$whereisanswer["brutus leonidis"] =
	$whereisanswer["leonidis"] =
	$whereisanswer["brutus"] =
		"Brutus Leonidis is at 1550 x 300 in Athen Shire";
	
	$whereisanswer["eric"] =
	$whereisanswer["eric mendelson"] =
	$whereisanswer["mendelson"] =
		"Eric Mendelson is at 2500 x 2150 in Varmint Woods";
	
	$whereisanswer["harry"] =
		"Harry, himself, is at Harrys Outpost in Lush Fields";
	
	$whereisanswer["horatio campbell"] =
	$whereisanswer["campbell"] =
	$whereisanswer["horatio"] =
		"Horatio Campbell is at 300 x 200 in Omni Trade";
	
	$whereisanswer["ida schuller"] =
	$whereisanswer["ida"] =
	$whereisanswer["schuller"] =
		"Ida Schuller is in Omni-Trade";
	
	$whereisanswer["agathon"] =
	$whereisanswer["iziris agathon"] =
	$whereisanswer["iziris"] =
		"Iziris Agathon is in Northeastern Omni-Trade";
	
	$whereisanswer["marvin"] =
		"Marvin is at 1250 x 2350 in Southern Artery Valley ";
	
	$whereisanswer["moog"] =
		"Moog is at 1850 x 2550 in Southern Artery Valley";
	
	$whereisanswer["red"] =
		"Red is at 650 x 1450 in Aegean";
	
	$whereisanswer["richelieu"] =
		"Richelieu is at 1850 x 2550 in Southern Artery Valley";
	
	$whereisanswer["ron mcbain"] =
	$whereisanswer["mcbain"] =
	$whereisanswer["ron"] =
		"Ron McBain is at 750 x 1700 in Stret East Bank, at the 2HO Outpost";
	
	$whereisanswer["stanley adams"] =
	$whereisanswer["stanley"] =
	$whereisanswer["adams"] =
		"Stanley Adams is at 3850 x 1900 in Varmint Woods";
	
	$whereisanswer["supply master smug"] =
	$whereisanswer["master smug"] =
	$whereisanswer["smug"] =
		"Supply Master Smug is at the northeast corner of Wailing Wastes at 2700 x 3500 ";
	
	$whereisanswer["supply master eel"] =
	$whereisanswer["master eel"] =
	$whereisanswer["eel"] =
		"Supply Master Eel is located at the OT Secondary Base in Avalon near 900 x 1600";
	
	$whereisanswer["general freewheeler"] =
	$whereisanswer["freewheeler"] =
		"General Freewheeler is located at the Main OT Base in Avalon near 1800 x 1200";
	
	$whereisanswer["general hardcastle"] =
	$whereisanswer["hardcastle"] =
	$whereisanswer["hard castle"] =
		"General Hardcastle is located at the Main OT Base in Avalon near 1800 x 1200";
	
	$whereisanswer["high commander fielding"] =
	$whereisanswer["commander fielding"] =
	$whereisanswer["fielding"] =
		"High Commander Fielding is at the northeast corner of Wailing Wastes at 2700 x 3500 ";
	
	$whereisanswer["high commander hoover"] =
	$whereisanswer["commander hoover"] =
	$whereisanswer["hoover"] =
		"High Commander Hoover is at the northeast corner of Wailing Wastes at 2700 x 3500 ";
	
	$whereisanswer["zoftig blimp"] =
	$whereisanswer["blimp"] =
	$whereisanswer["zoftig"] =
		"Zoftig Blimp is located in the City of Hope in Mort.";
	
	$whereisanswer["melee smith"] =
	$whereisanswer["melee shop"] =
	$whereisanswer["tsunayoshi smith"] =
	$whereisanswer["tsunayoshi"] =
		"Tsunayoshi Smith is at 2600 x 2900 in Southern Artery Valley near the Largest Soul Fragment.";
	
	$whereisanswer["trader shop"] =
	$whereisanswer["trader store"] =
		"Trader shops are located inside any of the superior special stores, some times called Luxury stores.  You must be a trader level 25 or above.";
	
	$whereisanswer["bb"] =
		"BB stands for Bront Burger.  There are Bronto Burger locations all over Rubi-Ka.";
	
	$whereisanswer["basic shop"] =
		"There are basic shops in almost every city.  Far too many to list here.";
	
	$whereisanswer["cheetos"] =
	$whereisanswer["cheeto"] =
		"In my stomach.";
	
	$whereisanswer["claw camp"] =
	$whereisanswer["clawcamp"] =
	$whereisanswer["claws"] =
		"There are claw camps in many different zones.  To many to list here";
	
	$whereisanswer["dodga demercel"] =
	$whereisanswer["dodga"] =
	$whereisanswer["demercel"] =
		"Dodga Demercel is in Rising Sun, in Aegean.";
	
	//Tylnessa	Mar 10th, 2003 9:26:41pm Pacific Standard Time	RK1	feedback Brainy Ant Woods is at 1296.2x1507.1 .. when i type 'whereis brainy ant woods' it gives me 'wine' instead.  fyi.  thanks for the great bot.
	$whereisanswer["brainy ant woods"] =
	$whereisanswer["brainy ant wood"] =
	$whereisanswer["brainy ant"] =
	$whereisanswer["brainy ants"] =
	$whereisanswer["ant woods"] =
		"Brainy Ant Woods is in Greater Tir County near 1300 x 1500,";
	
	$whereisanswer["zibell the wanderer"] =
	$whereisanswer["wanderer"] =
	$whereisanswer["zibell"] =
		"Zibell the Wanderer is an npc guy who's crazy..in the Area: The great bulk, in Central Artery Valley near 3432 x 2649";
	
	// Dizmo	Mar 13th, 2003 2:08:39pm Pacific Standard Time	RK2	feedback Majestik Woods is at 400, 2520 northeast of Rome Blue
	$whereisanswer["majestik woods"] =
		"Majestik Woods is at 400 x 2520 northeast of Rome Blue";
	
	//Madaxe42: "Ofoz" "Ofoz is in Borealis, at 684, 578"
	$whereisanswer["ofoz"] =
		"Ofoz is in Borealis, at 684, 578";
	
	//Madaxe42: "Smokey Willy" "Smokey Willy is in the north western corner of Omni-Ent sewers, at 472, 1043"
	$whereisanswer["smokey willy"] =
	$whereisanswer["willy"] =
	$whereisanswer["smokey"] =
		"Smokey Willy is in the north western corner of Omni-Ent sewers, at 472, 1043";
	
	//Madaxe42: "A spolied brat" "'A spolied brat' is in a park in Omni-Ent, at 946, 350"
	$whereisanswer["a spolied brat"] =
	$whereisanswer["spolied brat"] =
	$whereisanswer["brat"] =
	$whereisanswer["spolied"] =
		"'A spolied brat' is in a park in Omni-Ent, at 946, 350";
	
	//Madaxe42: "Brenda Diamond" "Brenda Diamond is at the ruins near Home in Broken Shores at 430, 2200"
	$whereisanswer["brenda diamond"] =
	$whereisanswer["brenda"] =
		"Brenda Diamond is at the ruins near Home in Broken Shores at 430, 2200";
	
	//Madaxe42: "Sally Tall" "Sally Tall is in a small pocket of 75% gas in Meetmedere at 1480, 2760"
	$whereisanswer["sally tall"] =
	$whereisanswer["tall"] =
	$whereisanswer["sally"] =
		"Sally Tall is in a small pocket of 75% gas in Meetmedere at 1480, 2760";
	
	//Madaxe42: "Coco" "Coco is next to the bar in 'The Cup', in West Athen"
	$whereisanswer["coco"] =
		"Coco is next to the bar in 'The Cup', in West Athen";
	
	//Kahoki	Mar 19th, 2003 11:48:15am Pacific Standard Time	RK2	feedback where is me ?
	$whereisanswer["me"] =
		$canned_answer["where am i"];
	
	//Soulsnyper	Mar 20th, 2003 5:18:19am Pacific Standard Time	RK2	feedback Eden Cafe is in Omni Entertainment,South 744x417
	$whereisanswer["eden cafe"] =
	$whereisanswer["eden"] =
		"Eden Cafe is in Omni Entertainment,South 744x417";
	
	//Madaxe42: sirocco is in the SW corner of Old Athen
	$whereisanswer["sirocco"] =
		"Sirocco is in the SW corner of Old Athen";
	
	//Narsh	Mar 25th, 2003 1:41:14pm Pacific Standard Time	RK1	feedback Aztur is the boss in Temple of the Three Winds that drops the red sword called "Stygian Desolator". You need to kill Uklesh the Frozen and Khalum before he spawns.
	$whereisanswer["aztur"] =
		"Aztur is the boss in Temple of the Three Winds that drops the red sword called \"Stygian Desolator\". You need to kill Uklesh the Frozen and Khalum before he spawns.";
	
	
	//Hatchit	Apr 12th, 2003 12:37:25am Pacific Standard Time	RK2	feedback Last Ditch is a neutral city in Stret West Bank. It has whompas to Borealis and Hope at 1279x2894. It is home to the popular club 'Reet Retreat'.
	$whereisanswer["last ditch"] =
		"Last Ditch is a neutral city in Stret West Bank. It has whompas to Borealis and Hope at 1279 x 2894. It is home to the popular club 'Reet Retreat'.";
	
	
	// Cromrack	Apr 14th, 2003 8:24:51pm Pacific Standard Time	RK1	feedback Hal Goliday is in Rompas in Omni-1 Entertainment
	$whereisanswer["hal goliday"] =
		"Hal Goliday is in Rompas in Omni-1 Entertainment";
	
	
	//Clodiachifer	Apr 20th, 2003 7:59:24pm Pacific Standard Time	RK1	feedback Hermit is at 823x2301 in Tir desert
	$whereisanswer["hermit"] =
		"Hermit is at 823 x 2301 in Tir desert";
	
	//Sakassis	May 8th, 2003 10:52:42am Pacific Standard Time	RK1	Feedback  Bjorn Krax, an NPC for the Living Cyber Armor Breastplate quest is found at 750, 588 in the junkyard area of wartorn valley.
	$whereisanswer["bjorn krax"] =
	$whereisanswer["krax"] =
	$whereisanswer["bjorn"] =
		"Bjorn Krax is an NPC for the Living Cyber Armor Breastplate quest is found at 750 x 588 in the junkyard area of wartorn valley.";
	
	//Scrarub	Apr 26th, 2003 11:22:57pm Pacific Standard Time	RK1	feedback Hellfury is at Cyborg Camp in Greatir Tir County
	$whereisanswer["hellfury"] =
		"Hellfury is at Cyborg Camp in Greatir Tir County";
	$whereisanswer["javabot"] =
		"I'm right here hun!";

	// clean up the incoming message
	$query = trim($query);
	if (strcmp(substr($query, 0, 4),"the ")==0)
		$query = substr($query, 4);
	if (strcmp(substr($query, -3)," at")==0)
		$query = substr($query, 0,-3);


	//try to see if there is an imediate match
	if ($whereisanswer[$query] != "") {
		$reply = $whereisanswer[$query];
		
		if($type == "msg")
			bot::send($reply, $sender);
		elseif($type == "guild")
			bot::send($reply, "guild");
		else
			bot::send($reply);
		return;
	}

/* unquote the following, down to line 1903 if you do want the bot to search for nearest possible matches in your org chat
	// if the bot still doesn't know the answer look for the best answer with a similarity above a certain percent
	$highest_percent = 0;
	foreach ($whereisanswer as $key => $value){
		$i = similar_text($query, $key, &$similarity_percent);
		if ($similarity_percent > $highest_percent && $similarity_percent > 80){
			$reply = $value;
			$highest_percent = $similarity_percent;
		}
	}

	if($reply != "")	{
		if($type == "msg")
			bot::send($reply, $sender);
		elseif($type == "guild")
			bot::send($reply, "guild");
		else
			bot::send($reply);
		return;		
	}
	
	// if the bot still doesn't know the answer look for the best answer with a same phonetic spelling
	foreach ($whereisanswer as $key => $value)
		if (soundex($key) == soundex($query))
			$reply = $value;

	if($reply != "")	{
		if($type == "msg")
			bot::send($reply, $sender);
		elseif($type == "guild")
			bot::send($reply, "guild");
		else
			bot::send($reply);
		return;		
	}

	// if the bot still doesn't know the answer look for the best answer with a same phonetic spelling
	foreach ($whereisanswer as $key => $value)
		if (metaphone($key)==metaphone($extra))
			$reply=$value;
*/
	if($reply == "")
		$reply = "No search results, Unable to match your search criteria";

	if($type == "msg")
		bot::send($reply, $sender);
	elseif($type == "guild")
		bot::send($reply, "guild");
	else
		bot::send($reply);
} else
	$syntax_error = true;
?>
