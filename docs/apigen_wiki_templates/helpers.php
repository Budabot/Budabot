<?php

/**
 * Custom helpers for Google wiki generation.
 */
class Helpers implements ApiGen\IHelperSet {
	/**
	 * Tries to load the requested helper.
	 * Implemented from IHelperSet.
	 *
	 * @param string $helperName Helper name
	 * @return \Nette\Callback
	 */
	public function loader($helperName) {
		if (method_exists(__CLASS__, $helperName)) {
			return new Nette\Callback(__CLASS__, $helperName);
		}
		return null;
	}
	
	/**
	 * Searches @Instance-annotation from given class and returns instance
	 * name if present.
	 *
	 * @param object $class class object
	 * @return string 
	 */
	public static function getInstanceName($class) {
		foreach ($class->annotations as $annotation => $values) {
			if (preg_match("/^Instance\\(['\"](.+)['\"]\\)$/i", $annotation, $matches)) {
				return $matches[1];
			}
		}
		return "";
	}
}

function filterOutInjects($properties) {
	$newProperties = array();
	foreach ($properties as $property) {
		foreach ($property->annotations as $annotation => $values) {
			if (preg_match("/^Inject/i", $annotation)) {
				continue 2;
			}
		}
		$newProperties []= $property;
	}
	return $newProperties;
}

function filterOutLogger($properties) {
	$newProperties = array();
	foreach ($properties as $property) {
		foreach ($property->annotations as $annotation => $values) {
			if (preg_match("/^Logger/i", $annotation)) {
				continue 2;
			}
		}
		$newProperties []= $property;
	}
	return $newProperties;
}
