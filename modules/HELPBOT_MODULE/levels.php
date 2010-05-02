<?php
   /*
   ** Author: Derroylo (RK2) (Updated by Blackruby RK2)
   ** Description: Shows level infos
   ** Version: 1.1
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 20.12.2005
   ** Date(last modified): 21.10.2006
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

if(eregi("^(level|lvl) ([0-9]+)$", $message, $arr)) {
    if($arr[2] <= 220 && $arr[2] >= 1) {
        $level[1]="<white>L 1: team 1-5<highlight> | <red> PvP 1-1 <highlight> | <yellow>1,450 XP<highlight> |<orange>  Missions 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1 <highlight>|<blue> 1 token(s)<highlight>";
        $level[2]="<white>L 2: team 2-5<highlight> | <red> PvP 2-3 <highlight> | <yellow>2,600 XP<highlight> |<orange>  Missions 3, 3, 2, 2, 2, 2, 1, 1, 1, 1, 1 <highlight>|<blue> 1 token(s)<highlight>";
        $level[3]="<white>L 3: team 3-5<highlight> | <red> PvP 2-4 <highlight> | <yellow>3,100 XP<highlight> |<orange>  Missions 5, 4, 4, 3, 3, 3, 2, 2, 2, 2, 2 <highlight>|<blue> 1 token(s)<highlight>";
        $level[4]="<white>L 4: team 4-8<highlight> | <red> PvP 3-5 <highlight> | <yellow>4,000 XP<highlight> |<orange>  Missions 7, 6, 5, 4, 4, 4, 3, 3, 3, 3, 2 <highlight>|<blue> 1 token(s)<highlight>";
	    $level[5]="<white>L 5: team 3-10<highlight> | <red> PvP 4-6 <highlight> | <yellow>4,500 XP<highlight> |<orange>  Missions 8, 7, 6, 6, 5, 5, 4, 4, 4, 3, 3 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[6]="<white>L 6: team 4-11<highlight> | <red> PvP 5-10 <highlight> | <yellow>5,000 XP<highlight> |<orange>  Missions 10, 9, 7, 7, 6, 6, 5, 5, 4, 4, 4 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[7]="<white>L 7: team 5-11<highlight> | <red> PvP 6-10 <highlight> | <yellow>5,500 XP<highlight> |<orange>  Missions 12, 10, 9, 8, 7, 7, 6, 5, 5, 5, 4 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[8]="<white>L 8: team 5-11<highlight> | <red> PvP 6-10 <highlight> | <yellow>6,000 XP<highlight> |<orange>  Missions 14, 12, 10, 9, 8, 8, 7, 6, 6, 6, 5 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[9]="<white>L 9: team 5-12<highlight> | <red> PvP 6-11 <highlight> | <yellow>6,500 XP<highlight> |<orange>  Missions 16, 13, 11, 10, 9, 9, 8, 7, 7, 6, 6 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[10]="<white>L 10: team 6-15<highlight> | <red> PvP 6-13 <highlight> | <yellow>7,000 XP<highlight> |<orange>  Missions 18, 15, 13, 12, 11, 10, 9, 8, 8, 7, 7 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[11]="<white>L 11: team 6-16<highlight> | <red> PvP 9-14 <highlight> | <yellow>7,700 XP<highlight> |<orange>  Missions 19, 16, 14, 13, 12, 11, 9, 9, 8, 8, 7 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[12]="<white>L 12: team 6-17<highlight> | <red> PvP 10-15 <highlight> | <yellow>8,300 XP<highlight> |<orange>  Missions 21, 18, 15, 14, 13, 12, 10, 10, 9, 9, 8 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[13]="<white>L 13: team 7-19<highlight> | <red> PvP 10-16 <highlight> | <yellow>8,900 XP<highlight> |<orange>  Missions 22, 19, 16, 15, 14, 13, 11, 11, 10, 9, 9 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[14]="<white>L 14: team 9-19<highlight> | <red> PvP 11-18 <highlight> | <yellow>9,600 XP<highlight> |<orange>  Missions 24, 21, 18, 16, 15, 14, 12, 11, 11, 10, 9 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[15]="<white>L 15: team 10-20<highlight> | <red> PvP 12-21 <highlight> | <yellow>XP 10,400<highlight> | <orange>  Missions 26, 22, 19, 18, 16, 15, 13, 12, 12, 11, 10 <highlight>|<blue> 1 token(s)<highlight>";
     	$level[16]="<white>L 16: team 11-21<highlight> | <red> PvP 13-21 <highlight> | <yellow>XP 11,000<highlight> | <orange>  Missions 28, 24, 20, 19, 17, 16, 14, 13, 12, 12, 11 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[17]="<white>L 17: team 12-23<highlight> | <red> PvP 14-22 <highlight> | <yellow>XP 11,900<highlight> | <orange>  Missions 30, 25, 22, 20, 18, 17, 15, 14, 13, 12, 11 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[18]="<white>L 18: team 13-24<highlight> | <red> PvP 14-23 <highlight> | <yellow>XP 12,700<highlight> | <orange>  Missions 32, 27, 23, 21, 19, 18, 16, 15, 14, 13, 12 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[19]="<white>L 19: team 13-26<highlight> | <red> PvP 15-24 <highlight> | <yellow>XP 13,700<highlight> | <orange>  Missions 34, 28, 24, 22, 20, 19, 17, 16, 15, 14, 13 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[20]="<white>L 20: team 14-28<highlight> | <red> PvP 16-25 <highlight> | <yellow>XP 15,400<highlight> | <orange>  Missions 36, 30, 26, 24, 22, 20, 18, 17, 16, 15, 14 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[21]="<white>L 21: team 15-29<highlight> | <red> PvP 17-26 <highlight> | <yellow>XP 16,400<highlight> | <orange>  Missions 37, 31, 27, 25, 23, 21, 18, 17, 16, 15, 14 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[22]="<white>L 22: team 15-30<highlight> | <red> PvP 18-28 <highlight> | <yellow>17,600<highlight> | <orange>  Missions 38, 33, 28, 26, 24, 22, 19, 18, 17, 16, 15 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[23]="<white>L 23: team 15-31<highlight> | <red> PvP 18-29 <highlight> | <yellow>18,800<highlight> | <orange>  Missions 41, 34, 29, 27, 25, 23, 20, 19, 18, 17, 16 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[24]="<white>L 24: team 16-33<highlight> | <red> PvP 19-30 <highlight> | <yellow>20,100<highlight> | <orange>  Missions 42, 36, 31, 28, 26, 24, 21, 20, 19, 18, 16 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[25]="<white>L 25: team 19-34<highlight> | <red> PvP 20-31 <highlight> | <yellow>21,500<highlight> | <orange>  Missions 44, 37, 32, 30, 27, 25, 22, 21, 20, 18, 17 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[26]="<white>L 26: team 20-36<highlight> | <red> PvP 21-33 <highlight> | <yellow>22,900<highlight> | <orange>  Missions 46, 39, 33, 31, 28, 26, 23, 22, 20, 19, 18 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[27]="<white>L 27: team 20-37<highlight> | <red> PvP 22-34 <highlight> | <yellow>24,500<highlight> | <orange>  Missions 48, 40, 35, 32, 29, 27, 24, 22, 21, 20, 18 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[28]="<white>L 28: team 20-38<highlight> | <red> PvP 22-35 <highlight> | <yellow>26,100<highlight> | <orange>  Missions 50, 42, 36, 33, 30, 28, 25, 23, 22, 21, 19 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[29]="<white>L 29: team 22-40<highlight> | <red> PvP 23-36 <highlight> | <yellow>27,800<highlight> | <orange>  Missions 51, 43, 37, 34, 31, 29, 26, 24, 23, 21, 20 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[30]="<white>L 30: team 22-41<highlight> | <red> PvP 24-38 <highlight> | <yellow>30,900<highlight> | <orange>  Missions 53, 45, 39, 36, 33, 30, 27, 25, 24, 22, 21 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[31]="<white>L 31: team 23-42<highlight> | <red> PvP 25-39 <highlight> | <yellow>33,000<highlight> | <orange>  Missions 55, 46, 40, 37, 34, 31, 27, 26, 24, 23, 21 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[32]="<white>L 32: team 24-44<highlight> | <red> PvP 26-40 <highlight> | <yellow>35,100<highlight> | <orange>  Missions 56, 48, 41, 38, 35, 32, 28, 27, 25, 24, 22 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[33]="<white>L 33: team 24-46<highlight> | <red> PvP 26-41 <highlight> | <yellow>37,400<highlight> | <orange>  Missions 58, 49, 42, 39, 36, 33, 29, 28, 26, 24, 23 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[34]="<white>L 34: team 25-47<highlight> | <red> PvP 27-43 <highlight> | <yellow>39,900<highlight> | <orange>  Missions 60, 51, 44, 40, 37, 34, 30, 28, 27, 25, 23 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[35]="<white>L 35: team 26-48<highlight> | <red> PvP 28-44 <highlight> | <yellow>42,400<highlight> | <orange>  Missions 62, 52, 45, 42, 38, 35, 31, 29, 28, 26, 24 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[36]="<white>L 36: team 26-49<highlight> | <red> PvP 29-45 <highlight> | <yellow>45,100<highlight> | <orange>  Missions 64, 54, 46, 43, 39, 36, 32, 30, 28, 27, 25 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[37]="<white>L 37: team 27-51<highlight> | <red> PvP 30-46 <highlight> | <yellow>47,900<highlight> | <orange>  Missions 66, 55, 48, 44, 40, 37, 33, 31, 29, 27, 25 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[38]="<white>L 38: team 28-52<highlight> | <red> PvP 30-48 <highlight> | <yellow>50,900<highlight> | <orange>  Missions 68, 57, 49, 45, 41, 38, 34, 32, 30, 28, 26 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[39]="<white>L 39: team 29-55<highlight> | <red> PvP 31-49 <highlight> | <yellow>54,000<highlight> | <orange>  Missions 69, 58, 50, 46, 42, 39, 35, 33, 31, 29, 27 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[40]="<white>L 40: team 30-55<highlight> | <red> PvP 32-50 <highlight> | <yellow>57,400<highlight> | <orange>  Missions 71, 60, 52, 48, 44, 40, 36, 34, 32, 30, 28 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[41]="<white>L 41: team 30-57<highlight> | <red> PvP 33-51 <highlight> | <yellow>60,900<highlight> | <orange>  Missions 73, 61, 53, 49, 45, 41, 36, 34, 32, 30, 28 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[42]="<white>L 42: team 31-58<highlight> | <red> PvP 34-54 <highlight> | <yellow>64,500<highlight> | <orange>  Missions 75, 63, 54, 50, 46, 42, 37, 35, 33, 31, 29 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[43]="<white>L 43: team 32-59<highlight> | <red> PvP 34-54 <highlight> | <yellow>68,400<highlight> | <orange>  Missions 77, 64, 55, 51, 47, 43, 38, 36, 34, 32, 30 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[44]="<white>L 44: team 32-61<highlight> | <red> PvP 35-55 <highlight> | <yellow>76,400<highlight> | <orange>  Missions 78, 66, 57, 52, 48, 44, 39, 37, 35, 33, 30 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[45]="<white>L 45: team 33-62<highlight> | <red> PvP 36-56 <highlight> | <yellow>81,000<highlight> | <orange>  Missions 80, 67, 58, 54, 49, 45, 40, 38, 36, 33, 31 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[46]="<white>L 46: team 33-63<highlight> | <red> PvP 37-58 <highlight> | <yellow>85,900<highlight> | <orange>  Missions 82, 69, 59, 55, 50, 46, 41, 39, 36, 34, 32 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[47]="<white>L 47: team 35-65<highlight> | <red> PvP 38-59 <highlight> | <yellow>91,000<highlight> | <orange>  Missions 84, 70, 61, 56, 51, 47, 42, 39, 37, 35, 32 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[48]="<white>L 48: team 35-66<highlight> | <red> PvP 38-60 <highlight> | <yellow>96,400<highlight> | <orange>  Missions 85, 72, 62, 57, 52, 48, 43, 40, 38, 36, 33 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[49]="<white>L 49: team 36-68<highlight> | <red> PvP 39-61 <highlight> | <yellow>101,900<highlight> | <orange>  Missions 87, 73, 63, 58, 53, 49, 44, 41, 39, 36, 34 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[50]="<white>L 50: team 37-69<highlight> | <red> PvP 40-63 <highlight> | <yellow>108,000<highlight> | <orange>  Missions 89, 75, 65, 60, 55, 50, 45, 42, 40, 37, 35 <highlight>|<blue> 2 token(s)<highlight>";
     	$level[51]="<white>L 51: team 37-70<highlight> | <red> PvP 41-64 <highlight> | <yellow>114,300<highlight> | <orange>  Missions 91, 76, 66, 61, 56, 51, 45, 43, 40, 38, 35 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[52]="<white>L 52: team 38-72<highlight> | <red> PvP 42-65 <highlight> | <yellow>120,800<highlight> | <orange>  Missions 92, 78, 67, 62, 57, 52, 46, 44, 41, 39, 36 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[53]="<white>L 53: team 39-73<highlight> | <red> PvP 42-66 <highlight> | <yellow>127,700<highlight> | <orange>  Missions 95, 79, 68, 63, 58, 53, 47, 45, 42, 39, 37 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[54]="<white>L 54: team 39-75<highlight> | <red> PvP 42-68 <highlight> | <yellow>135,000<highlight> | <orange>  Missions 97, 81, 70, 64, 59, 54, 48, 45, 43, 40, 37 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[55]="<white>L 55: team 39-76<highlight> | <red> PvP 44-69 <highlight> | <yellow>142,600<highlight> | <orange>  Missions 98, 82, 71, 66, 60, 55, 49, 46, 44, 41, 38 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[56]="<white>L 56: team 41-77<highlight> | <red> PvP 45-70 <highlight> | <yellow>150,700<highlight> | <orange>  Missions 100, 84, 72, 67, 61, 56, 50, 47, 44, 42, 39 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[57]="<white>L 57: team 41-79<highlight> | <red> PvP 46-71 <highlight> | <yellow>161,900<highlight> | <orange>  Missions 102, 85, 74, 68, 62, 57, 51, 48, 45, 42, 39 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[58]="<white>L 58: team 42-80<highlight> | <red> PvP 46-73 <highlight> | <yellow>167,800<highlight> | <orange>  Missions 103, 87, 75, 69, 63, 58, 52, 49, 46, 43, 40 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[59]="<white>L 59: team 43-83<highlight> | <red> PvP 47-74 <highlight> | <yellow>177,100<highlight> | <orange>  Missions 105, 88, 76, 70, 64, 59, 53, 50, 47, 44, 41 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[60]="<white>L 60: team 44-83<highlight> | <red> PvP 48-75 <highlight> | <yellow>203,500<highlight> | <orange>  Missions 108, 90, 78, 72, 66, 60, 54, 51, 48, 45, 42 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[61]="<white>L 61: team 45-84<highlight> | <red> PvP 49-76 <highlight> | <yellow>214,700<highlight> | <orange>  Missions 109, 91, 79, 73, 67, 61, 54, 51, 48, 45, 42 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[62]="<white>L 62: team 45-86<highlight> | <red> PvP 50-78 <highlight> | <yellow>226,700<highlight> | <orange>  Missions 110, 93, 80, 74, 68, 62, 55, 52, 49, 46, 43 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[63]="<white>L 63: team 46-87<highlight> | <red> PvP 50-79 <highlight> | <yellow>239,100<highlight> | <orange>  Missions 112, 94, 81, 75, 69, 63, 56, 53, 50, 47, 44 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[64]="<white>L 64: team 47-89<highlight> | <red> PvP 51-80 <highlight> | <yellow>251,900<highlight> | <orange>  Missions 114, 96, 83, 76, 70, 64, 57, 54, 51, 48, 44 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[65]="<white>L 65: team 47-90<highlight> | <red> PvP 52-81 <highlight> | <yellow>265,700<highlight> | <orange>  Missions 116, 97, 84, 78, 71, 65, 58, 55, 52, 48, 45 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[66]="<white>L 66: team 48-91<highlight> | <red> PvP 53-83 <highlight> | <yellow>280,000<highlight> | <orange>  Missions 118, 99, 85, 79, 72, 66, 59, 56, 52, 49, 46 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[67]="<white>L 67: team 49-93<highlight> | <red> PvP 54-84 <highlight> | <yellow>294,800<highlight> | <orange>  Missions 120, 100, 87, 80, 73, 67, 60, 56, 53, 50, 46 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[68]="<white>L 68: team 50-94<highlight> | <red> PvP 54-85 <highlight> | <yellow>310,600<highlight> | <orange>  Missions 121, 102, 88, 81, 74, 68, 61, 57, 54, 51, 47 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[69]="<white>L 69: team 50-96<highlight> | <red> PvP 55-86 <highlight> | <yellow>327,000<highlight> | <orange>  Missions 123, 103, 89, 82, 75, 69, 62, 58, 55, 51, 48 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[70]="<white>L 70: team 51-97<highlight> | <red> PvP 56-88 <highlight> | <yellow>344,400<highlight> | <orange>  Missions 125, 105, 91, 84, 77, 70, 63, 59, 56, 52, 49 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[71]="<white>L 71: team 52-98<highlight> | <red> PvP 57-89 <highlight> | <yellow>362,300<highlight> | <orange>  Missions 127, 106, 92, 85, 78, 71, 63, 60, 56, 53, 49 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[72]="<white>L 72: team 52-99<highlight> | <red> PvP 58-90 <highlight> | <yellow>381,100<highlight> | <orange>  Missions 128, 108, 93, 86, 79, 72, 64, 61, 57, 54, 50 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[73]="<white>L 73: team 53-100<highlight> | <red> PvP 58-91 <highlight> | <yellow>401,000<highlight> | <orange>  Missions 130, 109, 94, 87, 80, 73, 65, 62, 58, 54, 51 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[74]="<white>L 74: team 54-102<highlight> | <red> PvP 59-93 <highlight> | <yellow>421,600<highlight> | <orange>  Missions 132, 111, 96, 88, 81, 74, 66, 62, 59, 55, 51 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[75]="<white>L 75: team 55-104<highlight> | <red> PvP 60-94 <highlight> | <yellow>443,300<highlight> | <orange>  Missions 134, 112, 97, 90, 82, 75, 67, 63, 60, 56, 52 <highlight>|<blue> 3 token(s)<highlight>";
     	$level[76]="<white>L 76: team 55-105<highlight> | <red> PvP 61-95 <highlight> | <yellow>508,100<highlight> | <orange>  Missions 135, 114, 98, 91, 83, 76, 68, 64, 60, 57, 53 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[77]="<white>L 77: team 56-107<highlight> | <red> PvP 62-96 <highlight> | <yellow>534,200<highlight> | <orange>  Missions 137, 115, 100, 91, 83, 77, 69, 64, 60, 57, 53 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[78]="<white>L 78: team 57-108<highlight> | <red> PvP 62-98 <highlight> | <yellow>561,600<highlight> | <orange>  Missions 138, 117, 101, 93, 85, 78, 70, 66, 62, 58, 54 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[79]="<white>L 79: team 57-110<highlight> | <red> PvP 63-99 <highlight> | <yellow>590,200<highlight> | <orange>  Missions 139, 118, 102, 94, 86, 79, 71, 67, 63, 59, 55 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[80]="<white>L 80: team 58-111<highlight> | <red> PvP 64-100 <highlight> | <yellow>620,000<highlight> | <orange>  Missions 144, 120, 104, 96, 88, 80, 72, 68, 64, 60, 56 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[81]="<white>L 81: team 59-112<highlight> | <red> PvP 65-101 <highlight> | <yellow>651,000<highlight> | <orange>  Missions 145, 121, 105, 97, 89, 81, 72, 68, 64, 60, 56 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[82]="<white>L 82: team 60-114<highlight> | <red> PvP 66-103 <highlight> | <yellow>683,700<highlight> | <orange>  Missions 146, 123, 106, 98, 90, 82, 73, 69, 65, 61, 57 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[83]="<white>L 83: team 60-115<highlight> | <red> PvP 66-104 <highlight> | <yellow>717,900<highlight> | <orange>  Missions 148, 124, 107, 99, 91, 83, 74, 70, 66, 62, 58 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[84]="<white>L 84: team 61-117<highlight> | <red> PvP 67-105 <highlight> | <yellow>753,500<highlight> | <orange>  Missions 149, 126, 109, 100, 92, 84, 75, 71, 67, 63, 58 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[85]="<white>L 85: team 62-118<highlight> | <red> PvP 68-106 <highlight> | <yellow>790,800<highlight> | <orange>  Missions 151, 127, 110, 102, 93, 85, 76, 72, 68, 63, 59 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[86]="<white>L 86: team 62-119<highlight> | <red> PvP 69-108 <highlight> | <yellow>829,400<highlight> | <orange>  Missions 154, 129, 111, 103, 94, 86, 77, 73, 68, 64, 60 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[87]="<white>L 87: team 63-121<highlight> | <red> PvP 70-109 <highlight> | <yellow>870,000<highlight> | <orange>  Missions 155, 130, 113, 104, 95, 87, 78, 73, 69, 65, 60 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[88]="<white>L 88: team 64-122<highlight> | <red> PvP 70-110 <highlight> | <yellow>912,600<highlight> | <orange>  Missions 157, 132, 114, 105, 96, 88, 79, 74, 70, 66, 61 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[89]="<white>L 89: team 65-124<highlight> | <red> PvP 71-111 <highlight> | <yellow>956,800<highlight> | <orange>  Missions 159, 133, 115, 106, 97, 89, 80, 75, 71, 66, 62 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[90]="<white>L 90: team 65-125<highlight> | <red> PvP 72-113 <highlight> | <yellow>1,003,000<highlight> | <orange>  Missions 161, 135, 117, 108, 99, 90, 81, 76, 72, 67, 63 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[91]="<white>L 91: team 66-126<highlight> | <red> PvP 73-114 <highlight> | <yellow>1,051,300<highlight> | <orange>  Missions 163, 136, 118, 109, 100, 91, 81, 77, 72, 68, 63 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[92]="<white>L 92: team 67-128<highlight> | <red> PvP 74-115 <highlight> | <yellow>1,101,500<highlight> | <orange>  Missions 164, 138, 119, 110, 101, 92, 82, 78, 73, 69, 64 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[93]="<white>L 93: team 67-129<highlight> | <red> PvP 74-116 <highlight> | <yellow>1,153,900<highlight> | <orange>  Missions 166, 139, 120, 111, 102, 93, 83, 79, 74, 69, 65 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[94]="<white>L 94: team 68-131<highlight> | <red> PvP 75-118 <highlight> | <yellow>1,208,800<highlight> | <orange>  Missions 168, 141, 122, 112, 103, 94, 84, 79, 75, 70, 65 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[95]="<white>L 95: team 69-132<highlight> | <red> PvP 76-119 <highlight> | <yellow>1,266,000<highlight> | <orange>  Missions 170, 142, 123, 114, 104, 95, 85, 80, 76, 71, 66 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[96]="<white>L 96: team 70-132<highlight> | <red> PvP 77-120 <highlight> | <yellow>1,325,500<highlight> | <orange>  Missions 171, 144, 124, 115, 105, 96, 86, 81, 76, 72, 67 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[97]="<white>L 97: team 70-135<highlight> | <red> PvP 78-121 <highlight> | <yellow>1,387,700<highlight> | <orange>  Missions 173, 145, 126, 116, 106, 97, 87, 82, 77, 72, 67 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[98]="<white>L 98: team 71-137<highlight> | <red> PvP 78-123 <highlight> | <yellow>1,452,300<highlight> | <orange>  Missions 175, 147, 127, 117, 107, 98, 88, 83, 78, 73, 68 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[99]="<white>L 99: team 72-138<highlight> | <red> PvP 79-124 <highlight> | <yellow>1,519,900<highlight> | <orange>  Missions 177, 148, 128, 118, 108, 99, 89, 84, 79, 74, 69 <highlight>|<blue> 4 token(s)<highlight>";
     	$level[100]="<white>L 100: team 72-139<highlight> | <red> PvP 80-125 <highlight> | <yellow>1,590,300<highlight> | <orange>  Missions 179, 150, 130, 120, 110, 100, 90, 85, 80, 75, 70 <highlight>|<blue> 4 token(s)<highlight>";
    	$level[101]="<white>L 101: team 73-140<highlight> | <red> PvP 81-126 <highlight> | <yellow>1,663,500<highlight> | <orange>  Missions 184, 151, 131, 121, 111, 101, 90, 85, 80, 75, 70 <highlight>|<blue> 5 token(s)<highlight>";
    	$level[102]="<white>L 102: team 74-142<highlight> | <red> PvP 82-128 <highlight> | <yellow>1,739,900<highlight> | <orange>  Missions 186, 153, 132, 122, 112, 102, 91, 86, 81, 76, 71 <highlight>|<blue> 5 token(s)<highlight>";
    	$level[103]="<white>L 103: team 75-143<highlight> | <red> PvP 82-129 <highlight> | <yellow>1,819,600<highlight> | <orange> Missions 185, 154, 133, 123, 113, 103, 92, 87, 82, 77, 72 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[104]="<white>L 104: team 75-145<highlight> | <red> PvP 83-130 <highlight> | <yellow>1,902,200 XP<highlight> |<orange> Missions 187, 156, 135, 124, 114, 104, 93, 88, 83, 78, 72 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[105]="<white>L 105: team 76-145<highlight> | <red> PvP 84-131 <highlight> | <yellow>1,988,900 XP<highlight> |<orange> Missions 188, 157, 136, 126, 115, 105, 94, 89, 84, 78, 73 <highlight>|<blue> 5 token(s)<highlight>";
    	$level[106]="<white>L 106: team 77-147<highlight> | <red> PvP 85-133 <highlight> | <yellow>2,078,600<highlight> | <orange> Missions 189, 159, 137, 127, 116, 106, 95, 90, 84, 79, 74 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[107]="<white>L 107: team 77-149<highlight> | <red> PvP 86-134 <highlight> | <yellow>2,172,100 XP<highlight> |<orange> Missions 191, 160, 139, 128, 117, 107, 96, 90, 85, 80, 74 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[108]="<white>L 108: team 78-149<highlight> | <red> PvP 86-135 <highlight> | <yellow>2,269,800 XP<highlight> |<orange> Missions 193, 162, 140, 129, 118, 108, 97, 91, 86, 81, 75 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[109]="<white>L 109: team 79-152<highlight> | <red> PvP 87-136 <highlight> | <yellow>2,371,100 XP<highlight> |<orange> Missions 195, 163, 141, 130, 119, 109, 98, 92, 87, 81, 76 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[110]="<white>L 110: team 80-153<highlight> | <red> PvP 88-138 <highlight> | <yellow>2,476,600 XP<highlight> |<orange> Missions 197, 165, 143, 132, 121, 110, 99, 93, 88, 82, 77 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[111]="<white>L 111: team 80-154<highlight> | <red> PvP 89-139 <highlight> | <yellow>2,586,600 XP<highlight> |<orange> Missions 198, 166, 144, 133, 122, 111, 99, 94, 88, 83, 77 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[112]="<white>L 112: team 81-155<highlight> | <red> PvP 90-140 <highlight> | <yellow>2,701,000 XP<highlight> |<orange> Missions 200, 168, 145, 134, 122, 112, 100, 95, 89, 84, 78 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[113]="<white>L 113: team 82-156<highlight> | <red> PvP 90-141 <highlight> | <yellow>2,819,800 XP<highlight> |<orange> Missions 202, 169, 146, 135, 124, 113, 101, 96, 90, 84, 79 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[114]="<white>L 114: team 82-159<highlight> | <red> PvP 91-143 <highlight> | <yellow>2,943,600 XP<highlight> |<orange> Missions 204, 171, 148, 136, 125, 114, 102, 96, 91, 85, 79 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[115]="<white>L 115: team 83-160<highlight> | <red> PvP 92-144 <highlight> | <yellow>3,072,400 XP<highlight> |<orange> Missions 206, 172, 149, 138, 126, 115, 103, 97, 92, 86, 80 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[116]="<white>L 116: team 84-161<highlight> | <red> PvP 93-145 <highlight> | <yellow>3,205,800 XP<highlight> |<orange> Missions 208, 174, 150, 139, 127, 116, 104, 98, 92, 87, 81 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[117]="<white>L 117: team 85-162<highlight> | <red> PvP 94-146 <highlight> | <yellow>3,345,200 XP<highlight> |<orange> Missions 210, 175, 152, 140, 128, 117, 105, 99, 93, 87, 81 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[118]="<white>L 118: team 85-163<highlight> | <red> PvP 94-148 <highlight> | <yellow>3,489,700 XP<highlight> |<orange> Missions 211, 177, 153, 141, 129, 118, 106, 100, 94, 88, 82 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[119]="<white>L 119: team 86-166<highlight> | <red> PvP 95-149 <highlight> | <yellow>3,640,200 XP<highlight> |<orange> Missions 214, 178, 154, 142, 130, 119, 107, 101, 95, 89, 83 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[120]="<white>L 120: team 87-167<highlight> | <red> PvP 96-150 <highlight> | <yellow>3,796,500 XP<highlight> |<orange> Missions 215, 180, 156, 144, 132, 120, 108, 102, 96, 90, 84 <highlight>|<blue> 5 token(s)<highlight>";
    	$level[121]="<white>L 121: team 87-167<highlight> | <red> PvP 97-151 <highlight> | <yellow>3,958,900<highlight> | <orange> Missions 217, 181, 157, 145, 133, 121, 108, 102, 96, 90, 84 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[122]="<white>L 122: team 88-168<highlight> | <red> PvP 98-153 <highlight> | <yellow>4,128,000 XP<highlight> |<orange> Missions 218, 183, 158, 146, 134, 122, 109, 103, 97, 91, 85 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[123]="<white>L 123: team 89-171<highlight> | <red> PvP 98-155 <highlight> | <yellow>4,303,400 XP<highlight> |<orange> Missions 220, 184, 159, 147, 135, 123, 110, 104, 98, 92, 86 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[124]="<white>L 124: team 90-173<highlight> | <red> PvP 99-155 <highlight> | <yellow>4,485,700 XP<highlight> |<orange> Missions 222, 186, 161, 148, 136, 124, 111, 105, 99, 93, 86 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[125]="<white>L 125: team 90-174<highlight> | <red> PvP 100-156 <highlight> | <yellow>4,674,800 XP<highlight> |<orange> Missions 224, 187, 162, 150, 137, 125, 112, 106, 100, 93, 87 <highlight>|<blue> 5 token(s)<highlight>";
	    $level[126]="<white>L 126: team 91-175<highlight> | <red> PvP 101-158 <highlight> | <yellow>4,871,700 XP<highlight> |<orange> Missions 225, 189, 163, 151, 138, 126, 113, 107, 100, 94, 88 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[127]="<white>L 127: team 92-176<highlight> | <red> PvP 102-159 <highlight> | <yellow>5,075,700 XP<highlight> |<orange> Missions 225, 190, 165, 152, 139, 127, 114, 107, 101, 95, 88 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[128]="<white>L 128: team 92-177<highlight> | <red> PvP 102-160 <highlight> | <yellow>5,288,100 XP<highlight> |<orange> Missions 226, 192, 166, 153, 140, 128, 115, 108, 102, 96, 89 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[129]="<white>L 129: team 93-180<highlight> | <red> PvP 103-161 <highlight> | <yellow>5,508,200 XP<highlight> |<orange> Missions 227, 193, 167, 154, 141, 129, 116, 109, 103, 96, 90 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[130]="<white>L 130: team 94-181<highlight> | <red> PvP 104-163 <highlight> | <yellow>5,736,800 XP<highlight> |<orange> Missions 232, 195, 169, 156, 143, 130, 117, 110, 104, 97, 91 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[131]="<white>L 131: team 95-182<highlight> | <red> PvP 105-164 <highlight> | <yellow>5,974,600 XP<highlight> |<orange> Missions 235, 196, 170, 157, 144, 131, 117, 111, 104, 98, 91 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[132]="<white>L 132: team 95-183<highlight> | <red> PvP 106-165 <highlight> | <yellow>6,220,700 XP<highlight> |<orange> Missions 236, 198, 171, 158, 145, 132, 118, 112, 105, 99, 92 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[133]="<white>L 133: team 97-185<highlight> | <red> PvP 106-166 <highlight> | <yellow>6,474,500 XP<highlight> |<orange> Missions 237, 199, 172, 159, 146, 133, 119, 113, 106, 99, 93 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[134]="<white>L 134: team 97-187<highlight> | <red> PvP 107-168 <highlight> | <yellow>6,742,200 XP<highlight> |<orange> Missions 238, 201, 174, 160, 147, 134, 120, 113, 107, 100, 93 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[135]="<white>L 135: team 97-188<highlight> | <red> PvP 108-169 <highlight> | <yellow>7,017,500 XP<highlight> |<orange> Missions 239, 202, 175, 162, 148, 135, 121, 114, 108, 101, 94 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[136]="<white>L 136: team 98-189<highlight> | <red> PvP 109-170 <highlight> | <yellow>7,303,700 XP<highlight> |<orange> Missions 243, 204, 176, 163, 149, 136, 122, 115, 108, 102, 95 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[137]="<white>L 137: team 98-189<highlight> | <red> PvP 110-171 <highlight> | <yellow>7,600,100 XP<highlight> |<orange> Missions 245, 205, 178, 164, 150, 137, 123, 116, 109, 102, 95 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[138]="<white>L 138: team 100-191<highlight> | <red> PvP 110-173 <highlight> | <yellow>7,907,600 XP<highlight> |<orange> Missions 246, 207, 179, 165, 151, 138, 124, 117, 110, 103, 96 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[139]="<white>L 139: team 100-192<highlight> | <red> PvP 111-174 <highlight> | <yellow>8,227,000 XP<highlight> |<orange> Missions 248, 208, 180, 166, 152, 139, 125, 118, 111, 104, 97 <highlight>|<blue> 6 token(s)<highlight>";
		$level[140]="<white>L 140: team 101-194<highlight> | <red> PvP 112-175 <highlight> | <yellow>8,557,700 XP<highlight> |<orange> Missions 250, 210, 182, 168, 154, 140, 126, 119, 112, 105, 98 <highlight>|<blue> 6 token(s)<highlight>";
		$level[141]="<white>L 141: team 102-195<highlight> | <red> PvP 113-176 <highlight> | <yellow>8,901,000 XP<highlight> |<orange> Missions 250, 211, 183, 169, 155, 141, 126, 119, 112, 105, 98 <highlight>|<blue> 6 token(s)<highlight>";
	    $level[142]="<white>L 142: team 102-197<highlight> | <red> PvP 114-178 <highlight> | <yellow>9,256,800 XP<highlight> |<orange> Missions 250, 212, 184, 170, 156, 142, 127, 120, 113, 106, 99 <highlight>|<blue> 6 token(s)<highlight>";
		$level[143]="<white>L 143: team 103-198<highlight> | <red> PvP 114-179 <highlight> | <yellow>9,625,800 XP<highlight> |<orange> Missions 250, 214, 185, 171, 157, 143, 128, 121, 114, 107, 100 <highlight>|<blue> 6 token(s)<highlight>";
		$level[144]="<white>L 144: team 104-200<highlight> | <red> PvP 115-180 <highlight> | <yellow>10,008,600 XP<highlight> |<orange> Missions 250, 216, 187, 172, 158, 144, 129, 122, 115, 108, 100 <highlight>|<blue> 6 token(s)<highlight>";
		$level[145]="<white>L 145: team 105-201<highlight> | <red> PvP 116-181 <highlight> | <yellow>10,405,300 XP<highlight> |<orange> Missions 250, 217, 188, 174, 159, 145, 130, 123, 116, 108, 101 <highlight>|<blue> 6 token(s)<highlight>";
		$level[146]="<white>L 146: team 106-202<highlight> | <red> PvP 117-183 <highlight> | <yellow>10,816,600 XP<highlight> |<orange> Missions 250, 219, 189, 175, 160, 146, 131, 124, 116, 109, 102 <highlight>|<blue> 6 token(s)<highlight>";
		$level[147]="<white>L 147: team 106-204<highlight> | <red> PvP 118-184 <highlight> | <yellow>11,242,500 XP<highlight> |<orange> Missions 250, 220, 191, 176, 161, 147, 132, 124, 117, 110, 102 <highlight>|<blue> 6 token(s)<highlight>";
		$level[148]="<white>L 148: team 107-205<highlight> | <red> PvP 118-185 <highlight> | <yellow>11,684,300 XP<highlight> |<orange> Missions 250, 222, 192, 177, 162, 148, 133, 125, 118, 111, 103 <highlight>|<blue> 6 token(s)<highlight>";
		$level[149]="<white>L 149: team 107-206<highlight> | <red> PvP 119-186 <highlight> | <yellow>12,141,900 XP<highlight> |<orange> Missions 250, 223, 193, 178, 163, 149, 134, 126, 119, 111, 104 <highlight>|<blue> 6 token(s)<highlight>";
		$level[150]="<white>L 150: team 108-208<highlight> | <red> PvP 120-188 <highlight> | <yellow>12,616,200 XP<highlight> |<orange> Missions 250, 225, 195, 180, 165, 150, 135, 127, 120, 112, 105 <highlight>|<blue> 6 token(s)<highlight>";
		$level[151]="<white>L 151: team 109-209<highlight> | <red> PvP 121-189 <highlight> | <yellow>13,107,200 XP<highlight> |<orange> Missions 250, 226, 196, 181, 166, 151, 135, 128, 120, 113, 105 <highlight>|<blue> 7 token(s)<highlight>";
		$level[152]="<white>L 152: team 110-211<highlight> | <red> PvP 122-190 <highlight> | <yellow>13,616,100 XP<highlight> |<orange> Missions 250, 228, 197, 182, 167, 152, 136, 129, 121, 114, 106 <highlight>|<blue> 7 token(s)<highlight>";
		$level[153]="<white>L 153: team 110-212<highlight> | <red> PvP 122-191 <highlight> | <yellow>14,143,600 XP<highlight> |<orange> Missions 250, 229, 198, 183, 168, 153, 137, 130, 122, 114, 107 <highlight>|<blue> 7 token(s)<highlight>";
		$level[154]="<white>L 154: team 111-213<highlight> | <red> PvP 123-192 <highlight> | <yellow>14,689,700 XP<highlight> |<orange> Missions 250, 231, 200, 184, 169, 154, 138, 130, 123, 115, 107 <highlight>|<blue> 7 token(s)<highlight>";
		$level[155]="<white>L 155: team 112-215<highlight> | <red> PvP 123-194 <highlight> | <yellow>15,255,300 XP<highlight> |<orange> Missions 250, 232, 201, 186, 170, 155, 139, 131, 124, 116, 108 <highlight>|<blue> 7 token(s)<highlight>";
		$level[156]="<white>L 156: team 112-216<highlight> | <red> PvP 125-195 <highlight> | <yellow>15,841,000 XP<highlight> |<orange> Missions 250, 234, 202, 187, 171, 156, 140, 132, 124, 117, 109 <highlight>|<blue> 7 token(s)<highlight>";
		$level[157]="<white>L 157: team 114-218<highlight> | <red> PvP 126-196 <highlight> | <yellow>16,447,900 XP<highlight> |<orange> Missions 250, 235, 204, 188, 172, 157, 141, 133, 125, 117, 109 <highlight>|<blue> 7 token(s)<highlight>";
		$level[158]="<white>L 158: team 114-219<highlight> | <red> PvP 126-198 <highlight> | <yellow>17,075,800 XP<highlight> |<orange> Missions 250, 237, 205, 189, 173, 158, 142, 134, 126, 119, 110 <highlight>|<blue> 7 token(s)<highlight>";
		$level[159]="<white>L 159: team 115-220<highlight> | <red> PvP 127-199 <highlight> | <yellow>17,725,900 XP<highlight> |<orange> Missions 250, 238, 206, 190, 174, 159, 143, 135, 127, 119, 111 <highlight>|<blue> 7 token(s)<highlight>";
		$level[160]="<white>L 160: team 115-220<highlight> | <red> PvP 128-200 <highlight> | <yellow>18,399,400 XP<highlight> |<orange> Missions 250, 240, 208, 192, 176, 160, 144, 136, 128, 120, 112 <highlight>|<blue> 7 token(s)<highlight>";
		$level[161]="<white>L 161: team 116-220<highlight> | <red> PvP 129-201 <highlight> | <yellow>19,096,100 XP<highlight> |<orange> Missions 250, 241, 209, 193, 177, 161, 144, 136, 128, 120, 112 <highlight>|<blue> 7 token(s)<highlight>";
		$level[162]="<white>L 162: team 117-220<highlight> | <red> PvP 130-203 <highlight> | <yellow>19,817,500 XP<highlight> |<orange> Missions 250, 243, 210, 194, 178, 162, 145, 137, 129, 121, 113 <highlight>|<blue> 7 token(s)<highlight>";
		$level[163]="<white>L 163: team 118-220<highlight> | <red> PvP 130-204 <highlight> | <yellow>20,564,100 XP<highlight> |<orange> Missions 250, 244, 211, 195, 179, 163, 146, 138, 130, 122, 113 <highlight>|<blue> 7 token(s)<highlight>";
		$level[164]="<white>L 164: team 118-220<highlight> | <red> PvP 131-205 <highlight> | <yellow>21,336,600 XP<highlight> |<orange> Missions 250, 246, 213, 196, 180, 164, 147, 139, 131, 123, 114 <highlight>|<blue> 7 token(s)<highlight>";
		$level[165]="<white>L 165: team 119-220<highlight> | <red> PvP 132-206 <highlight> | <yellow>22,136,100 XP<highlight> |<orange> Missions 250, 247, 214, 198, 181, 165, 148, 140, 132, 123, 115 <highlight>|<blue> 7 token(s)<highlight>";
		$level[166]="<white>L 166: team 120-220<highlight> | <red> PvP 133-208 <highlight> | <yellow>22,963,600 XP<highlight> |<orange> Missions 250, 249, 215, 199, 182, 166, 149, 141, 132, 124, 116 <highlight>|<blue> 7 token(s)<highlight>";
		$level[167]="<white>L 167: team 120-220<highlight> | <red> PvP 134-209 <highlight> | <yellow>23,819,700 XP<highlight> |<orange> Missions 250, 250, 217, 200, 183, 167, 150, 141, 133, 125, 116 <highlight>|<blue> 7 token(s)<highlight>";
		$level[168]="<white>L 168: team 121-220<highlight> | <red> PvP 134-210 <highlight> | <yellow>24,705,200 XP<highlight> |<orange> Missions 250, 250, 218, 201, 184, 168, 151, 142, 134, 124, 117 <highlight>|<blue> 7 token(s)<highlight>";
		$level[169]="<white>L 169: team 122-220<highlight> | <red> PvP 135-211 <highlight> | <yellow>25,621,100 XP<highlight> |<orange> Missions 250, 250, 219, 202, 185, 169, 152, 143, 135, 126, 118 <highlight>|<blue> 7 token(s)<highlight>";
		$level[170]="<white>L 170: team 122-220<highlight> | <red> PvP 136-213 <highlight> | <yellow>26,569,000 XP<highlight> |<orange> Missions 250, 250, 220, 204, 187, 170, 153, 144, 136, 127, 119 <highlight>|<blue> 7 token(s)<highlight>";
		$level[171]="<white>L 171: team 123-220<highlight> | <red> PvP 137-214 <highlight> | <yellow>27,548,800 XP<highlight> |<orange> Missions 250, 250, 222, 205, 188, 171, 153, 145, 136, 128, 119 <highlight>|<blue> 7 token(s)<highlight>";
		$level[172]="<white>L 172: team 124-220<highlight> | <red> PvP 138-215 <highlight> | <yellow>28,562,900 XP<highlight> |<orange> Missions 250, 250, 223, 205, 189, 172, 154, 146, 137, 129, 120 <highlight>|<blue> 7 token(s)<highlight>";
		$level[173]="<white>L 173: team 125-220<highlight> | <red> PvP 138-218 <highlight> | <yellow>29,611,100 XP<highlight> |<orange> Missions 250, 250, 224, 207, 190, 173, 155, 147, 138, 129, 121 <highlight>|<blue> 7 token(s)<highlight>";
		$level[174]="<white>L 174: team 125-220<highlight> | <red> PvP 139-219 <highlight> | <yellow>30,695,300 XP<highlight> |<orange> Missions 250, 250, 226, 208, 191, 174, 156, 147, 139, 130, 121 <highlight>|<blue> 7 token(s)<highlight>";
		$level[175]="<white>L 175: team 126-220<highlight> | <red> PvP 140-220 <highlight> | <yellow>31,816,300 XP<highlight> |<orange> Missions 250, 250, 227, 210, 192, 175, 157, 148, 140, 131, 122 <highlight>|<blue> 7 token(s)<highlight>";
		$level[176]="<white>L 176: team 127-220<highlight> | <red> PvP 141-220 <highlight> | <yellow>32,975,100 XP<highlight> |<orange> Missions 250, 250, 228, 211, 193, 176, 158, 149, 140, 132, 123 <highlight>|<blue> 8 token(s)<highlight>";
		$level[177]="<white>L 177: team 128-220<highlight> | <red> PvP 142-220 <highlight> | <yellow>34,173,500 XP<highlight> |<orange> Missions 250, 250, 230, 212, 194, 177, 159, 150, 141, 132, 123 <highlight>|<blue> 8 token(s)<highlight>";
		$level[178]="<white>L 178: team 129-220<highlight> | <red> PvP 142-220 <highlight> | <yellow>35,412,500 XP<highlight> |<orange> Missions 250, 250, 231, 213, 195, 178, 160, 151, 142, 133, 124 <highlight>|<blue> 8 token(s)<highlight>";
		$level[179]="<white>L 179: team 129-220<highlight> | <red> PvP 143-220 <highlight> | <yellow>36,692,500 XP<highlight> |<orange> Missions 250, 250, 232, 214, 196, 179, 161, 152, 143, 134, 125 <highlight>|<blue> 8 token(s)<highlight>";
		$level[180]="<white>L 180: team 130-220<highlight> | <red> PvP 144-220 <highlight> | <yellow> 38,016,500 XP<highlight> |<orange> Missions 250, 250, 234, 216, 198, 180, 162, 153, 144, 135, 126 <highlight>|<blue> 8 token(s)<highlight>";
		$level[181]="<white>L 181: team 130-220<highlight> | <red> PvP 145-220 <highlight> | <yellow> 39,384,400 XP<highlight> |<orange> Missions 250, 250, 235, 217, 199, 181, 162, 153, 144, 135, 126 <highlight>|<blue> 8 token(s)<highlight>";
		$level[182]="<white>L 182: team 131-220<highlight> | <red> PvP 146-220 <highlight> | <yellow> 40,797,700 XP<highlight> |<orange> Missions 250, 250, 236, 218, 200, 182, 163, 154, 145, 136, 127 <highlight>|<blue> 8 token(s)<highlight>";
		$level[183]="<white>L 183: team 132-220<highlight> | <red> PvP 146-220 <highlight> | <yellow> 42,258,500 XP<highlight> |<orange> Missions 250, 250, 237, 219, 201, 183, 164, 155, 146, 137, 128 <highlight>|<blue> 8 token(s)<highlight>";
		$level[184]="<white>L 184: team 133-220<highlight> | <red> PvP 147-220 <highlight> | <yellow> 43,768,300 XP<highlight> |<orange> Missions 250, 250, 239, 220, 202, 184, 165, 156, 147, 138, 128 <highlight>|<blue> 8 token(s)<highlight>";
		$level[185]="<white>L 185: team 133-220<highlight> | <red> PvP 148-220 <highlight> | <yellow> 45,328,100 XP<highlight> |<orange> Missions 250, 250, 240, 222, 203, 185, 166, 157, 148, 138, 129 <highlight>|<blue> 8 token(s)<highlight>";
		$level[186]="<white>L 186: team 134-220<highlight> | <red> PvP 149-220 <highlight> | <yellow> 46,939,900 XP<highlight> |<orange> Missions 250, 250, 241, 223, 204, 186, 167, 158, 148, 139, 130 <highlight>|<blue> 8 token(s)<highlight>";
		$level[187]="<white>L 187: team 135-220<highlight> | <red> PvP 150-220 <highlight> | <yellow> 48,604,900 XP<highlight> |<orange> Missions 250, 250, 243, 224, 205, 187, 168, 158, 149, 140, 130 <highlight>|<blue> 8 token(s)<highlight>";
		$level[188]="<white>L 188: team 135-220<highlight> | <red> PvP 150-220 <highlight> | <yellow> 50,324,600 XP<highlight> |<orange> Missions 250, 250, 244, 225, 206, 188, 169, 159, 150, 141, 131 <highlight>|<blue> 8 token(s)<highlight>";
		$level[189]="<white>L 189: team 137-220<highlight> | <red> PvP 151-220 <highlight> | <yellow> 52,101,200 XP<highlight> |<orange> Missions 250, 250, 245, 226, 207, 189, 170, 160, 151, 141, 132 <highlight>|<blue> 8 token(s)<highlight>";
		$level[190]="<white>L 190: team 138-220<highlight> | <red> PvP 152-220 <highlight> | <yellow> 53,936,300 XP<highlight> |<orange> Missions 250, 250, 247, 228, 209, 190, 171, 161, 152, 142, 133 <highlight>|<blue> 9 token(s)<highlight>";
		$level[191]="<white>L 191: team 138-220<highlight> | <red> PvP 153-220 <highlight> | <yellow> 55,831,600 XP<highlight> |<orange> Missions 250, 250, 248, 229, 210, 191, 171, 162, 152, 143, 133 <highlight>|<blue> 9 token(s)<highlight>";
		$level[192]="<white>L 192: team 139-220<highlight> | <red> PvP 154-220 <highlight> | <yellow>57,788,700 XP<highlight> |<orange> Missions 250, 250, 249, 230, 211, 192, 172, 163, 153, 144, 134 <highlight>|<blue> 9 token(s)<highlight>";
		$level[193]="<white>L 193: team 140-220<highlight> | <red> PvP 154-220 <highlight> | <yellow>59,810,000 XP<highlight> |<orange> Missions 250, 250, 250, 231, 212, 193, 173, 164, 154, 144, 135 <highlight>|<blue> 9 token(s)<highlight>";
		$level[194]="<white>L 194: team 140-220<highlight> | <red> PvP 155-220 <highlight> | <yellow>61,897,000 XP<highlight> |<orange> Missions 250, 250, 250, 232, 213, 194, 174, 164, 155, 145, 135 <highlight>|<blue> 9 token(s)<highlight>";
		$level[195]="<white>L 195: team 140-220<highlight> | <red> PvP 156-220 <highlight> | <yellow>64,052,200 XP<highlight> |<orange> Missions 250, 250, 250, 234, 214, 195, 175, 165, 156, 146, 136 <highlight>|<blue> 9 token(s)<highlight>";
		$level[196]="<white>L 196: team 142-220<highlight> | <red> PvP 157-220 <highlight> | <yellow>66,277,200 XP<highlight> |<orange> Missions 250, 250, 250, 235, 215, 196, 176, 166, 156, 147, 137 <highlight>|<blue> 9 token(s)<highlight>";
		$level[197]="<white>L 197: team 142-220<highlight> | <red> PvP 157-220 <highlight> | <yellow>68,574,400 XP<highlight> |<orange> Missions 250, 250, 250, 236, 216, 197, 177, 167, 157, 147, 137 <highlight>|<blue> 9 token(s)<highlight>";
		$level[198]="<white>L 198: team 143-220<highlight> | <red> PvP 158-220 <highlight> | <yellow>70,945,700 XP<highlight> |<orange> Missions 250, 250, 250, 237, 217, 198, 178, 168, 158, 148, 138 <highlight>|<blue> 9 token(s)<highlight>";
		$level[199]="<white>L 199: team 144-220<highlight> | <red> PvP 159-220 <highlight> | <yellow>73,393,900 XP<highlight> |<orange> Missions 250, 250, 250, 238, 218, 199, 179, 169, 159, 149, 139 <highlight>|<blue> 9 token(s)<highlight>";
		$level[200]="<white>L 200: team 144-220<highlight> | <red> PvP 160-220 <highlight> | <yellow>80,000 SK<highlight> | <orange>Missions 250, 250, 250, 240, 220, 200, 180, 170, 160, 150, 140 <highlight>|<blue> 9 token(s)<highlight>";
		$level[201]="<white>L 201: team 145-220<highlight> | <red> PvP 161-220 <highlight> | <yellow>96,000 SK<highlight> | <orange>Missions 250, 250, 250, 241, 221, 201, 180, 170, 160, 150, 140 <highlight>|<blue> 9 token(s)<highlight>";
		$level[202]="<white>L 202: team 145-220<highlight> | <red> PvP 161-220 <highlight> | <yellow>115,200 SK<highlight> | <orange>Missions 250, 250, 250, 242, 222, 202, 181, 171, 161, 151, 141 <highlight>|<blue> 9 token(s)<highlight>";
		$level[203]="<white>L 203: team 146-220<highlight> | <red> PvP 162-220 <highlight> | <yellow>138,240 SK<highlight> | <orange>Missions 250, 250, 250, 243, 223, 203, 182, 172, 162, 152, 142 <highlight>|<blue> 9 token(s)<highlight>";
		$level[204]="<white>L 204: team 147-220<highlight> | <red> PvP 163-220 <highlight> | <yellow>165,888 SK<highlight> | <orange>Missions 250, 250, 250, 244, 224, 204, 183, 173, 163, 153, 142 <highlight>|<blue> 9 token(s)<highlight>";
		$level[205]="<white>L 205: team 148-220<highlight> | <red> PvP 164-220 <highlight> | <yellow>199,066 SK<highlight> | <orange>Missions 250, 250, 250, 246, 225, 205, 184, 174, 164, 153, 143 <highlight>|<blue> 9 token(s)<highlight>";
		$level[206]="<white>L 206: team 148-220<highlight> | <red> PvP 165-220 <highlight> | <yellow>238,879 SK<highlight> | <orange>Missions 250, 250, 250, 247, 226, 206, 185, 175, 164, 154, 144 <highlight>|<blue> 9 token(s)<highlight>";
		$level[207]="<white>L 207: team 149-220<highlight> | <red> PvP 165-220 <highlight> | <yellow>286,654 SK<highlight> | <orange>Missions 250, 250, 250, 248, 227, 207, 186, 175, 165, 155, 144 <highlight>|<blue> 9 token(s)<highlight>";
		$level[208]="<white>L 208: team 150-220<highlight> | <red> PvP 166-220 <highlight> | <yellow>343,985 SK<highlight> | <orange>Missions 250, 250, 250, 249, 228, 208, 187, 176, 166, 156, 145 <highlight>|<blue> 9 token(s)<highlight>";
		$level[209]="<white>L 209: team 150-220<highlight> | <red> PvP 167-220 <highlight> | <yellow>412,782 SK<highlight> | <orange>Missions 250, 250, 250, 250, 228, 209, 188, 177, 167, 156, 146 <highlight>|<blue> 9 token(s)<highlight>";
		$level[210]="<white>L 210: team 151-220<highlight> | <red> PvP 168-220 <highlight> | <yellow>495,339 SK<highlight> | <orange>Missions 250, 250, 250, 250, 231, 210, 189, 178, 168, 157, 147 <highlight>|<blue> 9 token(s)<highlight>";
		$level[211]="<white>L 211: team 152-220<highlight> | <red> PvP 169-220 <highlight> | <yellow>594,407 SK<highlight> | <orange>Missions 250, 250, 250, 250, 232, 211, 189, 179, 168, 158, 147 <highlight>|<blue> 9 token(s)<highlight>";
		$level[212]="<white>L 212: team 153-220<highlight> | <red> PvP 169-220 <highlight> | <yellow>713,288 SK<highlight> | <orange>Missions 250, 250, 250, 250, 233, 212, 190, 180, 169, 159, 148 <highlight>|<blue> 9 token(s)<highlight>";
		$level[213]="<white>L 213: team 153-220<highlight> | <red> PvP 170-220 <highlight> | <yellow>855,946 SK<highlight> | <orange>Missions 250, 250, 250, 250, 234, 213, 191, 181, 170, 159, 149 <highlight>|<blue> 9 token(s)<highlight>";
		$level[214]="<white>L 214: team 154-220<highlight> | <red> PvP 171-220 <highlight> | <yellow>1,027,135 SK<highlight> | <orange>Missions 250, 250, 250, 250, 235, 214, 192, 181, 171, 160, 149 <highlight>|<blue> 9 token(s)<highlight>";
		$level[215]="<white>L 215: team 155-220<highlight> | <red> PvP 172-220 <highlight> | <yellow>1,232,562 SK<highlight> | <orange>Missions 250, 250, 250, 250, 236, 215, 193, 182, 172, 161, 150 <highlight>|<blue> 9 token(s)<highlight>";
		$level[216]="<white>L 216: team 156-220<highlight> | <red> PvP 172-220 <highlight> | <yellow>1,479,074 SK<highlight> | <orange>Missions 250, 250, 250, 250, 237, 216, 194, 183, 172, 162, 151 <highlight>|<blue> 9 token(s)<highlight>";
		$level[217]="<white>L 217: team 156-220<highlight> | <red> PvP 172-220 <highlight> | <yellow>1,774,889 SK<highlight> | <orange>Missions 250, 250, 250, 250, 238, 217, 195, 184, 173, 162, 151 <highlight>|<blue> 9 token(s)<highlight>";
		$level[218]="<white>L 218: team 157-220<highlight> | <red> PvP 173-220 <highlight> | <yellow>2,129,867 SK<highlight> | <orange>Missions 250, 250, 250, 250, 239, 218, 196, 185, 174, 163, 152 <highlight>|<blue> 9 token(s)<highlight>";
		$level[219]="<white>L 219: team 158-220<highlight> | <red> PvP 174-220 <highlight> | <yellow>2,555,840 SK<highlight> | <orange>Missions 250, 250, 250, 250, 240, 219, 197, 186, 175, 164, 153 <highlight>|<blue> 9 token(s)<highlight>";
		$level[220]="<white>L 220: team 159-220<highlight> | <red> PvP 175-220 <highlight> | <yellow>0 SK<highlight> | <orange>Missions 250, 250, 250, 250, 242, 220, 198, 187, 176, 165, 154 <highlight>|<blue> 9 token(s)<highlight>";
        $msg = $level[$arr[2]];
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