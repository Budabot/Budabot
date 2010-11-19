<?php

class MMDBParser {
	private static $mmdb = array();

	public static function get_message_string($categoryId, $instanceId, $filename = "data/text.mdb") {
		// check for entry in cache
		if (isset(MMDBParser::$mmdb[$categoryId][$instanceId])) {
			return MMDBParser::$mmdb[$categoryId][$instanceId];
		}

		$in = fopen($filename, 'rb');
		if (!$in) {
			echo "Could not open {$filename} file\n";
			fclose($in);
			return null;
		}

		// make sure first 4 chars are 'MMDB'
		$entry = MMDBParser::read_entry($in);
		if ($entry['id'] != 1111772493) {
			echo "Not an mmdb file: '{$filename}'\n";
			fclose($in);
			return null;
		}

		// start at offset = 8 since that is where the categories start
		// find the category
		$entry = MMDBParser::find_entry($in, $categoryId, 8);
		if ($entry == null) {
			echo "Could not find categoryID: '{$categoryId}'\n";
			fclose($in);
			return null;
		}

		// find the instance
		$entry = MMDBParser::find_entry($in, $instanceId, $entry['offset']);
		if ($entry == null) {
			echo "Could not find instance id: '{$instanceId}' for categoryId: '{$categoryId}'\n";
			fclose($in);
			return null;
		}

		fseek($in, $entry['offset']);
		$message = MMDBParser::read_string($in);
		MMDBParser::$mmdb[$categoryId][$instanceId] = $message;

		fclose($in);

		return $message;
	}
	
	private static function find_entry(&$in, $id, $offset) {
		fseek($in, $offset);

		do {
			$previousEntry = $entry;
			$entry = MMDBParser::read_entry($in);
			
			if ($previousEntry != null && $entry['id'] < $previousEntry['id']) {
				return null;
			}
		} while ($id != $entry['id']);

		return $entry;
	}
	
	/**
	 * @returns array($id, $offset)
	 */
	private static function read_entry(&$in) {
		return array('id' => MMDBParser::read_long($in), 'offset' => MMDBParser::read_long($in));
	}
	
	private static function read_long(&$in) {
		return array_pop(unpack("L", fread($in, 4)));
	}
	
	private static function read_string(&$in) {
		$message = '';
		$char = '';
		
		$char = fread($in, 1);
		while ($char !== "\0") {
			$message .= $char;
			$char = fread($in, 1);
		}
		
		return $message;
	}
}

?>
