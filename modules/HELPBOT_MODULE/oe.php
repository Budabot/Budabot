<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: OE calculator
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 14.12.2005
   ** Date(last modified): 22.12.2005
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

if (preg_match("/^oe ([0-9]+)$/i", $message, $arr)) {
    $oe = $arr[1]; 
	$oevalue = (int)round($oe / 0.8); 
	$lowoevalue = (int)round($oe * 0.8); 
	$blob = "With a skill of <highlight>${oe}<end>, you will be OE above <highlight>${oevalue}<end> requirement. " . 
		"With a requirement of <highlight>${oe}<end> skill, you can have <highlight>${lowoevalue}<end> without being OE.";
	
	$msg = "<orange>{$lowoevalue}<end> - <yellow>{$oe}<end> - <orange>{$oevalue}<end> " . bot::makeLink('More info', $blob, 'blob');
    
    bot::send($msg, $sendto);
} else {
	$syntax_error = true;
}
?>
