<?php

/**
 * Stores the version string of Budabot in a property.
 *
 * @author Marebone
 */
class BudabotVersionTask extends Task {
	private $propertyName;
	private $sourceFile;

	/**
	 * Sets the name of the property to use.
	 */
	function setPropertyName($propertyName) {
		$this->propertyName = $propertyName;
	}
	
	/**
	 * Sets the source file where version information is read.
	 */
	public function setSourceFile($sourceFile) {
		$this->sourceFile = $sourceFile;
	}

	/**
	 * The main entry point.
	 *
	 * @throws BuildException
	 */
	function main() {
		if (!$this->sourceFile) {
			throw new BuildException("Source file has not been set");
		}
		if (!$this->propertyName) {
			throw new BuildException("Destination property name has not been set");
		}
		
		$contents = file_get_contents($this->sourceFile);
		if ($contents === false) {
			throw new BuildException("Failed to read the source file");
		}
		
		if (!preg_match('/\$version\s*=\s*([^;]+);/', $contents, $matches)) {
			throw new BuildException('Failed to find $version variable from source file');
		}
		$value = trim($matches[1], '\'"');

		$this->project->setProperty($this->propertyName, $value);
	}
}
