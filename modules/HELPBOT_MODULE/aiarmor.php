<?php


if (preg_match("/^aiarmor (cc|cm|co|cp|cs|css|ss)$/i", $message, $arr) ||
		preg_match("/^aiarmor (cc|cm|co|cp|cs|css|ss) ([0-9]+)$/i", $message, $arr) ||
		preg_match("/^aiarmor ([0-9]+) (cc|cm|co|cp|cs|css|ss)$/i", $message, $arr2)) {

	if ($arr2) {
		$armortype = strtolower($arr2[2]);
		if ($arr2[1] >= 1 && $arr2[1] <= 300) {
			$ql = $arr2[1];
		} else {
			$ql = 300;
		}
	} else {
		$armortype = strtolower($arr[1]);
		if ($arr[2] >= 1 && $arr[2] <= 300) {
			$ql = $arr[2];
		} else {
			$ql = 300;
		}
	}
		
	$trg_ql = $ql;
  	$src_ql = ceil($trg_ql * 0.8);
  	
	switch ($armortype) {
	    case 'cc':
	    	//Result
	    	$icon_armor_result = 256308;
	    	$name_armor_result = "Combined Commando's";
	    	$lowid_armor_result = 246659;
	    	$highid_armor_result = 246659;
			//Source
			$icon_armor_src = 256362;
	    	$name_armor_src = "Strong";
	    	$lowid_armor_src = 247139;
	    	$highid_armor_src = 247139;

			//Target
			$icon_armor_trg = 256296;
	    	$name_armor_trg = "Supple";
	    	$lowid_armor_trg = 247140;
	    	$highid_armor_trg = 247141;
	    	break;
	    case 'cm':
	    	//Result
	    	$icon_armor_result = 256356;
	    	$name_armor_result = "Combined Mercenary's";
	    	$lowid_armor_result = 246637;
	    	$highid_armor_result = 246638;

			//Source
			$icon_armor_src = 256362;
	    	$name_armor_src = "Strong";
	    	$lowid_armor_src = 247139;
	    	$highid_armor_src = 247139;

			//Target
			$icon_armor_trg = 256344;
	    	$name_armor_trg = "Enduring";
	    	$lowid_armor_trg = 247137;
	    	$highid_armor_trg = 247137;
	    	break;
	    case 'co':
	    	//Result
	    	$icon_armor_result = 256320;
	    	$name_armor_result = "Combined Officer's";
	    	$lowid_armor_result = 246671;
	    	$highid_armor_result = 246672;

			//Source
			$icon_armor_src = 256332;
	    	$name_armor_src = "Spiritual";
	    	$lowid_armor_src = 247146;
	    	$highid_armor_src = 247146;

			//Target
			$icon_armor_trg = 256314;
	    	$name_armor_trg = "Arithmetic";
	    	$lowid_armor_trg = 247145;
	    	$highid_armor_trg = 247145;
	    	break;
	    case 'cp':
	    	//Result
	    	$icon_armor_result = 256350;
	    	$name_armor_result = "Combined Paramedic's";
	    	$lowid_armor_result = 246647;
	    	$highid_armor_result = 246648;

			//Source
			$icon_armor_src = 256332;
	    	$name_armor_src = "Spiritual";
	    	$lowid_armor_src = 247146;
	    	$highid_armor_src = 247146;

			//Target
			$icon_armor_trg = 256344;
	    	$name_armor_trg = "Enduring";
	    	$lowid_armor_trg = 247137;
	    	$highid_armor_trg = 247137;
	    	break;
	    case 'cs':
	    	//Result
	    	$icon_armor_result = 256326;
	    	$name_armor_result = "Combined Scout's";
	    	$lowid_armor_result = 246683;
	    	$highid_armor_result = 246684;

			//Source
			$icon_armor_src = 256338;
	    	$name_armor_src = "Observant";
	    	$lowid_armor_src = 247142;
	    	$highid_armor_src = 247143;

			//Target
			$icon_armor_trg = 256314;
	    	$name_armor_trg = "Arithmetic";
	    	$lowid_armor_trg = 247145;
	    	$highid_armor_trg = 247145;
	    	break;
		case 'css':
	    case 'ss':
	    	//Result
	    	$icon_armor_result = 256302;
	    	$name_armor_result = "Combined Sharpshooter's";
	    	$lowid_armor_result = 246695;
	    	$highid_armor_result = 246696;

			//Source
			$icon_armor_src = 256338;
	    	$name_armor_src = "Observant";
	    	$lowid_armor_src = 247142;
	    	$highid_armor_src = 247143;

			//Target
			$icon_armor_trg = 256296;
	    	$name_armor_trg = "Supple";
	    	$lowid_armor_trg = 247140;
	    	$highid_armor_trg = 247141;
	    break;
	}
	$list = "<header>::::: Building process for $ql $name_armor_result :::::<end>\n\n";
	$list .= "<u>Result</u> \n";
	$list .= "<img src=rdb://$icon_armor_result>\n";
	$list .= "<a href='itemref://$lowid_armor_result/$highid_armor_result/$ql'>QL$ql $name_armor_result</a>\n\n";

	$list .= "<u>Source Armor</u>\n";
	$list .= "<img src=rdb://$icon_armor_src>\n";
	$list .= "<a href='itemref://$lowid_armor_src/$highid_armor_src/$src_ql'>QL$src_ql $name_armor_src</a> (";
	$list .= Text::make_link("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_src $src_ql", "chatcmd").")\n\n";
	
	$list .= "<u>Target Armor</u>\n";
	$list .= "<img src=rdb://$icon_armor_trg>\n";
	$list .= "<a href='itemref://$lowid_armor_trg/$highid_armor_trg/$trg_ql'>QL$trg_ql $name_armor_trg</a> (";
	$list .= Text::make_link("Tradeskill process for this item", "/tell <myname> aiarmor $name_armor_trg $trg_ql", "chatcmd").")";
	$msg = Text::make_link("Building process for $ql $name_armor_result", $list);
	$chatBot->send($msg, $sendto);
} else if (preg_match("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual)$/i", $message, $arr) ||
		preg_match("/^aiarmor (strong|supple|enduring|observant|arithmetic|spiritual) ([0-9]+)/i", $message, $arr) ||
		preg_match("/^aiarmor ([0-9]+) (strong|supple|enduring|observant|arithmetic|spiritual)/i", $message, $arr2)) {
	
	if ($arr2) {
		$armortype = ucfirst(strtolower($arr2[2]));
		if ($arr2[1] >= 1 && $arr2[1] <= 300) {
			$ql = $arr2[1];
		} else {
			$ql = 300;
		}

	} else {
		$armortype = ucfirst(strtolower($arr[1]));
		if ($arr[2] >= 1 && $arr[2] <= 300) {
			$ql = $arr[2];
		} else {
			$ql = 300;
		}
	}

	$list = "<header>::::: Building process for $ql $armortype :::::<end>\n\n";
	
	$list .= "You need the following items to build $armortype Armor\n";
	$list .= "- Kyr'Ozch Viralbots\n";
	$list .= "- Kyr'Ozch Atomic Re-Structulazing Tool\n";
	$list .= "- Solid Clump of Kyr'Ozch Biomaterial\n";
	$list .= "- Arithmetic/Strong/Enduring/Spiritual/Observant/Supple Viralbots\n\n";

	$list .= "<highlight><u>Step 1</u><end>\n";
	$list .= "<tab><img src=rdb://100330>\n<a href='itemref://247113/247114/300'>Kyr'Ozch Viralbots</a> (<highlight>Drops of Alien City Generals<end>)\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://247098>\n<a href='itemref://247099/247099/100'>Kyr'Ozch Atomic Re-Structuralizing Tool</a> (<highlight>Drops of every Alien<end>)\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://100331>\n<a href='itemref://247118/247119/300'>Memory-Wiped Kyr'Ozch Viralbots</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 4.5)." Computer Literacy\n";
	$list .= "- ".($ql * 4.5)." Nano Programming\n\n";

	$list .= "<highlight><u>Step 2</u><end>\n";
	$list .= "<tab><img src=rdb://99279>\n<a href='itemref://161699/161699/1'>Nano Programming Interface</a> (<highlight>Can be bought in General Shops<end>)\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://100331>\n<a href='itemref://247118/247119/300'>Memory-Wiped Kyr'Ozch Viralbots</a>\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://100334>\n<a href='itemref://247120/247121/300'>Formatted Kyr'Ozch Viralbots</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 6)." Nano Programming\n\n";

	$list .= "<highlight><u>Step 3</u><end>\n";
	$list .= "<tab><img src=rdb://247097>\n<a href='itemref://247100/247100/100'>Kyr'Ozch Structural Analyzer</a>\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://247101>\n<a href='itemref://247102/247103/$ql'>QL$ql Solid Clump of Kyr'Ozch Biomaterial</a> (<highlight>Drops of every Alien<end>)\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://255705>\n<a href='itemref://247108/247109/$ql'>QL$ql Mutated Kyr'Ozch Biomaterial</a> or <a href='itemref://247106/247107/$ql'>QL$ql Pristine Kyr'Ozch Biomaterial</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 4.5)." Chemistry(for Pristine)\n";
	$list .= "- ".($ql * 7)." Chemistry(for Mutated)\n\n";
	
	$list .= "<highlight><u>Step 4</u><end>\n";
	$list .= "<tab><img src=rdb://255705>\n<a href='itemref://247108/247109/$ql'>QL$ql Mutated Kyr'Ozch Biomaterial</a> or <a href='itemref://247106/247107/$ql'>QL$ql Pristine Kyr'Ozch Biomaterial</a>\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://100333>\n<a href='itemref://247110/247110/100'>Uncle Bazzit's Generic Nano Solvent</a> (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://247115>\n<a href='itemref://247111/247112/300'>Generic Kyr'Ozch DNA Soup</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 7)." Chemistry\n\n";
	
	$list .= "<highlight><u>Step 5</u><end>\n";
	$list .= "<tab><img src=rdb://247115>\n<a href='itemref://247111/247112/300'>Generic Kyr'Ozch DNA Soup</a>\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://247122>\n<a href='itemref://247123/247123/100'>Essential Human DNA</a> (<highlight>Can be bought in Bazzit Shop in MMD<end>)\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://247116>\n<a href='itemref://247124/247125/300'>DNA Cocktail</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 6)." Pharma Tech\n\n";

	$list .= "<highlight><u>Step 6</u><end>\n";
	$list .= "<tab><img src=rdb://100334>\n<a href='itemref://247120/247121/300'>Formatted Kyr'Ozch Viralbots</a>\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://247116>\n<a href='itemref://247124/247125/300'>DNA Cocktail</a>\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://247117>\n<a href='itemref://247126/247127/300'>Kyr'Ozch Formatted Viralbot Solution</a>\n";
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 6)." Pharma Tech\n\n";

	$list .= "<highlight><u>Step 7</u><end>\n";
	$list .= "<tab><img src=rdb://247117>\n<a href='itemref://247126/247127/300'>Kyr'Ozch Formatted Viralbot Solution</a>\n";
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247163/247163/1'>Basic Vest</a> (<highlight>Can be obtained by the Basic Armor Quest<end>)\n";
	$list .= "<tab><tab>=\n";
	$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247172/247173/$ql'>Formatted Viralbot Vest</a>\n\n";

	$list .= "<highlight><u>Step 8</u><end>\n";
	$list .= "<tab><img src=rdb://100337>\n";
	
	$vb_ql = ceil($ql*0.8);
	switch ($armortype) {
		case "Arithmetic":
			$list .= "<a href='itemref://247144/247145/$vb_ql'>QL$vb_ql Arithmetic Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
		case "Supple":
			$list .= "<a href='itemref://247140/247141/$vb_ql'>QL$vb_ql Supple Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
		case "Enduring":
			$list .= "<a href='itemref://247136/247137/$vb_ql'>QL$vb_ql Enduring Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
		case "Observant":
			$list .= "<a href='itemref://247142/247143/$vb_ql'>QL$vb_ql Observant Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
		case "Strong":
			$list .= "<a href='itemref://247138/247139/$vb_ql'>QL$vb_ql Strong Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
		case "Spiritual":
			$list .= "<a href='itemref://247146/247147/$vb_ql'>QL$vb_ql Spiritual Lead Viralbots</a> (<highlight>Rare Drop off Alien City Generals<end>)\n";
			break;
	}
	$list .= "<tab><tab>+\n";
	$list .= "<tab><img src=rdb://245924>\n<a href='itemref://247172/247173/$ql'>Formatted Viralbot Vest</a></a>\n";
	$list .= "<tab><tab>=\n";
	switch ($armortype) {
		case "Arithmetic":
			$list .= "<tab><img src=rdb://256314>\n<a href='itemref://246559/246560/$ql'>QL$ql Arithmetic Body Armor</a>\n";
			break;
		case "Supple":
			$list .= "<tab><img src=rdb://256296>\n<a href='itemref://246621/246622/$ql'>QL$ql Supple Body Armor</a>\n";
			break;
		case "Enduring":
			$list .= "<tab><img src=rdb://256344>\n<a href='itemref://246579/246580/$ql'>QL$ql Enduring Body Armor</a>\n";
			break;
		case "Observant":
			$list .= "<tab><img src=rdb://256338>\n<a href='itemref://246591/246592/$ql'>QL$ql Observant Body Armor</a></a>\n";
			break;
		case "Strong":
			$list .= "<tab><img src=rdb://256362>\n<a href='itemref://246616/246617/$ql'>QL$ql Strong Body Armor</a>\n";
			break;
		case "Spiritual":
			$list .= "<tab><img src=rdb://256332>\n<a href='itemref://246600/246601/$ql'>QL$ql Spiritual Body Armor</a>\n";
			break;
	}
	$list .= "<highlight>Required Skills:<end>\n";
	$list .= "- ".($ql * 6)." Psychology\n\n";
		
	$msg = Text::make_link("Building process for $ql $armortype", $list);
	$chatBot->send($msg, $sendto);
} else {
 	$msg = "<red>Unknown Syntax or wrong Armor specified<end>! Use one of the following: <highlight>cc<end>, <highlight>cm<end>, <highlight>co<end>, <highlight>cp<end>, <highlight>cs<end>, <highlight>ss<end>, <highlight>strong<end>, <highlight>supple<end>, <highlight>enduring<end>, <highlight>observant<end>, <highlight>arithmetic<end> or <highlight>spiritual.<end>";
	$chatBot->send($msg, $sendto);
}
?>