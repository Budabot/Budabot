<?php
/*
   ** Author: Blackruby (RK2)
   ** Description: Shows data on Alien Generals and their Loottable.
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

if (preg_match("/^aigen (ankari|ilari|rimah|jaax|xoch|cha)$/i", $message, $arr)) {
	$gen = ucfirst(strtolower($arr[1]));

	$blob = '';
	switch ($gen) {
		case "Ankari":
			$blob .= "<red>Low Evade/Dodge,low AR, casting Viral/Virral nukes.<blue> \nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247145/247145/300'>Arithmetic Lead Viralbots</a>.\n<orange>(Nanoskill / Tradeskill)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247684/247684/300'>Kyr'Ozch Bio-Material - Type 1</a>\n<img src=rdb://255705></img>\n<a href='itemref://247685/247685/300'>Kyr'Ozch Bio-Material - Type 2</a>\n<img src=rdb://255705></img>\n<a href='itemref://288672/288673/300'>Kyr'Ozch Bio-Material - Type 48</a>";
			break;
		case "Ilari":
			$blob .= "<red>Low Evade/Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247146/247146/300'>Spiritual Lead Viralbots</a>.<orange>\n(Nanocost / Nanopool / Max Nano)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247681/247681/300'>Kyr'Ozch Bio-Material - Type 992</a>\n<img src=rdb://255705></img>\n<a href='itemref://247679/247679/300'>Kyr'Ozch Bio-Material - Type 880</a>";
			break;
		case "Rimah":
			$blob .= "<red>Low Evade/Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247143/247143/300'>Observant Lead Viralbots</a>.<orange>\n(Init / Evades)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247675/247675/300'>Kyr'Ozch Bio-Material - Type 112</a>\n<img src=rdb://255705></img>\n<a href='itemref://247678/247678/300'>Kyr'Ozch Bio-Material - Type 240</a>";
			break;
		case "Jaax":
			$blob .= "<red>High Evade, Low Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247139/247139/300'>Strong Lead Viralbots</a>.<orange>\n(Melee / Spec Melee / Add All Def / Add Damage)\n<blue><img src=rdb://255705></img>\n<a href='itemref://247694/247694/300'>Kyr'Ozch Bio-Material - Type 3</a>\n<img src=rdb://255705></img>\n<a href='itemref://247688/247688/300'>Kyr'Ozch Bio-Material - Type 4</a>";
			break;
		case "Xoch":
			$blob .= "<red>High Evade/Dodge, casting Ilari Biorejuvenation heals.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247137/247137/300'>Enduring Lead Viralbots</a>.<orange>\n(Max Health / Body Dev)<blue>\n<img src=rdb://255705></img>\n<a href='itemref://247690/247690/300'>Kyr'Ozch Bio-Material - Type 5</a>\n<img src=rdb://255705></img>\n<a href='itemref://247692/247692/300'>Kyr'Ozch Bio-Material - Type 12</a>";
			break;
		case "Cha":
			$blob .= "<red>High Evade/NR, Low Dodge.<blue>\nBoss of this type drops:\n\n<img src=rdb://100337></img>\n<a href='itemref://247141/247141/300'>Supple Lead Viralbots</a>.<orange>\n(Ranged / Spec Ranged / Add All Off)\n<img src=rdb://255705></img>\n<a href='itemref://247696/247696/300'>Kyr'Ozch Bio-Material - Type 13</a>\n<img src=rdb://255705></img>\n<a href='itemref://247674/247674/300'>Kyr'Ozch Bio-Material - Type 76</a>";
			break;
	}

	$msg = Text::make_blob("Info about General $gen", $blob);
	$sendto->reply($msg);
} else {
	$syntax_error = true;
}

?>
