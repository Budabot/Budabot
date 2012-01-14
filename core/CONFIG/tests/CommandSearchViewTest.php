<?php

require_once 'CommandSearchView.class.php';

class CommandSearchViewTest extends PHPUnit_Framework_TestCase {
	const ExactMatch = true;
	const NotExactMatch = false;

	/**
	 * This method is called just before executing a testXXX method.
	 */
	public function setup() {
		$this->testObj = new CommandSearchView;
		$this->testObj->text = Mockery::mock('Text');

		// fake implementation of the Text-class
		$this->testObj->text->shouldReceive('make_chatcmd')->andReturnUsing(function($title, $links) {
			return "<link value=\"$links\">$title</link>";
		})->byDefault();
		$this->testObj->text->shouldReceive('make_blob')->andReturnUsing(function($name, $content, $style=NULL) {
			return "<blob content=\"$content\">$name</blob>";
		})->byDefault();
	}

	/**
	 * This method is called right after executing a testXXX method.
	 */
	public function teardown() {
		\Mockery::close();
	}

	public function testNoResultsReturnsNoCommandFound() {
		// when no results are given, user must be informed
		$msg = $this->testObj->render(array(), false, self::ExactMatch);
		$this->assertEquals('No results found.', $msg);
	}

	public function testNonExactMatchesReturnsExactMatchNotFound() {
		$results = array(
			$this->buildResult("foobaz", "FOOBAR_MODULE", "", "desc text"),
		);
		$msg = $this->testObj->render($results, false, self::NotExactMatch);
		$this->assertRegExp('/Exact match not found/', $msg);
		$this->assertRegExp('/Did you mean one of these\?/', $msg);
}

	private function buildResult($cmd, $module, $help, $description) {
		$Row = new StdClass;
		$object->cmd = $cmd;
		$object->module = $module;
		$object->help = $help;
		$object->description = $description;
		return $object;
	}
}

