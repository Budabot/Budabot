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
	
	/**
	 * Converts given $title text to anchor text.
	 *
	 * @param string $title title text
	 * @return string
	 */
	public static function titleToAnchor($title) {
		// algorithm found by trial and error (unit tests pls!)
		$title = preg_replace('/<[^>]+>/', ' ', $title);
		$title = str_replace('  ', ' ', $title);
		$title = substr($title, 0, 64);
		$title = trim($title);
		$title = str_replace(' ', '_', $title);
		return $title;
	}
	
	/**
	 * Extracts description from @param annotation.
	 *
	 * @param string $paramAnnotation annotation
	 * @return string
	 */
	public static function getParamDescription($paramAnnotation) {
		if (preg_match('/- (.*)$/', $paramAnnotation, $matches)) {
			return $matches[1];
		}
		return '';
	}

	/**
	 * Extracts description from @return annotation.
	 *
	 * @param string $returnAnnotation annotation
	 * @return string
	 */
	public static function getReturnDescription($returnAnnotation) {
		if (preg_match('/(\s+)(.*)$/', $returnAnnotation, $matches)) {
			return $matches[2];
		}
		return '';
	}
	
	/**
	 * Attempts to collpase multiple empty new lines to one new line.
	 *
	 * @param string $contents data to collapse
	 * @return string
	 */
	public static function collapseExtraNewLines($contents) {
		return preg_replace("/[\\n]{3,}/", "\n\n", $contents);
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
