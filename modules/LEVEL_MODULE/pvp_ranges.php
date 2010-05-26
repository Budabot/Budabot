<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: PVP levelrange list
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 13.12.2005
   ** Date(last modified): 14.12.2005
   ** 
   ** Copyright (C) 2005 Carsten Lohmann
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

if(preg_match("/^pvp ([0-9]+)$/i", $message, $arr)) {
    if($arr[1] <= 220 && $arr[1] >= 1) {
        $pvp[1]="1-1";
        $pvp[2]="2-3";
        $pvp[3]="2-4";
        $pvp[4]="3-5";
        $pvp[5]="4-6";
        $pvp[6]="5-10";
        $pvp[7]="6-10";
        $pvp[8]="6-10";
        $pvp[9]="6-11";
        $pvp[10]="6-13";
        $pvp[11]="9-14";
        $pvp[12]="10-15";
        $pvp[13]="10-16";
        $pvp[14]="11-18";
        $pvp[15]="12-21";
        $pvp[16]="13-21";
        $pvp[17]="14-22";
        $pvp[18]="14-23";
        $pvp[19]="15-24";
        $pvp[20]="16-25";
        $pvp[21]="17-26";
        $pvp[22]="18-28";
        $pvp[23]="18-29";
        $pvp[24]="19-30";
        $pvp[25]="20-31";
        $pvp[26]="21-33";
        $pvp[27]="22-34";
        $pvp[28]="22-35";
        $pvp[29]="23-36";
        $pvp[30]="24-38";
        $pvp[31]="25-39";
        $pvp[32]="26-40";
        $pvp[33]="26-41";
        $pvp[34]="27-43";
        $pvp[35]="28-44";
        $pvp[36]="29-45";
        $pvp[37]="30-46";
        $pvp[38]="30-48";
        $pvp[39]="31-49";
        $pvp[40]="32-50";
        $pvp[41]="33-51";
        $pvp[42]="34-54";
        $pvp[43]="34-54";
        $pvp[44]="35-55";
        $pvp[45]="36-56";
        $pvp[46]="37-58";
        $pvp[47]="38-59";
        $pvp[48]="38-60";
        $pvp[49]="39-61";
        $pvp[50]="40-63";
        $pvp[51]="41-64";
        $pvp[52]="42-65";
        $pvp[53]="42-66";
        $pvp[54]="42-68";
        $pvp[55]="44-69";
        $pvp[56]="45-70";
        $pvp[57]="46-71";
        $pvp[58]="46-73";
        $pvp[59]="47-74";
        $pvp[60]="48-75";
        $pvp[61]="49-76";
        $pvp[62]="50-78";
        $pvp[63]="50-79";
        $pvp[64]="51-80";
        $pvp[65]="52-81";
        $pvp[66]="53-83";
        $pvp[67]="54-84";
        $pvp[68]="54-85";
        $pvp[69]="55-86";
        $pvp[70]="56-88";
        $pvp[71]="57-89";
        $pvp[72]="58-90";
        $pvp[73]="58-91";
        $pvp[74]="59-93";
        $pvp[75]="60-94";
        $pvp[76]="61-95";
        $pvp[77]="62-96";
        $pvp[78]="62-98";
        $pvp[79]="63-99";
        $pvp[80]="64-100";
        $pvp[81]="65-101";
        $pvp[82]="66-103";
        $pvp[83]="66-104";
        $pvp[84]="67-105";
        $pvp[85]="68-106";
        $pvp[86]="69-108";
        $pvp[87]="70-109";
        $pvp[88]="70-110";
        $pvp[89]="71-111";
        $pvp[90]="72-113";
        $pvp[91]="73-114";
        $pvp[92]="74-115";
        $pvp[93]="74-116";
        $pvp[94]="75-118";
        $pvp[95]="76-119";
        $pvp[96]="77-120";
        $pvp[97]="78-121";
        $pvp[98]="78-123";
        $pvp[99]="79-124";
        $pvp[100]="80-125";
        $pvp[101]="81-126";
        $pvp[102]="82-128";
        $pvp[103]="82-129";
        $pvp[104]="83-130";
        $pvp[105]="84-131";
        $pvp[106]="85-133";
        $pvp[107]="86-134";
        $pvp[108]="86-135";
        $pvp[109]="87-136";
        $pvp[110]="88-138";
        $pvp[111]="89-139";
        $pvp[112]="90-140";
        $pvp[113]="90-141";
        $pvp[114]="91-143";
        $pvp[115]="92-144";
        $pvp[116]="93-145";
        $pvp[117]="94-146";
        $pvp[118]="94-148";
        $pvp[119]="95-149";
        $pvp[120]="96-150";
        $pvp[121]="97-151";
        $pvp[122]="98-153";
        $pvp[123]="98-155";
        $pvp[124]="99-155";
        $pvp[125]="100-156";
        $pvp[126]="101-158";
        $pvp[127]="102-159";
        $pvp[128]="102-160";
        $pvp[129]="103-161";
        $pvp[130]="104-163";
        $pvp[131]="105-164";
        $pvp[132]="106-165";
        $pvp[133]="106-166";
        $pvp[134]="107-168";
        $pvp[135]="108-169";
        $pvp[136]="109-170";
        $pvp[137]="110-171";
        $pvp[138]="110-173";
        $pvp[139]="111-174";
    	$pvp[140]="112-175";
    	$pvp[141]="113-176";
        $pvp[142]="114-178";
    	$pvp[143]="114-179";
    	$pvp[144]="115-180";
    	$pvp[145]="116-181";
    	$pvp[146]="117-183";
    	$pvp[147]="118-184";
    	$pvp[148]="118-185";
    	$pvp[149]="119-186";
    	$pvp[150]="120-188";
    	$pvp[151]="121-189";
    	$pvp[152]="122-190";
    	$pvp[153]="122-191";
    	$pvp[154]="123-192";
    	$pvp[155]="123-194";
    	$pvp[156]="125-195";
    	$pvp[157]="126-196";
    	$pvp[158]="126-198";
    	$pvp[159]="127-199";
    	$pvp[160]="128-200";
    	$pvp[161]="129-201";
    	$pvp[162]="130-203";
    	$pvp[163]="130-204";
    	$pvp[164]="131-205";
    	$pvp[165]="132-206";
    	$pvp[166]="133-208";
    	$pvp[167]="134-209";
    	$pvp[168]="134-210";
    	$pvp[169]="135-211";
    	$pvp[170]="136-213";
    	$pvp[171]="137-214";
    	$pvp[172]="138-215";
    	$pvp[173]="138-218";
    	$pvp[174]="139-219";
    	$pvp[175]="140-220";
    	$pvp[176]="141-220";
    	$pvp[177]="142-220";
    	$pvp[178]="142-220";
    	$pvp[179]="143-220";
    	$pvp[180]="144-220";
    	$pvp[181]="145-220";
    	$pvp[182]="146-220";
    	$pvp[183]="146-220";
    	$pvp[184]="147-220";
    	$pvp[185]="148-220";
    	$pvp[186]="149-220";
    	$pvp[187]="150-220";
    	$pvp[188]="150-220";
    	$pvp[189]="151-220";
    	$pvp[190]="152-220";
    	$pvp[191]="153-220";
    	$pvp[192]="154-220";
    	$pvp[193]="154-220";
    	$pvp[194]="155-220";
    	$pvp[195]="156-220";
    	$pvp[196]="157-220";
    	$pvp[197]="157-220";
    	$pvp[198]="158-220";
    	$pvp[199]="159-220";
    	$pvp[200]="160-220";
    	$pvp[201]="161-220";
    	$pvp[202]="161-220";
    	$pvp[203]="162-220";
    	$pvp[204]="163-220";
    	$pvp[205]="164-220";
    	$pvp[206]="165-220";
    	$pvp[207]="165-220";
    	$pvp[208]="166-220";
    	$pvp[209]="167-220";
    	$pvp[210]="168-220";
    	$pvp[211]="169-220";
    	$pvp[212]="169-220";
    	$pvp[213]="170-220";
    	$pvp[214]="171-220";
    	$pvp[215]="172-220";
    	$pvp[216]="172-220";
    	$pvp[217]="172-220";
	    $pvp[218]="173-220";
    	$pvp[219]="174-220";
    	$pvp[220]="175-220";
        $msg = "With level <highlight>".$arr[1]."<end> you can attack players in the level range <highlight>".$pvp[$arr[1]]."<end>.";
    } else{
        $msg = "The level must be between <highlight>1<end> and <highlight>220<end>";
    }
    // Send info back
    if($type == "msg")
        bot::send($msg, $sender);
    elseif($type == "priv")
      	bot::send($msg);
    elseif($type == "guild")
      	bot::send($msg, "guild");
} else
	$syntax_error = true;
?>