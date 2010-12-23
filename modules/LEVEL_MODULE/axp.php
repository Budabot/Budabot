<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Alien XP List
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

//Set the required axp for every alienlvl
$axp[1] = 1500;
$axp[2] = 9000;
$axp[3] = 22500;
$axp[4] = 42000;
$axp[5] = 67500;
$axp[6] = 99000;
$axp[7] = 136500;
$axp[8] = 180000;
$axp[9] = 229500;
$axp[10] = 285000;
$axp[11] = 346500;
$axp[12] = 414000;
$axp[13] = 487500;
$axp[14] = 567000;
$axp[15] = 697410;
$axp[16] = 857814;
$axp[17] = 1055112;
$axp[18] = 1297787;
$axp[19] = 1596278;
$axp[20] = 1931497;
$axp[21] = 2298481;
$axp[22] = 2689223;
$axp[23] = 3092606;
$axp[24] = 3494645;
$axp[25] = 3879056;
$axp[26] = 4228171;
$axp[27] = 4608707;
$axp[28] = 5023490;
$axp[29] = 5475604;
$axp[30] = 5968409;

if (preg_match("/^axp$/i", $message)) {
    $msg = "<header>::::: Alien Experience List :::::<end>\n\n";
    $msg .= "<u>AI Lvl - AXP   - Rank         - Lvl Req.</u>\n";
    $msg .= " 1 -      1.500 - Fledgling - 5\n";
    $msg .= " 2 -     9.000 - Amateur - 15\n";
    $msg .= " 3 -    22.500 - Beginner - 25\n";
    $msg .= " 4 -    42.000 - Starter - 35\n";
    $msg .= " 5 -    67.500 - Newcomer - 45\n";
    $msg .= " 6 -    99.000 - Student - 55\n";
    $msg .= " 7 -   136.500 - Common - 65\n";
    $msg .= " 8 -   180.000 - Intermediate - 75\n";
    $msg .= " 9 -   229.500 - Mediocre - 85\n";
    $msg .= "10 -   285.000 - Fair - 95\n";
    $msg .= "11 -   346.500 - Able - 105\n";
    $msg .= "12 -   414.000 - Accomplished - 110\n";
    $msg .= "13 -   487.500 - Adept - 115\n";
    $msg .= "14 -   567.000 - Qualified - 120\n";
    $msg .= "15 -   697.410 - Competent - 125\n";
    $msg .= "16 -   857.814 - Suited - 130\n";
    $msg .= "17 - 1.055.112 - Talented - 135\n";
    $msg .= "18 - 1.297.787 - Trustworthy - 140\n";
    $msg .= "19 - 1.596.278 - Supporter - 145\n";
    $msg .= "20 - 1.931.497 - Backer - 150\n";
    $msg .= "21 - 2.298.481 - Defender - 155\n";
    $msg .= "22 - 2.689.223 - Challenger - 160\n";
    $msg .= "23 - 3.092.606 - Patron - 165\n";
    $msg .= "24 - 3.494.645 - Protector - 170\n";
    $msg .= "25 - 3.879.056 - Medalist - 175\n";
    $msg .= "26 - 4.228.171 - Champ - 180\n";
    $msg .= "27 - 4.608.707 - Hero - 185\n";
    $msg .= "28 - 5.023.490 - Guardian - 190\n";
    $msg .= "29 - 5.475.604 - Vanquisher - 195\n";
    $msg .= "30 - 5.968.409 - Vindicator - 200\n";

    $msg = bot::makeLink("AXP Table", $msg);

    bot::send($msg, $sendto);
} else if (preg_match("/^axp ([0-9]+)$/i", $message, $arr)) {
    if ($arr[1] >= 1 && $arr[1] <= 30) {
        $msg = "With ai lvl <highlight>".$arr[1]."<end> you need <highlight>".number_format($axp[$arr[1]])."<end> AXP to level up.";
    } else {
        $msg = "You need to specify a lvl between 1 and 30.";
	}

    bot::send($msg, $sendto);
} else if (preg_match("/^axp ([0-9]+) ([0-9]+)$/i", $message, $arr)) {
    if ($arr[1] >= 0 && $arr[1] <= 30 && $arr[2] >= 1 && $arr[2] <= 30) {
        if ($arr[1] < $arr[2]) {
            for ($i = $arr[1]+1; $i <= $arr[2]; $i++) {
                $axp_comp += $axp[$i];
			}

            $msg = "From the beginning of ai lvl <highlight>".$arr[1]."<end> to ai lvl <highlight>".$arr[2]."<end> you need <highlight>".number_format($axp_comp)."<end> AXP to level up.";
        } else {
            $msg = "The start level can't be higher then the endlevel.";
		}
    } else {
        $msg = "You need to specify a lvl between 1 and 30.";
	}

    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
