<?php

namespace Budabot\Core;

/**
 * @name: MMDBParser
 * @author: Tyrence (RK2)
 * @description: reads entries from the text.mdb file
 */
class MMDBParser {
	private $mmdb = array();
	private $file;
	
	private $logger;
	
	public function __construct($file) {
		$this->file = $file;
		$this->logger = new LoggerWrapper('MMDBParser');
	}

	public function getMessageString($categoryId, $instanceId) {
		// check for entry in cache
		if (isset($this->mmdb[$categoryId][$instanceId])) {
			return $this->mmdb[$categoryId][$instanceId];
		}

		$in = $this->open_file();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		// find the category
		$category = $this->find_entry($in, $categoryId, 8);
		if ($category === null) {
			$this->logger->log('error', "Could not find categoryID: '{$categoryId}'");
			fclose($in);
			return null;
		}

		// find the instance
		$instance = $this->find_entry($in, $instanceId, $category['offset']);
		if ($instance === null) {
			$this->logger->log('error', "Could not find instance id: '{$instanceId}' for categoryId: '{$categoryId}'");
			fclose($in);
			return null;
		}

		fseek($in, $instance['offset']);
		$message = $this->read_string($in);
		$this->mmdb[$categoryId][$instanceId] = $message;

		fclose($in);

		return $message;
	}

	public function findAllInstancesInCategory($categoryId) {
		$in = $this->open_file();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		// find the category
		$category = $this->find_entry($in, $categoryId, 8);
		if ($category === null) {
			$this->logger->log('error', "Could not find categoryID: '{$categoryId}'");
			fclose($in);
			return null;
		}
		
		fseek($in, $category['offset']);

		// find all instances
		$instances = array();
		$instance = $this->read_entry($in);
		while ($previousInstance == null || $instance['id'] > $previousInstance['id']) {
			$instances[] = $instance;
			$previousInstance = $instance;
			$instance = $this->read_entry($in);
		}
		
		fclose($in);

		return $instances;
	}
	
	public function getCategories() {
		$in = $this->open_file();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		fseek($in, 8);

		// find all categories
		$categories = array();
		$category = $this->read_entry($in);
		while ($previousCategory == null || $category['id'] > $previousCategory['id']) {
			$categories[] = $category;
			$previousCategory = $category;
			$category = $this->read_entry($in);
		}
		
		fclose($in);

		return $categories;
	}

	private function open_file($filename = "data/text.mdb") {
		$in = fopen($filename, 'rb');
		if (!$in) {
			$this->logger->log('error', "Could not open file: '{$filename}'");
			fclose($in);
			return null;
		}

		// make sure first 4 chars are 'MMDB'
		$entry = $this->read_entry($in);
		if ($entry['id'] != 1111772493) {
			$this->logger->log('error', "Not an mmdb file: '{$filename}'");
			fclose($in);
			return null;
		}

		return $in;
	}

	private function find_entry(&$in, $id, $offset) {
		fseek($in, $offset);

		do {
			$previousEntry = $entry;
			$entry = $this->read_entry($in);

			if ($previousEntry != null && $entry['id'] < $previousEntry['id']) {
				return null;
			}
		} while ($id != $entry['id']);

		return $entry;
	}

	/**
	 * @returns array($id, $offset)
	 */
	private function read_entry(&$in) {
		return array('id' => $this->read_long($in), 'offset' => $this->read_long($in));
	}

	private function read_long(&$in) {
		return array_pop(unpack("L", fread($in, 4)));
	}

	private function read_string(&$in) {
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
