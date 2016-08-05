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

		$in = $this->openFile();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		// find the category
		$category = $this->findEntry($in, $categoryId, 8);
		if ($category === null) {
			$this->logger->log('error', "Could not find categoryID: '{$categoryId}'");
			fclose($in);
			return null;
		}

		// find the instance
		$instance = $this->findEntry($in, $instanceId, $category['offset']);
		if ($instance === null) {
			$this->logger->log('error', "Could not find instance id: '{$instanceId}' for categoryId: '{$categoryId}'");
			fclose($in);
			return null;
		}

		fseek($in, $instance['offset']);
		$message = $this->readString($in);
		$this->mmdb[$categoryId][$instanceId] = $message;

		fclose($in);

		return $message;
	}

	public function findAllInstancesInCategory($categoryId) {
		$in = $this->openFile();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		// find the category
		$category = $this->findEntry($in, $categoryId, 8);
		if ($category === null) {
			$this->logger->log('error', "Could not find categoryID: '{$categoryId}'");
			fclose($in);
			return null;
		}
		
		fseek($in, $category['offset']);

		// find all instances
		$instances = array();
		$instance = $this->readEntry($in);
		while ($previousInstance == null || $instance['id'] > $previousInstance['id']) {
			$instances[] = $instance;
			$previousInstance = $instance;
			$instance = $this->readEntry($in);
		}
		
		fclose($in);

		return $instances;
	}
	
	public function getCategories() {
		$in = $this->openFile();
		if ($in === null) {
			return null;
		}

		// start at offset = 8 since that is where the categories start
		fseek($in, 8);

		// find all categories
		$categories = array();
		$category = $this->readEntry($in);
		while ($previousCategory == null || $category['id'] > $previousCategory['id']) {
			$categories[] = $category;
			$previousCategory = $category;
			$category = $this->readEntry($in);
		}
		
		fclose($in);

		return $categories;
	}

	private function openFile($filename = "data/text.mdb") {
		$in = fopen($filename, 'rb');
		if (!$in) {
			$this->logger->log('error', "Could not open file: '{$filename}'");
			fclose($in);
			return null;
		}

		// make sure first 4 chars are 'MMDB'
		$entry = $this->readEntry($in);
		if ($entry['id'] != 1111772493) {
			$this->logger->log('error', "Not an mmdb file: '{$filename}'");
			fclose($in);
			return null;
		}

		return $in;
	}

	private function findEntry(&$in, $id, $offset) {
		fseek($in, $offset);

		do {
			$previousEntry = $entry;
			$entry = $this->readEntry($in);

			if ($previousEntry != null && $entry['id'] < $previousEntry['id']) {
				return null;
			}
		} while ($id != $entry['id']);

		return $entry;
	}

	/**
	 * @returns array($id, $offset)
	 */
	private function readEntry(&$in) {
		return array('id' => $this->readLong($in), 'offset' => $this->readLong($in));
	}

	private function readLong(&$in) {
		return array_pop(unpack("L", fread($in, 4)));
	}

	private function readString(&$in) {
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
