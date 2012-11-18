<?php

/**
 * This interface descripes methods that instance 'alts' provides.
 */
interface AltsInterface {
	/**
	 * Returns AltInfo object of characters which are alternative characters
	 * of given @a $player main character.
	 */
	public function get_alt_info($player);
}
