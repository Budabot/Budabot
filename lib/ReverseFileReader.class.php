<?php
/************************************************** ***
** Title.........: ReverseFileReader Class
** Version.......: 1.00
** Author........: Steve Weet <sweet@weet.demon.co.uk>
** Filename......: ReverseFileReader.class.php
** Last changed..: 30th Jan 2004
** Purpose.......: Allows the display of a file in
** ..............: In reverse order
************************************************** ****/

// taken from: http://bytes.com/topic/php/answers/4033-reading-file-reverse-order

/*
Example Usage
$file = new ReverseFileReader("/etc/passwd");

while (!$file->sof()) {
	echo $file->getLine();
}
*/

class ReverseFileReader {

	var $fileHandle;
	var $filePos;

	function __construct($filename) {
		$this->fileHandle = @fopen($filename, "r");

		if ($this->fileHandle === false) {
			throw new Exception("Could not open file $filename");
		}

		// Find EOF
		if (fseek($this->fileHandle, 0, SEEK_END) != 0) {
			throw new Exception("Could not find end of file in $filename");
		}

		// Store file position
		$this->filePos = ftell($this->fileHandle);

		// Check that file is not empty or doesn't contain a single newline
		if ($this->filePos < 2 ) {
			throw new Exception("$filename is empty");
		}

		// Position file pointer just before final newline
		// i.e. Skip EOF
		$this->filePos -= 1;
	}

	function getLine() {
		$pos = $this->filePos -1;
		$ch = " ";
		$line = "";
		while ($ch != "\n" && $pos >= 0) {
			fseek($this->fileHandle, $pos);
			$ch = fgetc($this->fileHandle);

			// Decrement out pointer and prepend to the line
			// if we have not hit the new line
			if ($ch != "\n") {
				$pos = $pos -1;
				$line = $ch . $line;
			}
		}
		$this->filePos = $pos;
		return $line . "\n";
	}

	function sof() {
		return ($this->filePos <= 0);
	}

	function close() {
		fclose($this->fileHandle);
	}
}
?>
