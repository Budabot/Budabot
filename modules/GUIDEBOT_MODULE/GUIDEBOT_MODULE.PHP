<?php
/*
   ** Author: Plugsz (RK1)
   ** Description: Guides
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 12.21.2006
   ** Date(last modified): 12.21.2006
   ** 
   ** Copyright (C) 2006 Donald Vanatta
   **
   ** Licence Infos: 
   ** This file is for use with Budabot.
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
   
$MODULE_NAME = "GUIDEBOT_MODULE";
$PLUGIN_VERSION = 1.0;
$FOLDER = $dir;

	//A Message From Organization's Leader
	bot::command("guild", "$MODULE_NAME/message4org.php", "MESSAGE4ORG", "ALL", "A MESSAGE FROM PLUGSZ");
	bot::command("msg",   "$MODULE_NAME/message4org.php", "MESSAGE4ORG", "ALL", "A MESSAGE FROM PLUGSZ");
	bot::command("priv",  "$MODULE_NAME/message4org.php", "MESSAGE4ORG", "ALL", "A MESSAGE FROM PLUGSZ");

	//Org Depts 
	bot::command("guild", "$MODULE_NAME/orgdepts.php",  "ORGDEPTS", "ALL", "GUIDE TO ORGDEPTS");
	bot::command("msg",   "$MODULE_NAME/orgdepts.php",  "ORGDEPTS", "ALL", "GUIDE TO ORGDEPTS");
	bot::command("priv",  "$MODULE_NAME/orgdepts.php",  "ORGDEPTS", "ALL", "GUIDE TO ORGDEPTS");
	
	//Official Code Of Conduct 
	bot::command("guild", "$MODULE_NAME/official.php",  "OFFICIAL", "ALL", "OFFICIAL CODE OF CONDUCT");
	bot::command("msg",   "$MODULE_NAME/official.php",  "OFFICIAL", "ALL", "OFFICIAL CODE OF CONDUCT");
	bot::command("priv",  "$MODULE_NAME/official.php",  "OFFICIAL", "ALL", "OFFICIAL CODE OF CONDUCT");
	
	//Wrangles 
	bot::command("guild", "$MODULE_NAME/wrangle.php",   "WRANGLE", "ALL", "INFO ON WRANGLES");
	bot::command("msg",   "$MODULE_NAME/wrangle.php",   "WRANGLE", "ALL", "INFO ON WRANGLES");
	bot::command("priv",  "$MODULE_NAME/wrangle.php",   "WRANGLE", "ALL", "INFO ON WRANGLES");
	
	//Guide To Making Money 
	bot::command("guild", "$MODULE_NAME/makemoney.php", "MAKEMONEY", "ALL", "GUIDE TO MAKING MONEY");
	bot::command("msg",   "$MODULE_NAME/makemoney.php", "MAKEMONEY", "ALL", "GUIDE TO MAKING MONEY");
	bot::command("priv",  "$MODULE_NAME/makemoney.php", "MAKEMONEY", "ALL", "GUIDE TO MAKING MONEY");
	
	//Beginners FAQs
	bot::command("guild", "$MODULE_NAME/beginnersg.php", "BEGINNERSG", "ALL", "BEGINNERS FAQS");
	bot::command("msg",   "$MODULE_NAME/beginnersg.php", "BEGINNERSG", "ALL", "BEGINNERS FAQS");
	bot::command("priv",  "$MODULE_NAME/beginnersg.php", "BEGINNERSG", "ALL", "BEGINNERS FAQS");
	
	//Blitzing 
	bot::command("guild", "$MODULE_NAME/blitzingg.php", "BLITZINGG", "ALL", "GUIDE TO BLITZING");
	bot::command("msg",   "$MODULE_NAME/blitzingg.php", "BLITZINGG", "ALL", "GUIDE TO BLITZING");
	bot::command("priv",  "$MODULE_NAME/blitzingg.php", "BLITZINGG", "ALL", "GUIDE TO BLITZING");
	
	//Locations 
	bot::command("guild", "$MODULE_NAME/locations.php", "LOCATIONS", "ALL", "LOCATIONS LIST");
	bot::command("msg",   "$MODULE_NAME/locations.php", "LOCATIONS", "ALL", "LOCATIONS LIST");
	bot::command("priv",  "$MODULE_NAME/locations.php", "LOCATIONS", "ALL", "LOCATIONS LIST");
	
	//AO Terms 
	bot::command("guild", "$MODULE_NAME/terms.php",     "TERMS", "ALL", "GUIDE TO AO TERMINOLOGY");
	bot::command("msg",   "$MODULE_NAME/terms.php",     "TERMS", "ALL", "GUIDE TO AO TERMINOLOGY");
	bot::command("priv",  "$MODULE_NAME/terms.php",     "TERMS", "ALL", "GUIDE TO AO TERMINOLOGY");
	
	//The Grid 
	bot::command("guild", "$MODULE_NAME/infogrid.php",  "INFOGRID", "ALL", "GUIDE TO THE GRID");
	bot::command("msg",   "$MODULE_NAME/infogrid.php",  "INFOGRID", "ALL", "GUIDE TO THE GRID");
	bot::command("priv",  "$MODULE_NAME/infogrid.php",  "INFOGRID", "ALL", "GUIDE TO THE GRID");
	
	//Jacks Rings 
	bot::command("guild", "$MODULE_NAME/jacksrings.php",  "jacksrings", "ALL", "Jacks Professionals Rings RK");
	bot::command("msg",   "$MODULE_NAME/jacksrings.php",  "jacksrings", "ALL", "Jacks Professionals Rings RK");
	bot::command("priv",  "$MODULE_NAME/jacksrings.php",  "jacksrings", "ALL", "Jacks Professionals Rings RK");
	
	//Halloween 
	bot::command("guild", "$MODULE_NAME/halloween.php",  "halloween", "ALL", "Ferrel_s Halloween Guide");
	bot::command("msg",   "$MODULE_NAME/halloween.php",  "halloween", "ALL", "Ferrel_s Halloween Guide");
	bot::command("priv",  "$MODULE_NAME/halloween.php",  "halloween", "ALL", "Ferrel_s Halloween Guide");
	
	//IP Reset 
	bot::command("guild", "$MODULE_NAME/ipreset.php",   "IPRESET", "ALL", "GUIDE TO IP RESET");
	bot::command("msg",   "$MODULE_NAME/ipreset.php",   "IPRESET", "ALL", "GUIDE TO IP RESET");
	bot::command("priv",  "$MODULE_NAME/ipreset.php",   "IPRESET", "ALL", "GUIDE TO IP RESET");
	
	//Smugglers Den 
	bot::command("guild", "$MODULE_NAME/smugden.php",   "SMUGDEN", "ALL", "GUIDE TO SMUGGLER DEN");
	bot::command("msg",   "$MODULE_NAME/smugden.php",   "SMUGDEN", "ALL", "GUIDE TO SMUGGLER DEN");
	bot::command("priv",  "$MODULE_NAME/smugden.php",   "SMUGDEN", "ALL", "GUIDE TO SMUGGLER DEN");
	
	//Guides 
	bot::command("guild", "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	bot::command("msg",   "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	bot::command("priv",  "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	
	//Temple of Three Winds
	bot::command("guild", "$MODULE_NAME/totw.php",      "TOTW", "ALL", "GUIDE TO TEMPLE OF THREE WINDS");
	bot::command("msg",   "$MODULE_NAME/totw.php",      "TOTW", "ALL", "GUIDE TO TEMPLE OF THREE WINDS");
	bot::command("priv",  "$MODULE_NAME/totw.php",      "TOTW", "ALL", "GUIDE TO TEMPLE OF THREE WINDS");

  //Buffs
	bot::command("guild", "$MODULE_NAME/buffs.php",     "BUFFS", "ALL", "INFORMATION ABOUT ALL THE IN GAME BUFFS");
	bot::command("msg",   "$MODULE_NAME/buffs.php",     "BUFFS", "ALL", "INFORMATION ABOUT ALL THE IN GAME BUFFS");
	bot::command("priv",  "$MODULE_NAME/buffs.php",     "BUFFS", "ALL", "INFORMATION ABOUT ALL THE IN GAME BUFFS");
	
	//Foremans Biomare
	bot::command("guild", "$MODULE_NAME/biomare.php",   "BIOMARE", "ALL", "GUIDE TO FOREMANS AKA BIOMARE QUESTS");
	bot::command("msg",   "$MODULE_NAME/biomare.php",   "BIOMARE", "ALL", "GUIDE TO FOREMANS AKA BIOMARE QUESTS");
	bot::command("priv",  "$MODULE_NAME/biomare.php",   "BIOMARE", "ALL", "GUIDE TO FOREMANS AKA BIOMARE QUESTS");
	
	//Armor
	bot::command("guild", "$MODULE_NAME/armor.php",     "ARMOR", "ALL", "INFORMATION ABOUT ARMORS BY STAT AND TYPES");
	bot::command("msg",   "$MODULE_NAME/armor.php",     "ARMOR", "ALL", "INFORMATION ABOUT ARMORS BY STAT AND TYPES");
	bot::command("priv",  "$MODULE_NAME/armor.php",     "ARMOR", "ALL", "INFORMATION ABOUT ARMORS BY STAT AND TYPES");
	
	//Advy Guide
	bot::command("guild", "$MODULE_NAME/adventurer.php", "ADVYS", "ALL", "GUIDE TO ADVENTURERS");
	bot::command("msg",   "$MODULE_NAME/adventurer.php", "ADVYS", "ALL", "GUIDE TO ADVENTURERS");
	bot::command("priv",  "$MODULE_NAME/adventurer.php", "ADVYS", "ALL", "GUIDE TO ADVENTURERS");

	//Agent Guide
	bot::command("guild", "$MODULE_NAME/agent.php",     "AGENTS", "ALL", "GUIDE TO AGENTS");
	bot::command("msg",   "$MODULE_NAME/agent.php",     "AGENTS", "ALL", "GUIDE TO AGENTS");
	bot::command("priv",  "$MODULE_NAME/agent.php",     "AGENTS", "ALL", "GUIDE TO AGENTS");
	
	//Doctor Guide
	bot::command("guild", "$MODULE_NAME/doctor.php",    "DOCTORS", "ALL", "GUIDE TO DOCTORS");
	bot::command("msg",   "$MODULE_NAME/doctor.php",    "DOCTORS", "ALL", "GUIDE TO DOCTORS");
	bot::command("priv",  "$MODULE_NAME/doctor.php",    "DOCTORS", "ALL", "GUIDE TO DOCTORS");
	
	//Bureaucrat Guide
	bot::command("guild", "$MODULE_NAME/bureaucrat.php", "BUREAUCRATS", "ALL", "GUIDE TO BUREAUCRAT");
	bot::command("msg",   "$MODULE_NAME/bureaucrat.php", "BUREAUCRATS", "ALL", "GUIDE TO BUREAUCRAT");
	bot::command("priv",  "$MODULE_NAME/bureaucrat.php", "BUREAUCRATS", "ALL", "GUIDE TO BUREAUCRAT");
	
	//Enforcer Guide
	bot::command("guild", "$MODULE_NAME/enforcer.php",  "ENFORCERS", "ALL", "GUIDE TO ENFORCER");
	bot::command("msg",   "$MODULE_NAME/enforcer.php",  "ENFORCERS", "ALL", "GUIDE TO ENFORCER");
	bot::command("priv",  "$MODULE_NAME/enforcer.php",  "ENFORCERS", "ALL", "GUIDE TO ENFORCER");
	
	//ENGINEER Guide
	bot::command("guild", "$MODULE_NAME/engineers.php",  "ENGINEERS", "ALL", "GUIDE TO ENGINEERS");
	bot::command("msg",   "$MODULE_NAME/engineers.php",  "ENGINEERS", "ALL", "GUIDE TO ENGINEERS");
	bot::command("priv",  "$MODULE_NAME/engineers.php",  "ENGINEERS", "ALL", "GUIDE TO ENGINEERS");
	
	//FIXER Guide
	bot::command("guild", "$MODULE_NAME/fixer.php",     "FIXERS", "ALL", "GUIDE TO FIXER");
	bot::command("msg",   "$MODULE_NAME/fixer.php",     "FIXERS", "ALL", "GUIDE TO FIXER");
	bot::command("priv",  "$MODULE_NAME/fixer.php",     "FIXERS", "ALL", "GUIDE TO FIXER");
	
	//MARTIAL ARTIST Guide
	bot::command("guild", "$MODULE_NAME/martialartist.php", "MARTIALARTISTS", "ALL", "GUIDE TO MARTIALARTIST");
	bot::command("msg",   "$MODULE_NAME/martialartist.php", "MARTIALARTISTS", "ALL", "GUIDE TO MARTIALARTIST");
	bot::command("priv",  "$MODULE_NAME/martialartist.php", "MARTIALARTISTS", "ALL", "GUIDE TO MARTIALARTIST");
	
	//META PHYSICIST Guide
	bot::command("guild", "$MODULE_NAME/metaphysicists.php", "METAPHYSICISTS", "ALL", "GUIDE TO METAPHYSICIST");
	bot::command("msg",   "$MODULE_NAME/metaphysicists.php", "METAPHYSICISTS", "ALL", "GUIDE TO METAPHYSICIST");
	bot::command("priv",  "$MODULE_NAME/metaphysicists.php", "METAPHYSICISTS", "ALL", "GUIDE TO METAPHYSICIST");
	
	//SOLDIER Guide
	bot::command("guild", "$MODULE_NAME/soldier.php",   "SOLDIERS", "ALL", "GUIDE TO SOLDIER");
	bot::command("msg",   "$MODULE_NAME/soldier.php",   "SOLDIERS", "ALL", "GUIDE TO SOLDIER");
	bot::command("priv",  "$MODULE_NAME/soldier.php",   "SOLDIERS", "ALL", "GUIDE TO SOLDIER");
	
	//TRADER Guide
	bot::command("guild", "$MODULE_NAME/traders.php",    "TRADERS", "ALL", "GUIDE TO TRADER");
	bot::command("msg",   "$MODULE_NAME/traders.php",    "TRADERS", "ALL", "GUIDE TO TRADER");
	bot::command("priv",  "$MODULE_NAME/traders.php",    "TRADERS", "ALL", "GUIDE TO TRADER");
	
	//NANOTECH Guide
	bot::command("guild", "$MODULE_NAME/nanotech.php",  "NANOTECHS", "ALL", "GUIDE TO NANOTECHS");
	bot::command("msg",   "$MODULE_NAME/nanotech.php",  "NANOTECHS", "ALL", "GUIDE TO NANOTECHS");
	bot::command("priv",  "$MODULE_NAME/nanotech.php",  "NANOTECHS", "ALL", "GUIDE TO NANOTECHS");
	
	//Professions Guide
	bot::command("guild", "$MODULE_NAME/professions.php", "PROFESSIONS", "ALL", "GUIDE TO ALL PROFESSIONS");
	bot::command("msg",   "$MODULE_NAME/professions.php", "PROFESSIONS", "ALL", "GUIDE TO ALL PROFESSIONS");
	bot::command("priv",  "$MODULE_NAME/professions.php", "PROFESSIONS", "ALL", "GUIDE TO ALL PROFESSIONS");

	//Guides 
	bot::command("guild", "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	bot::command("msg",   "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	bot::command("priv",  "$MODULE_NAME/guides.php",    "GUIDES", "ALL", "GUIDE TO AO");
	
	//Trade NPCs 
	bot::command("guild", "$MODULE_NAME/tradenpc.php",   "TRADENPC", "ALL", "GUIDE TO UNIQUE RK NPCS");
	bot::command("msg",   "$MODULE_NAME/tradenpc.php",   "TRADENPC", "ALL", "GUIDE TO UNIQUE RK NPCS");
	bot::command("priv",  "$MODULE_NAME/tradenpc.php",   "TRADENPC", "ALL", "GUIDE TO UNIQUE RK NPCS");
	
	//Fixer Shop 
	bot::command("guild", "$MODULE_NAME/fshop.php",      "FSHOP", "ALL", "GUIDE TO FIXER SHOP");
	bot::command("msg",   "$MODULE_NAME/fshop.php",      "FSHOP", "ALL", "GUIDE TO FIXER SHOP");
	bot::command("priv",  "$MODULE_NAME/fshop.php",      "FSHOP", "ALL", "GUIDE TO FIXER SHOP");
	
	//Thin Bernice 
	bot::command("guild", "$MODULE_NAME/bernice.php",    "BERNICE", "ALL", "GUIDE TO THIN BERNICE");
	bot::command("msg",   "$MODULE_NAME/bernice.php",    "BERNICE", "ALL", "GUIDE TO THIN BERNICE");
	bot::command("priv",  "$MODULE_NAME/bernice.php",    "BERNICE", "ALL", "GUIDE TO THIN BERNICE");
	
	//Trader Shop 
	bot::command("guild", "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	bot::command("msg",   "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	bot::command("priv",  "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	
	//Zoftig Blimp 
	bot::command("guild", "$MODULE_NAME/zoftig.php",     "ZOFTIG", "ALL", "GUIDE TO ZOFTIG BLIMP");
	bot::command("msg",   "$MODULE_NAME/zoftig.php",     "ZOFTIG", "ALL", "GUIDE TO ZOFTIG BLIMP");
	bot::command("priv",  "$MODULE_NAME/zoftig.php",     "ZOFTIG", "ALL", "GUIDE TO ZOFTIG BLIMP");
	
	//MELEE SMITH 
	bot::command("guild", "$MODULE_NAME/meleesmith.php", "MELEESMITH", "ALL", "GUIDE TO MELEE SMITH");
	bot::command("msg",   "$MODULE_NAME/meleesmith.php", "MELEESMITH", "ALL", "GUIDE TO MELEE SMITH");
	bot::command("priv",  "$MODULE_NAME/meleesmith.php", "MELEESMITH", "ALL", "GUIDE TO MELEE SMITH");
	
	//TRADER SHOP 
	bot::command("guild", "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	bot::command("msg",   "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	bot::command("priv",  "$MODULE_NAME/tshop.php",      "TSHOP", "ALL", "GUIDE TO TRADER SHOP");
	
	//YALM SHOP 
	bot::command("guild", "$MODULE_NAME/infoyalm.php",      "INFOYALM", "ALL", "Locations of Yalms");
	bot::command("msg",   "$MODULE_NAME/infoyalm.php",      "INFOYALM", "ALL", "Locations of Yalms");
	bot::command("priv",  "$MODULE_NAME/infoyalm.php",      "INFOYALM", "ALL", "Locations of Yalms");
	
	//RK Info 
	bot::command("guild", "$MODULE_NAME/rkinfo.php",     "RKINFO", "ALL", "RUBI-KA LITTLE INSTRUCTION BOOK");
	bot::command("msg",   "$MODULE_NAME/rkinfo.php",     "RKINFO", "ALL", "RUBI-KA LITTLE INSTRUCTION BOOK");
	bot::command("priv",  "$MODULE_NAME/rkinfo.php",     "RKINFO", "ALL", "RUBI-KA LITTLE INSTRUCTION BOOK");
	
	//SL powerleveling guide 
	bot::command("guild", "$MODULE_NAME/slpower.php",     "SLPOWER", "ALL", "Solo Powerleveling guide for SL");
	bot::command("msg",   "$MODULE_NAME/slpower.php",     "SLPOWER", "ALL", "Solo Powerleveling guide for SL");
	bot::command("priv",  "$MODULE_NAME/slpower.php",     "SLPOWER", "ALL", "Solo Powerleveling guide for SL");
	
	//Implants 
	bot::command("guild", "$MODULE_NAME/implants.php",   "IMPLANTS", "ALL", "BASIC GUIDE TO IMPLANTS");
	bot::command("msg",   "$MODULE_NAME/implants.php",   "IMPLANTS", "ALL", "BASIC GUIDE TO IMPLANTS");
	bot::command("priv",  "$MODULE_NAME/implants.php",   "IMPLANTS", "ALL", "BASIC GUIDE TO IMPLANTS");
	
	//Hollow Island 
	bot::command("guild", "$MODULE_NAME/hollow.php",     "HOLLOW", "ALL", "GUIDE TO HOLLOW ISLAND");
	bot::command("msg",   "$MODULE_NAME/hollow.php",     "HOLLOW", "ALL", "GUIDE TO HOLLOW ISLAND");
	bot::command("priv",  "$MODULE_NAME/hollow.php",     "HOLLOW", "ALL", "GUIDE TO HOLLOW ISLAND");
	
	//Rk Dungeon Guides 
	bot::command("guild", "$MODULE_NAME/rkdung.php",     "RKDUNG", "ALL", "RK DUNGEON GUIDES");
	bot::command("msg",   "$MODULE_NAME/rkdung.php",     "RKDUNG", "ALL", "RK DUNGEON GUIDES");
	bot::command("priv",  "$MODULE_NAME/rkdung.php",     "RKDUNG", "ALL", "RK DUNGEON GUIDES");
	
	//Steps Of Madness 
	bot::command("guild", "$MODULE_NAME/stepsm.php",     "STEPSM", "ALL", "GUIDE TO STEPS OF MADNESS");
	bot::command("msg",   "$MODULE_NAME/stepsm.php",     "STEPSM", "ALL", "GUIDE TO STEPS OF MADNESS");
	bot::command("priv",  "$MODULE_NAME/stepsm.php",     "STEPSM", "ALL", "GUIDE TO STEPS OF MADNESS");
	
	//Crypt Of Home 
	bot::command("guild", "$MODULE_NAME/cryptinfo.php",  "CRYPTINFO", "ALL", "GUIDE TO CRYPT OF HOME");
	bot::command("msg",   "$MODULE_NAME/cryptinfo.php",  "CRYPTINFO", "ALL", "GUIDE TO CRYPT OF HOME");
	bot::command("priv",  "$MODULE_NAME/cryptinfo.php",  "CRYPTINFO", "ALL", "GUIDE TO CRYPT OF HOME");
	
	//Inner Sanctum 
	bot::command("guild", "$MODULE_NAME/inners.php",     "INNERS", "ALL", "GUIDE TO INNER SANCTUM");
	bot::command("msg",   "$MODULE_NAME/inners.php",     "INNERS", "ALL", "GUIDE TO INNER SANCTUM");
	bot::command("priv",  "$MODULE_NAME/inners.php",     "INNERS", "ALL", "GUIDE TO INNER SANCTUM");
		
	//NPC QUESTS 
	bot::command("guild", "$MODULE_NAME/quests.php",     "QUESTS", "ALL", "GUIDE TO NPC QUEST GUIDE");
	bot::command("msg",   "$MODULE_NAME/quests.php",     "QUESTS", "ALL", "GUIDE TO NPC QUEST GUIDE");
	bot::command("priv",  "$MODULE_NAME/quests.php",     "QUESTS", "ALL", "GUIDE TO NPC QUEST GUIDE");
		
	//Sided1 
	bot::command("guild", "$MODULE_NAME/sided1.php",     "SIDED1", "ALL", "GUIDE TO SIDED1");
	bot::command("msg",   "$MODULE_NAME/sided1.php",     "SIDED1", "ALL", "GUIDE TO SIDED1");
	bot::command("priv",  "$MODULE_NAME/sided1.php",     "SIDED1", "ALL", "GUIDE TO SIDED1");
	
	//Sided2 
	bot::command("guild", "$MODULE_NAME/sided2.php",     "SIDED2", "ALL", "GUIDE TO SIDED2");
	bot::command("msg",   "$MODULE_NAME/sided2.php",     "SIDED2", "ALL", "GUIDE TO SIDED2");
	bot::command("priv",  "$MODULE_NAME/sided2.php",     "SIDED2", "ALL", "GUIDE TO SIDED2");
		
	//Sided3 
	bot::command("guild", "$MODULE_NAME/sided3.php",     "SIDED3", "ALL", "GUIDE TO SIDED3");
	bot::command("msg",   "$MODULE_NAME/sided3.php",     "SIDED3", "ALL", "GUIDE TO SIDED3");
	bot::command("priv",  "$MODULE_NAME/sided3.php",     "SIDED3", "ALL", "GUIDE TO SIDED3");
	
	//Tailor Quest 
	bot::command("all", "$MODULE_NAME/atailor.php",      "ATAILOR", "ALL", "GUIDE TO A TAILOR WOE");
	bot::command("msg",   "$MODULE_NAME/atailor.php",    "ATAILOR", "ALL", "GUIDE TO A TAILOR WOE");
	bot::command("priv",  "$MODULE_NAME/atailor.php",    "ATAILOR", "ALL", "GUIDE TO A TAILOR WOE");	
	
	//Cloak Quest 
	bot::command("all",   "$MODULE_NAME/cloakr.php",    "cloakr", "ALL", "GUIDE TO Cloak of The Reanimated Upgrades");
	bot::command("msg",   "$MODULE_NAME/cloakr.php",    "cloakr", "ALL", "GUIDE TO Cloak of The Reanimated Upgrades");
	bot::command("priv",  "$MODULE_NAME/cloakr.php",    "cloakr", "ALL", "GUIDE TO Cloak of The Reanimated Upgrades");	
	
	//Fixer Grid Part 1 
	bot::command("guild", "$MODULE_NAME/fgridone.php",   "FGRIDONE", "ALL", "GUIDE TO FIXER GRID PART 1");
	bot::command("msg",   "$MODULE_NAME/fgridone.php",   "FGRIDONE", "ALL", "GUIDE TO FIXER GRID PART 1");
	bot::command("priv",  "$MODULE_NAME/fgridone.php",   "FGRIDONE", "ALL", "GUIDE TO FIXER GRID PART 1");
		
	//Fixer Grid Part 2 
	bot::command("guild", "$MODULE_NAME/fgrid2.php",     "FGRID2", "ALL", "GUIDE TO FIXER GRID PART 2");
	bot::command("msg",   "$MODULE_NAME/fgrid2.php",     "FGRID2", "ALL", "GUIDE TO FIXER GRID PART 2");
	bot::command("priv",  "$MODULE_NAME/fgrid2.php",     "FGRID2", "ALL", "GUIDE TO FIXER GRID PART 2");
		
	//Mission Settings 
	bot::command("guild", "$MODULE_NAME/mishsets.php",   "MISHSETS", "ALL", "GUIDE TO MISSION SETTINGS");
	bot::command("msg",   "$MODULE_NAME/mishsets.php",   "MISHSETS", "ALL", "GUIDE TO MISSION SETTINGS");
	bot::command("priv",  "$MODULE_NAME/mishsets.php",   "MISHSETS", "ALL", "GUIDE TO MISSION SETTINGS");
		
	//SL Garden Nanos 
	bot::command("guild", "$MODULE_NAME/slnano.php",      "SLNANO", "ALL", "SHADOWLANDS GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/slnano.php",      "SLNANO", "ALL", "SHADOWLANDS GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/slnano.php",      "SLNANO", "ALL", "SHADOWLANDS GARDEN NANOS");
	
	//Compact DataDisc Guide 
	bot::command("guild", "$MODULE_NAME/compact.php",     "COMPACT", "ALL", "GUIDE TO COMPACT DATA DISC QUESTS");
	bot::command("msg",   "$MODULE_NAME/compact.php",     "COMPACT", "ALL", "GUIDE TO COMPACT DATA DISC QUESTS");
	bot::command("priv",  "$MODULE_NAME/compact.php",     "COMPACT", "ALL", "GUIDE TO COMPACT DATA DISC QUESTS");

	//Shoel Quests Independants 
	bot::command("guild", "$MODULE_NAME/indieshoel.php",     "indieshoel", "ALL", "GUIDE TO Shoel Quests Independants");
	bot::command("msg",   "$MODULE_NAME/indieshoel.php",     "indieshoel", "ALL", "GUIDE TO Shoel Quests Independants");
	bot::command("priv",  "$MODULE_NAME/indieshoel.php",     "indieshoel", "ALL", "GUIDE TO Shoel Quests Independants");
	
	//Shoel Quests Independants 
	bot::command("guild", "$MODULE_NAME/shoelyutto.php",     "shoelyutto", "ALL", "GUIDE TO Shoel Quests Yuttos");
	bot::command("msg",   "$MODULE_NAME/shoelyutto.php",     "shoelyutto", "ALL", "GUIDE TO Shoel Quests Yuttos");
	bot::command("priv",  "$MODULE_NAME/shoelyutto.php",     "shoelyutto", "ALL", "GUIDE TO Shoel Quests Yuttos");
	
	//Shoel Quests Jobe 
	bot::command("guild", "$MODULE_NAME/jobeshoel.php",     "jobeshoel", "ALL", "GUIDE TO Shoel Quests Jobe Scientists");
	bot::command("msg",   "$MODULE_NAME/jobeshoel.php",     "jobeshoel", "ALL", "GUIDE TO Shoel Quests Jobe Scientists");
	bot::command("priv",  "$MODULE_NAME/jobeshoel.php",     "jobeshoel", "ALL", "GUIDE TO Shoel Quests Jobe Scientists");
		
	//Inferno Star Quest 
	bot::command("guild", "$MODULE_NAME/infstar.php",     "infstar", "ALL", "Inferno: Professions Star Quest");
	bot::command("msg",   "$MODULE_NAME/infstar.php",     "infstar", "ALL", "Inferno: Professions Star Quest");
	bot::command("priv",  "$MODULE_NAME/infstar.php",     "infstar", "ALL", "Inferno: Professions Star Quest");
			
	//Nascense Garden Nanos 
	bot::command("guild", "$MODULE_NAME/nascgar.php",     "NASCGAR", "ALL", "NASCENSE GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/nascgar.php",     "NASCGAR", "ALL", "NASCENSE GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/nascgar.php",     "NASCGAR", "ALL", "NASCENSE GARDEN NANOS");
	
	//Ely Garden Nanos 
	bot::command("guild", "$MODULE_NAME/elygar.php",      "ELYGAR", "ALL", "ELY GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/elygar.php",      "ELYGAR", "ALL", "ELY GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/elygar.php",      "ELYGAR", "ALL", "ELY GARDEN NANOS");
	
	//Ely Sanc Garden Nanos 
	bot::command("guild", "$MODULE_NAME/elysancn.php",    "ELYSANCN", "ALL", "ELY SANC GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/elysancn.php",    "ELYSANCN", "ALL", "ELY SANC GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/elysancn.php",    "ELYSANCN", "ALL", "ELY SANC GARDEN NANOS");
	
	//Shoel Garden Nanos 
	bot::command("guild", "$MODULE_NAME/shogar.php",      "SHOGAR", "ALL", "SHOEL GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/shogar.php",      "SHOGAR", "ALL", "SHOEL GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/shogar.php",      "SHOGAR", "ALL", "SHOEL GARDEN NANOS");
	
	//Shoel Sanc Garden Nanos 
	bot::command("guild", "$MODULE_NAME/shosancn.php",    "SHOSANCN", "ALL", "ELY SANC GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/shosancn.php",    "SHOSANCN", "ALL", "ELY SANC GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/shosancn.php",    "SHOSANCN", "ALL", "ELY SANC GARDEN NANOS");
	
	//ADONIS Quests 
	bot::command("guild", "$MODULE_NAME/adoquest.php",     "ADOQUEST", "ALL", "ADONIS Quests");
	bot::command("msg",   "$MODULE_NAME/adoquest.php",     "ADOQUEST", "ALL", "ADONIS Quests");
	bot::command("priv",  "$MODULE_NAME/adoquest.php",     "ADOQUEST", "ALL", "ADONIS Quests");
	
	//ADO Garden Nanos 
	bot::command("guild", "$MODULE_NAME/adogarn.php",     "ADOGARN", "ALL", "ADONIS GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/adogarn.php",     "ADOGARN", "ALL", "ADONIS GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/adogarn.php",     "ADOGARN", "ALL", "ADONIS GARDEN NANOS");
	
	//ADO Sanc Garden Nanos 
	bot::command("guild", "$MODULE_NAME/adosancn.php",    "ADOSANCN", "ALL", "ADONIS SANC GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/adosancn.php",    "ADOSANCN", "ALL", "ADONIS SANC GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/adosancn.php",    "ADOSANCN", "ALL", "ADONIS SANC GARDEN NANOS");

	//PENUMBRA Garden Nanos 
	bot::command("guild", "$MODULE_NAME/pengarn.php",     "PENGARN", "ALL", "PENUMBRA GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/pengarn.php",     "PENGARN", "ALL", "PENUMBRA GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/pengarn.php",     "PENGARN", "ALL", "PENUMBRA GARDEN NANOS");
	
	//PENUMBRA Sanc Garden Nanos 
	bot::command("guild", "$MODULE_NAME/pensancn.php",    "PENSANCN", "ALL", "PENUMBRA SANC GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/pensancn.php",    "PENSANCN", "ALL", "PENUMBRA SANC GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/pensancn.php",    "PENSANCN", "ALL", "PENUMBRA SANC GARDEN NANOS");

	//INFERNO Garden Nanos 
	bot::command("guild", "$MODULE_NAME/infgarn.php",     "INFGARN", "ALL", "INFERNO GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/infgarn.php",     "INFGARN", "ALL", "INFERNO GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/infgarn.php",     "INFGARN", "ALL", "INFERNO GARDEN NANOS");
	
	//INFERNO Sanc Garden Nanos 
	bot::command("guild", "$MODULE_NAME/infsancn.php",    "INFSANCN", "ALL", "INFERNO SANC GARDEN NANOS");
	bot::command("msg",   "$MODULE_NAME/infsancn.php",    "INFSANCN", "ALL", "INFERNO SANC GARDEN NANOS");
	bot::command("priv",  "$MODULE_NAME/infsancn.php",    "INFSANCN", "ALL", "INFERNO SANC GARDEN NANOS");
	
	//Pandemonium Vendor Nanos 
	bot::command("guild", "$MODULE_NAME/panven.php",      "PANVEN", "ALL", "PANDEMONIUM VENDOR NANOS");
	bot::command("msg",   "$MODULE_NAME/panven.php",      "PANVEN", "ALL", "PANDEMONIUM VENDOR NANOS");
	bot::command("priv",  "$MODULE_NAME/panven.php",      "PANVEN", "ALL", "PANDEMONIUM VENDOR NANOS");

	//Nascence Garden Key Omni Quest
	bot::command("guild", "$MODULE_NAME/nasckey.php",     "NASCKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/nasckey.php",     "NASCKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/nasckey.php",     "NASCKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");

	//Elysium Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/elykey.php",      "ELYKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/elykey.php",      "ELYKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/elykey.php",      "ELYKEY", "ALL", "ELYSIUM GARDEN KEY OMNI QUEST");

	//Elysium Sanctuary Garden Key Omni quest 
	bot::command("guild", "$MODULE_NAME/elysanckey.php",  "ELYSANCKEY", "ALL", "ELYSIUM SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/elysanckey.php",  "ELYSANCKEY", "ALL", "ELYSIUM SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/elysanckey.php",  "ELYSANCKEY", "ALL", "ELYSIUM SANCTUARY GARDEN KEY OMNI QUEST");

	//Shoel Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/rochkey.php",     "ROCHKEY", "ALL", "SHOEL GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/rochkey.php",     "ROCHKEY", "ALL", "SHOEL GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/rochkey.php",     "ROCHKEY", "ALL", "SHOEL GARDEN KEY OMNI QUEST");

	//Shoel Sanctuary Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/rochsanckey.php", "ROCHSANCKEY", "ALL", "SHOEL SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/rochsanckey.php", "ROCHSANCKEY", "ALL", "SHOEL SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/rochsanckey.php", "ROCHSANCKEY", "ALL", "SHOEL SANCTUARY GARDEN KEY OMNI QUEST");

	//Adonis Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/adokey.php",  "ADOKEY", "ALL", "ADONIS GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/adokey.php",     "ADOKEY", "ALL", "ADONIS GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/adokey.php",     "ADOKEY", "ALL", "ADONIS GARDEN KEY OMNI QUEST");

	//Adonis Sanctuary Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/adosanckey.php", "ADOSANCKEY", "ALL", "ADONIS SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("msg",   "$MODULE_NAME/adosanckey.php", "ADOSANCKEY", "ALL", "ADONIS SANCTUARY GARDEN KEY OMNI QUEST");
	bot::command("priv",  "$MODULE_NAME/adosanckey.php", "ADOSANCKEY", "ALL", "ADONIS SANCTUARY GARDEN KEY OMNI QUEST");
  
  //Adonis Sanctuary Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY CLAN QUEST");
	bot::command("msg",   "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY CLAN QUEST");
	bot::command("priv",  "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY CLAN QUEST");
		
	//Penumbra Sanctuary Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/pensancg.php", "pensancg", "ALL", "Penumbra Sanctuary Garden Key Omni Quest");
	bot::command("msg",   "$MODULE_NAME/pensancg.php", "pensancg", "ALL", "Penumbra Sanctuary Garden Key Omni Quest");
	bot::command("priv",  "$MODULE_NAME/pensancg.php", "pensancg", "ALL", "Penumbra Sanctuary Garden Key Omni Quest");
		
	//Inferno Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/infgarnkey.php", "infgarnkey", "ALL", "Inferno Garden Key Omni Quest");
	bot::command("msg",   "$MODULE_NAME/infgarnkey.php", "infgarnkey", "ALL", "Inferno Garden Key Omni Quest");
	bot::command("priv",  "$MODULE_NAME/infgarnkey.php", "infgarnkey", "ALL", "Inferno Garden Key Omni Quest");
		
	//Inferno Spirit Quest 
	bot::command("guild", "$MODULE_NAME/spiritquest.php", "spiritquest", "ALL", "Inferno Spirits Quest");
	bot::command("msg",   "$MODULE_NAME/spiritquest.php", "spiritquest", "ALL", "Inferno Spirits Quest");
	bot::command("priv",  "$MODULE_NAME/spiritquest.php", "spiritquest", "ALL", "Inferno Spirits Quest");
		
	//Nascence Garden Key Clan Quest
	bot::command("guild", "$MODULE_NAME/nasckeyclan.php",     "NASCKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/nasckeyclan.php",     "NASCKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/nasckeyclan.php",     "NASCKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");

	//Elysium Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/elykeyclan.php",      "ELYKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/elykeyclan.php",      "ELYKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/elykeyclan.php",      "ELYKEYCLAN", "ALL", "ELYSIUM GARDEN KEY Clan Quest");

	//Elysium Sanctuary Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/elysancclan.php",  "ELYSANCCLAN", "ALL", "ELYSIUM SANCTUARY GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/elysancclan.php",  "ELYSANCCLAN", "ALL", "ELYSIUM SANCTUARY GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/elysancclan.php",  "ELYSANCCLAN", "ALL", "ELYSIUM SANCTUARY GARDEN KEY Clan Quest");

	//Shoel Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/shoelkeyclan.php",     "SHOELKEYCLAN", "ALL", "SHOEL GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/shoelkeyclan.php",     "SHOELKEYCLAN", "ALL", "SHOEL GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/shoelkeyclan.php",     "SHOELKEYCLAN", "ALL", "SHOEL GARDEN KEY Clan Quest");

	//Shoel Sanctuary Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/shoelsancclan.php", "SHOELSANCCLAN", "ALL", "SHOEL SANCTUARY GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/shoelsancclan.php", "SHOELSANCCLAN", "ALL", "SHOEL SANCTUARY GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/shoelsancclan.php", "SHOELSANCCLAN", "ALL", "SHOEL SANCTUARY GARDEN KEY Clan Quest");

	//Adonis Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/adokeyclan.php",  "ADOKEYCLAN", "ALL", "ADONIS GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/adokeyclan.php",  "ADOKEYCLAN", "ALL", "ADONIS GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/adokeyclan.php",  "ADOKEYCLAN", "ALL", "ADONIS GARDEN KEY Clan Quest");

	//Adonis Sanctuary Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY Clan Quest");
	bot::command("msg",   "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY Clan Quest");
	bot::command("priv",  "$MODULE_NAME/adosancclan.php", "ADOSANCCLAN", "ALL", "ADONIS SANCTUARY GARDEN KEY Clan Quest");
  
	//Penumbra Sanctuary Garden Key Clan Quest 
	bot::command("guild", "$MODULE_NAME/pensancclan.php", "pensancclan", "ALL", "Penumbra Sanctuary Garden Key CLAN Quest");
	bot::command("msg",   "$MODULE_NAME/pensancclan.php", "pensancclan", "ALL", "Penumbra Sanctuary Garden Key CLAN Quest");
	bot::command("priv",  "$MODULE_NAME/pensancclan.php", "pensancclan", "ALL", "Penumbra Sanctuary Garden Key CLAN Quest");
	
	//Inferno Sanctuary Garden Key Omni Quest 
	bot::command("guild", "$MODULE_NAME/infsanckeyo.php", "infsanckeyo", "ALL", "Inferno Sanctuary Garden Key OMNI Quest");
	bot::command("msg",   "$MODULE_NAME/infsanckeyo.php", "infsanckeyo", "ALL", "Inferno Sanctuary Garden Key OMNI Quest");
	bot::command("priv",  "$MODULE_NAME/infsanckeyo.php", "infsanckeyo", "ALL", "Inferno Sanctuary Garden Key OMNI Quest");
	
	//Making Perennium Weapons 
	bot::command("guild", "$MODULE_NAME/pernweps.php",  "PERNWEPS", "ALL", "MAKING PERENNIUM WEAPONS ");
	bot::command("msg",   "$MODULE_NAME/pernweps.php",  "PERNWEPS", "ALL", "MAKING PERENNIUM WEAPONS");
	bot::command("priv",  "$MODULE_NAME/pernweps.php",  "PERNWEPS", "ALL", "MAKING PERENNIUM WEAPONS");

	//Making Jobe Armor 
	bot::command("guild", "$MODULE_NAME/jobearmor.php",  "JOBEARMOR", "ALL", "MAKING JOBE ARMOR ");
	bot::command("msg",   "$MODULE_NAME/jobearmor.php",  "JOBEARMOR", "ALL", "MAKING JOBE ARMOR");
	bot::command("priv",  "$MODULE_NAME/jobearmor.php",  "JOBEARMOR", "ALL", "MAKING JOBE ARMOR");

	//Making Tier 1 Armor 
	bot::command("guild", "$MODULE_NAME/tier1armor.php", "TIER1ARMOR", "ALL", "MAKING TIER 1 ARMOR ");
	bot::command("msg",   "$MODULE_NAME/tier1armor.php", "TIER1ARMOR", "ALL", "MAKING TIER 1 ARMOR");
	bot::command("priv",  "$MODULE_NAME/tier1armor.php", "TIER1ARMOR", "ALL", "MAKING TIER 1 ARMOR");

	//Making Tier 2 Armor 
	bot::command("guild", "$MODULE_NAME/tier2armor.php", "TIER2ARMOR", "ALL", "MAKING TIER 2 ARMOR ");
	bot::command("msg",   "$MODULE_NAME/tier2armor.php", "TIER2ARMOR", "ALL", "MAKING TIER 2 ARMOR");
	bot::command("priv",  "$MODULE_NAME/tier2armor.php", "TIER2ARMOR", "ALL", "MAKING TIER 2 ARMOR");

	//Making Tier 3 Armor 
	bot::command("guild", "$MODULE_NAME/tier3armor.php", "TIER3ARMOR", "ALL", "MAKING TIER 3 ARMOR ");
	bot::command("msg",   "$MODULE_NAME/tier3armor.php", "TIER3ARMOR", "ALL", "MAKING TIER 3 ARMOR");
	bot::command("priv",  "$MODULE_NAME/tier3armor.php", "TIER3ARMOR", "ALL", "MAKING TIER 3 ARMOR");

	//Websites 
	bot::command("guild", "$MODULE_NAME/websites.php",   "WEBSITES", "ALL", "WEBSITES ");
	bot::command("msg",   "$MODULE_NAME/websites.php",   "WEBSITES", "ALL", "WEBSITES");
	bot::command("priv",  "$MODULE_NAME/websites.php",   "WEBSITES", "ALL", "WEBSITES");
	
	//Penumbra Guide 
	bot::command("guild", "$MODULE_NAME/penguide.php",   "PENGUIDE", "ALL", "Guide To Penumbra ");
	bot::command("msg",   "$MODULE_NAME/penguide.php",   "PENGUIDE", "ALL", "Guide To Penumbra");
	bot::command("priv",  "$MODULE_NAME/penguide.php",   "PENGUIDE", "ALL", "Guide To Penumbra");
	
	//Bazzit's Quest
  bot::command("guild", "$MODULE_NAME/bazzit.php",   "BAZZIT", "ALL", "Uncle Bazzit Quest");
	bot::command("msg",   "$MODULE_NAME/bazzit.php",   "BAZZIT", "ALL", "Uncle Bazzit Quest");
	bot::command("priv",  "$MODULE_NAME/bazzit.php",   "BAZZIT", "ALL", "Uncle Bazzit Quest");

	//PVP Guide
  bot::command("guild", "$MODULE_NAME/pvp.php",   "pvpguide", "ALL", "PVP Guide");
	bot::command("msg",   "$MODULE_NAME/pvp.php",   "pvpguide", "ALL", "PVP Guide");
	bot::command("priv",  "$MODULE_NAME/pvp.php",   "pvpguide", "ALL", "PVP Guide");

	//APF 13 Guide
  bot::command("guild", "$MODULE_NAME/apf13.php",   "APF13GUIDE", "ALL", "APF 13 Guide");
	bot::command("msg",   "$MODULE_NAME/apf13.php",   "APF13GUIDE", "ALL", "APF 13 Guide");
	bot::command("priv",  "$MODULE_NAME/apf13.php",   "APF13GUIDE", "ALL", "APF 13 Guide");

	//APF 28 Guide
  bot::command("guild", "$MODULE_NAME/apf28.php",   "APF28GUIDE", "ALL", "APF 28 Guide");
	bot::command("msg",   "$MODULE_NAME/apf28.php",   "APF28GUIDE", "ALL", "APF 28 Guide");
	bot::command("priv",  "$MODULE_NAME/apf28.php",   "APF28GUIDE", "ALL", "APF 28 Guide");

	//APF 35 Guide
  bot::command("guild", "$MODULE_NAME/apf35.php",   "APF35GUIDE", "ALL", "APF 35 Guide");
	bot::command("msg",   "$MODULE_NAME/apf35.php",   "APF35GUIDE", "ALL", "APF 35 Guide");
	bot::command("priv",  "$MODULE_NAME/apf35.php",   "APF35GUIDE", "ALL", "APF 35 Guide");

	//APF 42 Guide
  bot::command("guild", "$MODULE_NAME/apf42.php",   "APF42GUIDE", "ALL", "APF 42 Guide");
	bot::command("msg",   "$MODULE_NAME/apf42.php",   "APF42GUIDE", "ALL", "APF 42 Guide");
	bot::command("priv",  "$MODULE_NAME/apf42.php",   "APF42GUIDE", "ALL", "APF 42 Guide");

  //Social Clothes
  bot::command("guild", "$MODULE_NAME/clothes.php",   "CLOTHES", "ALL", "AI Social Clothing");
	bot::command("msg",   "$MODULE_NAME/clothes.php",   "CLOTHES", "ALL", "AI Social Clothing");
	bot::command("priv",  "$MODULE_NAME/clothes.php",   "CLOTHES", "ALL", "AI Social Clothing");
	
	//Dreadloch Camps - Clan
  bot::command("guild", "$MODULE_NAME/clandreadloch.php",   "clandreadloch", "ALL", "Dreadloch Camps - Clan");
	bot::command("msg",   "$MODULE_NAME/clandreadloch.php",   "clandreadloch", "ALL", "Dreadloch Camps - Clan");
	bot::command("priv",  "$MODULE_NAME/clandreadloch.php",   "clandreadloch", "ALL", "Dreadloch Camps - Clan");

	//Dreadloch Camps - Omni
  bot::command("guild", "$MODULE_NAME/omnidreadloch.php",   "omnidreadloch", "ALL", "Dreadloch Camps - Omni");
	bot::command("msg",   "$MODULE_NAME/omnidreadloch.php",   "omnidreadloch", "ALL", "Dreadloch Camps - Omni");
	bot::command("priv",  "$MODULE_NAME/omnidreadloch.php",   "omnidreadloch", "ALL", "Dreadloch Camps - Omni");
	
  //Battle Stations
  bot::command("guild", "$MODULE_NAME/bsinfo.php",   "bsinfo", "ALL", "Battle Stations");
	bot::command("msg",   "$MODULE_NAME/bsinfo.php",   "bsinfo", "ALL", "Battle Stations");
	bot::command("priv",  "$MODULE_NAME/bsinfo.php",   "bsinfo", "ALL", "Battle Stations");

  //LE Alien Missions
  bot::command("guild", "$MODULE_NAME/lemish.php",   "lemish", "ALL", "LE Alien Missions");
	bot::command("msg",   "$MODULE_NAME/lemish.php",   "lemish", "ALL", "LE Alien Missions");
	bot::command("priv",  "$MODULE_NAME/lemish.php",   "lemish", "ALL", "LE Alien Missions");
	
	//Newcomers Alliance PvP Rules
  bot::command("guild", "$MODULE_NAME/napvp.php",   "napvp", "ALL", "Newcomers Alliance PvP Rules");
	bot::command("msg",   "$MODULE_NAME/napvp.php",   "napvp", "ALL", "Newcomers Alliance PvP Rules");
	bot::command("priv",  "$MODULE_NAME/napvp.php",   "napvp", "ALL", "Newcomers Alliance PvP Rules");
	
	//Albtraum
  bot::command("guild", "$MODULE_NAME/albainfo.php", "albainfo", "ALL", "Guide to Albtraum");
	bot::command("msg",   "$MODULE_NAME/albainfo.php", "albainfo", "ALL", "Guide to Albtraum");
	bot::command("priv",  "$MODULE_NAME/albainfo.php", "albainfo", "ALL", "Guide to Albtraum");
	
	//Arid Rift
  bot::command("guild", "$MODULE_NAME/aridrift.php", "aridrift", "ALL", "Guide to aridrift");
	bot::command("msg",   "$MODULE_NAME/aridrift.php", "aridrift", "ALL", "Guide to aridrift");
	bot::command("priv",  "$MODULE_NAME/aridrift.php", "aridrift", "ALL", "Guide to aridrift");
	
	bot::regGroup("prof_guides", $MODULE_NAME, "Guides for all Professions", "advys", "agents", "doctors", "bureaucrats", "enforcers", "engineers", "fixers", "martialartists", "metaphysicists", "soldiers", "traders", "nanotechs", "professions");
	bot::regGroup("slomni_guides", $MODULE_NAME, "Guides for various SL Garden Keys Omni", "nasckey", "elykey", "elysanckey", "rochkey", "rochsanckey", "adokey", "adosanckey", "penguide", "pensancg", "infgarnkey");
	bot::regGroup("slclan_guides", $MODULE_NAME, "Guides for various SL Garden Keys Clan", "nasckeyclan", "elykeyclan", "elysancclan", "shoelkeyclan", "shoelsancclan", "adokeyclan", "adosancclan", "pensancclan");
	bot::regGroup("slkey_guides", $MODULE_NAME, "Guides for various SL Garden Keys", "compact", "indieshoel", "shoelyutto", "jobeshoel", "slnano");
	bot::regGroup("quests", $MODULE_NAME, "Guides for RK Quests", "quests", "sided1", "sided2", "sided3", "atailor", "fgridone", "fgrid2");
	bot::regGroup("slts_guides", $MODULE_NAME, "Guides for SL Tradeskills","pernweps", "jobearmor", "tier1armor", "tier2armor", "tier3armor");
	bot::regGroup("slnano_guides", $MODULE_NAME, "Guides for SL Nanos", "nascgar", "elygar", "elysancn", "shogar", "shosancn", "adogarn", "adosancn", "pengarn", "pensancn", "infgarn", "infsancn", "panven");
	bot::regGroup("basicinfo_guides", $MODULE_NAME, "Basic AO Information Guides", "wrangle", "makemoney", "beginnersg", "blitzingg", "locations", "terms", "infogrid", "ipreset", "buffs", "armor", "rkinfo", "implants", "mishsets");
	bot::regGroup("rkdung_guides", $MODULE_NAME, "RK Static Dungeon Guides", "smugden", "totw", "biomare", "hollow", "rkdung", "stepsm", "cryptinfo", "inners");
	bot::regGroup("orgsinfo_guides", $MODULE_NAME, "Basic Org Information", "message4org", "orgdepts", "official");
	bot::regGroup("rkinfo_guides", $MODULE_NAME, "RK NPC Information", "zoftig", "meleesmith", "tshop", "fshop", "bernice", "tradenpc", "websites", "infoyalm");
	bot::regGroup("aiinfo_guides", $MODULE_NAME, "Alien Invasion Information", "bazzit", "clothes");
	bot::regGroup("leinfo_guides", $MODULE_NAME, "Lost Eden Information", "clandreadloch", "omnidreadloch", "bsinfo", "lemish" );
	bot::regGroup("alieninvasion_guides", $MODULE_NAME, "pvpguide", "apf13guide", "apf28guide", "apf35guide", "apf42guide", "aridrift"  );
  bot::regGroup("slinfo_guides", $MODULE_NAME, "SL Quest Guides", "infstar", "albainfo", "penguide", "spiritquest", "adoquest", "jobeshoel", "shoelyutto", "compact");
?>
                                                                                                                     
