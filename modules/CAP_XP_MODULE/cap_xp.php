<?
   /*
   ** Author: Legendadv (RK2)
   ** Description: Shows research % needed to still get maximum xp/sk from a mission/high level mob
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 7/13/2009
   ** Date(last modified): 7/24/2009
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

// Create an area for the experience list
$level[1] = 1450;
$level[2] = 2600;
$level[3] = 3100;
$level[4] = 4000;
$level[5] = 4500;
$level[6] = 5000;
$level[7] = 5500;
$level[8] = 6000;
$level[9] = 6500;
$level[10] = 7000;
$level[11] = 7700;
$level[12] = 8300;
$level[13] = 8900;
$level[14] = 9600;
$level[15] = 10400;
$level[16] = 11000;
$level[17] = 11900;
$level[18] = 12700;
$level[19] = 13700;
$level[20] = 15400;
$level[21] = 16400;
$level[22] = 17600;
$level[23] = 18800;
$level[24] = 20100;
$level[25] = 21500;
$level[26] = 22900;
$level[27] = 24500;
$level[28] = 26100;
$level[29] = 27800;
$level[30] = 30900;
$level[31] = 33000;
$level[32] = 35100;
$level[33] = 37400;
$level[34] = 39900;
$level[35] = 42400;
$level[36] = 45100;
$level[37] = 47900;
$level[38] = 50900;
$level[39] = 54000;
$level[40] = 57400;
$level[41] = 60900;
$level[42] = 64500;
$level[43] = 68400;
$level[44] = 76400;
$level[45] =  81000;
$level[46] = 85900;
$level[47] = 91000;
$level[48] = 96400;
$level[49] = 101900;
$level[50] = 108000;
$level[51] = 114300;
$level[52] = 120800;
$level[53] = 127700;
$level[54] = 135000;
$level[55] = 142600;
$level[56] = 150700;
$level[57] = 161900;
$level[58] = 167800;
$level[59] = 177100;
$level[60] = 203500;
$level[61] = 214700;
$level[62] = 226700;
$level[63] = 239100;
$level[64] = 251900;
$level[65] = 265700;
$level[66] = 280000;
$level[67] = 294800;
$level[68] = 310600;
$level[69] = 327000;
$level[70] = 344400;
$level[71] = 362300;
$level[72] = 381100;
$level[73] = 401000;
$level[74] = 421600;
$level[75] = 443300;
$level[76] = 508100;
$level[77] = 534200;
$level[78] = 561600;
$level[79] = 590200;
$level[80] = 620000;
$level[81] = 651000;
$level[82] = 683700;
$level[83] = 717900;
$level[84] = 753500;
$level[85] = 790800;
$level[86] = 829400;
$level[87] = 870000;
$level[88] = 912600;
$level[89] = 956800;
$level[90] = 1003000;
$level[91] = 1051300;
$level[92] = 1101500;
$level[93] = 1153900;
$level[94] = 1208800;
$level[95] = 1266000;
$level[96] = 1325500;
$level[97] = 1387700;
$level[98] = 1452300;
$level[99] = 1519900;
$level[100] = 1590300;
$level[101] = 1663500;
$level[102] = 1739900;
$level[103] = 1819600;
$level[104] = 1902200;
$level[105] = 1988900;
$level[106] = 2078600;
$level[107] = 2172100;
$level[108] = 2269800;
$level[109] = 2371100;
$level[110] = 2476600;
$level[111] = 2586600;
$level[112] = 2701000;
$level[113] = 2819800;
$level[114] = 2943600;
$level[115] = 3072400;
$level[116] = 3205800;
$level[117] = 3345200;
$level[118] = 3489700;
$level[119] = 3640200;
$level[120] = 3796500;
$level[121] = 3958900;
$level[122] = 4128000;
$level[123] = 4303400;
$level[124] = 4485700;
$level[125] = 4674800;
$level[126] = 4871700;
$level[127] = 5075700;
$level[128] = 5288100;
$level[129] = 5508200;
$level[130] = 5736800;
$level[131] = 5974600;
$level[132] = 6220700;
$level[133] = 6476500;
$level[134] = 6742200;
$level[135] = 7017500;
$level[136] = 7303700;
$level[137] = 7600100;
$level[138] = 7907600;
$level[139] = 8227000;
$level[140] = 8557700;
$level[141] = 8901000;
$level[142] = 9256800;
$level[143] = 9625800;
$level[144] = 10008600;
$level[145] = 10405300;
$level[146] = 10816600;
$level[147] = 11242500;
$level[148] = 11684300;
$level[149] = 12141900;
$level[150] = 12616200;
$level[151] = 13107200;
$level[152] = 13616100;
$level[153] = 14143600;
$level[154] = 14689700;
$level[155] = 15255300;
$level[156] = 15841000;
$level[157] = 16447900;
$level[158] = 17075800;
$level[159] = 17725900;
$level[160] = 18399400;
$level[161] = 19096100;
$level[162] = 19817500;
$level[163] = 20564100;
$level[164] = 21336600;
$level[165] = 22136100;
$level[166] = 22963600;
$level[167] = 23819700;
$level[168] = 24705200;
$level[169] = 25621100;
$level[170] = 26569000;
$level[171] = 27548800;
$level[172] = 28562900;
$level[173] = 29611100;
$level[174] = 30695300;
$level[175] = 31816300;
$level[176] = 32975100;
$level[177] = 34173500;
$level[178] = 35412500;
$level[179] = 36692500;
$level[180] = 38016500;
$level[181] = 39384400;
$level[182] = 40797700;
$level[183] = 42258500;
$level[184] = 43768300;
$level[185] = 45328100;
$level[186] = 46939900;
$level[187] = 48604900;
$level[188] = 50324600;
$level[189] = 52101200;
$level[190] = 53936300;
$level[191] = 55831600;
$level[192] = 57788700;
$level[193] = 59810000;
$level[194] = 61897000;
$level[195] = 64052200;
$level[196] = 66277200;
$level[197] = 68574400;
$level[198] = 70945700;
$level[199] = 73393900;
$level[200] = 80000;
$level[201] = 96000;
$level[202] = 115200;
$level[203] = 138240;
$level[204] = 165888;
$level[205] = 199066;
$level[206] = 238879;
$level[207] = 286654;
$level[208] = 343985;
$level[209] = 412782;
$level[210] = 495339;
$level[211] = 594407;
$level[212] = 713288;
$level[213] = 855946;
$level[214] = 1027135;
$level[215] = 1232562;
$level[216] = 1479074;
$level[217] = 1774889;
$level[218] = 2129867;
$level[219] = 2555840;

if (preg_match("/^(capxp|capsk) ([0-9]+)/i$", $message, $arr)) {
		//get player lvl
		$rk_num = $this->vars["dimension"];
		$info = new whois($sender);
		
		if ($info->errorCode != 0) {
			bot::send("An Error occurred while trying to get your level. Please input it manually via <highlight><symbol>capxp 'mission reward' 'your lvl'<end> or try again later.", $sendto);
			return;
		}
		else {
			$lvl = $info->level;
			$cont = "on";
		}
}
else if (preg_match("/^(capxp|capsk) ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
	
	if (($arr[3] > 220) || ($arr[3] < 1)) {
		bot::send("Your level cannot be greater than 220 or less than 1.", $sendto);
		return;
	}
	else {
		$lvl = $arr[3];
		$cont = "on";
	}
}

if (($cont == "on") && ($arr[2] >= 300)) {
	if($lvl < 200) {
		$xp = $level[$lvl];
		$research = (1-(($xp*.2)/$arr[2]))*100;
	} else {
		$sk = $level[$lvl];
		$research = (1-(($sk*.2)/$arr[2]))*100;
	}
	if($research < 0)
		$research = 0;
	
	if($sk) {
		$msg = "At lvl <highlight>".number_format($lvl)."<end> you need <highlight>".number_format($sk)."<end> sk to level. With a mission reward of <highlight>".number_format($arr[2])."<end> sk, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum sk from this mission reward.";
	} elseif(!$sk) {
		$msg = "At lvl <highlight>".number_format($lvl)."<end> you need <highlight>".number_format($xp)."<end> xp to level. With a mission reward of <highlight>".number_format($arr[2])."<end> xp, set your research bar to <highlight>".ceil($research)."%<end> to receive maximum xp from this mission reward.";
	}
} elseif ($arr[2] < 300) {
	 $msg = "Usage: <highlight><symbol>capxp 'mission reward amount' 'custom level'<end><br><tab>ex: !capxp 165000 215<br>If no level is specified, it will use your current level.";
}
	
bot::send($msg, $sendto);

?>
