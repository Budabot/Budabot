<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Shows the loottable of apf sectors
   ** Version: 0.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 16.03.2006
   ** Date(last modified): 17.03.2006
   ** 
   ** Copyright (C) 2006 Carsten Lohmann
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

if (preg_match("/^apf (13|28|35)$/i", $message, $arr)) {
	$sector = $arr[1];

	$tradelink["ICE"] = "<a href='chatcmd:///tell <myname> guide Hacker ICE-Breaker Source'>Click here</a>";
	$itemlink["ICE"] = Text::make_item(257968, 257968, 1, "Hacker ICE-Breaker Source");
	$tradelink["BOARD"] = "<a href='chatcmd:///tell <myname> guide KyrOzch Helmet'>Click here</a>";
	$itemlink["BOARD"] = Text::make_item(257706, 257706, 1, "Kyr'Ozch Helmet");
	$tradelink["APE"] = "<a href='chatcmd:///tell <myname> guide Action Probability Estimator'>Click here</a>";
	$itemlink["APE"] = Text::make_item(257960, 257960, 250, "Action Probability Estimator");
	$tradelink["DGRV"] = "<a href='chatcmd:///tell <myname> guide Dynamic Gas Redistribution Valves'>Click here</a>";
	$itemlink["DGRV"] = Text::make_item(257962, 257962, 250, "Dynamic Gas Redistribution Valves");
	$itemlink["KBAP"] = Text::make_item(257529, 257529, 1, "Kyr'Ozch Battlesuit Audio Processor");
	$itemlink["KVPU"] = Text::make_item(257533, 257533, 1, "Kyr'Ozch Video Processing Unit");
	$itemlink["KRI"] = Text::make_item(257531, 257531, 1, "Kyr'Ozch Rank Identification");	
	$itemlink["ICEU"] = Text::make_item(257110, 257110, 1, "Intrusion Countermeasure Electronics Upgrade");
	$itemlink["OTAE"] = Text::make_item(257112, 257112, 1, "Omni-Tek Award - Exemplar");
	$itemlink["CMP"] = Text::make_item(257113, 257113, 1, "Clan Merits - Paragon");
	$itemlink["EMCH"] = Text::make_item(257379, 257379, 200, "Extruder's Molybdenum Crash Helmet");
	$itemlink["CKCNH"] = Text::make_item(257115, 257115, 200, "Conscientious Knight Commander Nizno's Helmet");
	$itemlink["SKCGH"] = Text::make_item(257114, 257114, 200, "Sworn Knight Commander Genevra's Helmet");
	$itemlink["BCOH"] = Text::make_item(257383, 257383, 300, "Blackmane's Combined Officer's Headwear");		
	$itemlink["GCCH"] = Text::make_item(257381, 257381, 300, "Gannondorf's Combined Commando's Headwear");		
	$itemlink["HCSH"] = Text::make_item(257384, 257384, 300, "Haitte's Combined Sharpshooter's Headwear");		
	$itemlink["OCPH"] = Text::make_item(257377, 257377, 300, "Odum's Combined Paramedic's Headwear");		
	$itemlink["SCMH"] = Text::make_item(257380, 257380, 300, "Sillum's Combined Mercenary's Headwear");		
	$itemlink["YCSH"] = Text::make_item(257382, 257382, 300, "Yakomo's Combined Scout's Headwear");		
	$itemlink["HLOA"] = Text::make_item(257128, 257128, 300, "High Lord of Angst");
	$itemlink["SKR2"] = Text::make_item(257967, 257967, 300, "Silenced Kyr'Ozch Rifle - Type 2");
	$itemlink["SKR3"] = Text::make_item(257131, 257131, 300, "Silenced Kyr'Ozch Rifle - Type 3");
	$itemlink["ASC"] = Text::make_item(257126, 257126, 300, "Amplified Sleek Cannon");
	$itemlink["IAPU"] = Text::make_item(257959, 257959, 1, "Inertial Adjustment Processing Unit");
	$itemlink["HVBCP"] = Text::make_item(257119, 257119, 300, "Hadrulf's Viral Belt Component Platform");
	$tradelink["BELT"] = "<a href='chatcmd:///tell <myname> guide Inertial Adjustment Processing Unit'>Click here</a>";
	$itemlink["NAC"] = Text::make_item(257963, 257963, 250, "Notum Amplification Coil");
	$itemlink["TAHSC"] = Text::make_item(257124, 257124, 300, "Twice Augmented Hellspinner Shock Cannon");
	$itemlink["ONC"] = Text::make_item(257118, 257118, 250, "ObiTom's Nano Calculator");
	$itemlink["AKC12"] = Text::make_item(257143, 257143, 300, "Amplified Kyr'Ozch Carbine - Type 12");
	$itemlink["AKC13"] = Text::make_item(257142, 257142, 300, "Amplified Kyr'Ozch Carbine - Type 13");
	$itemlink["AKC5"] = Text::make_item(257144, 257144, 300, "Amplified Kyr'Ozch Carbine - Type 5");	
	$tradelink["NAC"] = "<a href='chatcmd:///tell <myname> guide Notum Amplification Coil'>Click here</a>";
	$itemlink["ERU"] = Text::make_item(257961, 257961, 250, "Energy Redistribution Unit");	
	$itemlink["BOB"] = Text::make_item(257147, 257147, 300, "Blades of Boltar");	
	$itemlink["DVLPR"] = Text::make_item(257116, 257116, 1, "De'Valos Lava Protection Ring");	
	$tradelink["ERU"] = "<a href='chatcmd:///tell <myname> guide Energy Redistribution Unit'>Click here</a>";
	$itemlink["VLRD"] = Text::make_item(257964, 257964, 250, "Visible Light Remodulation Device");	
	$itemlink["DVRPR"] = Text::make_item(257117, 257117, 1, "De'Valos Radiation Protection Ring");	
	$itemlink["SSSS"] = Text::make_item(257141, 257141, 300, "Scoped Salabim Shotgun Supremo");	
	$itemlink["EPP"] = Text::make_item(258345, 258345, 300, "Explosif's Polychromatic Pillows");	
	$tradelink["VLRD"] = "<a href='chatcmd:///tell <myname> guide Visible Light Remodulation Device'>Click here</a>";
	$itemlink["VNGW"] = Text::make_item(257123, 257123, 300, "Vektor ND Grand Wyrm");	
	$list = "<header>::::: Loottable for Sector $sector :::::<end>\n\n";
	
	switch($sector) {
	  	case "13":
	  		//CRU
			$list .= "<img src=rdb://257196> \n";
			$list .= "<highlight>Name:<end> {$itemlink["ICE"]}\n";
			$list .= "<highlight>Purpose:<end> {$itemlink["ICEU"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["ICE"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield 5 times from the Boss.\n\n";

			//Token Credit Items
			$list .= "<img src=rdb://218775> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KBAP"]}\n";
			$list .= "<img src=rdb://218758> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KVPU"]}\n";
			$list .= "<img src=rdb://218768> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KRI"]}\n";
			$list .= "<highlight>Purpose:<end> Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss(One from each type).\n\n";
			
			//Token Board
			$list .= "<img src=rdb://230855> \n";
			$list .= "<highlight>Name:<end> {$itemlink["BOARD"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["OTAE"]}\n";
			$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["BOARD"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss.\n\n";
			
			//Action Probability Estimator
			$list .= "<img src=rdb://203502> \n";
			$list .= "<highlight>Name:<end> {$itemlink["APE"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["EMCH"]}\n";
			$list .= "<tab><tab>     - {$itemlink["CKCNH"]}\n";
			$list .= "<tab><tab>     - {$itemlink["SKCGH"]}\n";
			$list .= "<tab><tab>     - {$itemlink["BCOH"]}\n";
			$list .= "<tab><tab>     - {$itemlink["GCCH"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["HCSH"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["OCPH"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["SCMH"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["YCSH"]}\n";			
			$list .= "<highlight>Guide:<end> {$tradelink["APE"]}\n\n";

			//Dynamic Gas Redistribution Valves
			$list .= "<img src=rdb://205508> \n";
			$list .= "<highlight>Name:<end> {$itemlink["DGRV"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["HLOA"]}\n";
			$list .= "<tab><tab>     - {$itemlink["SKR2"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["SKR3"]}\n";
			$list .= "<tab><tab>     - {$itemlink["ASC"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["DGRV"]}\n\n";
	  	break;
	  	case "28":
	  		//CRU
			$list .= "<img src=rdb://257196> \n";
			$list .= "<highlight>Name:<end> {$itemlink["ICE"]}\n";
			$list .= "<highlight>Purpose:<end> {$itemlink["ICEU"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["ICE"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield 5 times from the Boss.\n\n";

			//Token Credit Items
			$list .= "<img src=rdb://218775> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KBAP"]}\n";
			$list .= "<img src=rdb://218758> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KVPU"]}\n";
			$list .= "<img src=rdb://218768> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KRI"]}\n";
			$list .= "<highlight>Purpose:<end> Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss(One from each type).\n\n";
			
			//Token Board
			$list .= "<img src=rdb://230855> \n";
			$list .= "<highlight>Name:<end> {$itemlink["BOARD"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["OTAE"]}\n";
			$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["BOARD"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss.\n\n";
			
			//APF Belt
			$list .= "<img src=rdb://11618> \n";
			$list .= "<highlight>Name:<end> {$itemlink["IAPU"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["HVBCP"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["BELT"]}\n\n";			

			//Notum coil
			$list .= "<img src=rdb://257195> \n";
			$list .= "<highlight>Name:<end> {$itemlink["NAC"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["TAHSC"]}\n";
			$list .= "<tab><tab>     - {$itemlink["ONC"]}\n";
			$list .= "<tab><tab>     - {$itemlink["AKC12"]}\n";			
			$list .= "<tab><tab>     - {$itemlink["AKC13"]}\n";
			$list .= "<tab><tab>     - {$itemlink["AKC5"]}\n";			
			$list .= "<highlight>Guide:<end> {$tradelink["NAC"]}\n";
	  	break;
	  	case "35":
	  		//CRU
			$list .= "<img src=rdb://257196> \n";
			$list .= "<highlight>Name:<end> {$itemlink["ICE"]}\n";
			$list .= "<highlight>Purpose:<end> {$itemlink["ICEU"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["ICE"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield 5 times from the Boss.\n\n";

			//Token Credit Items
			$list .= "<img src=rdb://218775> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KBAP"]}\n";
			$list .= "<img src=rdb://218758> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KVPU"]}\n";
			$list .= "<img src=rdb://218768> \n";
			$list .= "<highlight>Name:<end> {$itemlink["KRI"]}\n";
			$list .= "<highlight>Purpose:<end> Kyr'Ozch Rank Identification, Kyr'Ozch Video Processing Unit and Kyr'Ozch Battlesuit Audio Processor can be traded at your faction vendor at the Alien Playfield Bar for Tokens or Credits.\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss(One from each type).\n\n";
			
			//Token Board
			$list .= "<img src=rdb://230855> \n";
			$list .= "<highlight>Name:<end> {$itemlink["BOARD"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["OTAE"]}\n";
			$list .= "<tab><tab>     - {$itemlink["CMP"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["BOARD"]}\n";
			$list .= "<highlight>Note:<end> Drops on all Alien Playfield from the Boss.\n\n";

			//Energy Redistribution Unit
			$list .= "<img src=rdb://257197> \n";
			$list .= "<highlight>Name:<end> {$itemlink["ERU"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["BOB"]}\n";
			$list .= "<tab><tab>     - {$itemlink["DVLPR"]}\n";
			$list .= "<tab><tab>     - {$itemlink["VNGW"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["ERU"]}\n\n";
			
			//Visible Light Remodulation Device
			$list .= "<img src=rdb://235270> \n";
			$list .= "<highlight>Name:<end> {$itemlink["VLRD"]}\n";
			$list .= "<highlight>Purpose:<end> - {$itemlink["DVRPR"]}\n";
			$list .= "<tab><tab>     - {$itemlink["SSSS"]}\n";
			$list .= "<tab><tab>     - {$itemlink["EPP"]}\n";
			$list .= "<highlight>Guide:<end> {$tradelink["VLRD"]}\n";
	  	break;
	}

	$msg = Text::make_link("Loot table for sector $sector", $list);

	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>