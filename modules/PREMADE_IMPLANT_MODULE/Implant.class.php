<?php
   /*
   ** Module: PREMADE_IMPLANT
   ** Author: Tyrence/Whiz (RK2)
   ** Description: Allows you search for the implants in the premade implant booths.
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): Fall 2008
   ** Date(last modified): 9-Mar-2010
   ** 
   ** Copyright (C) 2008 Jason Wheeler (bigwheels16@hotmail.com)
   **
   ** This module and all it's files and contents are licensed
   ** under the GNU General Public License.  You may distribute
   ** and modify this module and it's contents freely.
   **
   ** This module may be obtained at: http://www.box.net/shared/bgl3cx1c3z
   **
   */
   
class Implant {
	
	public $slot;
	public $profession;
	public $ability;
	public $shiny;
	public $bright;
	public $faded;
	
	function __construct($slot, $profession, $ability, $shiny, $bright, $faded) {

		$this->slot = $slot;
		$this->profession = $profession;
		$this->ability = $ability;
		$this->shiny = $shiny;
		$this->bright = $bright;
		$this->faded = $faded;

	}

}