<?php

require_once 'CommandSearchController.class.php';
require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();

class CommandSearchControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * This method is called just before executing a testXXX method.
	 */
	public function setup() {
		$this->testObj = new CommandSearchController;
		$this->testObj->chatBot = Mockery::mock('Budabot');
		$this->testObj->db = Mockery::mock('Database');
		$this->testObj->accessLevel = Mockery::mock('AccessLevel');
		$this->testObj->view = Mockery::mock('CommandSearchView');

		// the mocks will support following calls by default, but these can be
		// overwitten seperately in each invidual testXXX-method
		$this->testObj->chatBot->shouldReceive('send')->byDefault();
		$this->testObj->db->shouldReceive('query')->andReturn(array())->byDefault();
		$this->testObj->accessLevel->shouldReceive('checkAccess')->byDefault();
		$this->testObj->view->shouldReceive('render')->byDefault();
	}

	/**
	 * This method is called right after executing a testXXX method.
	 */
	public function teardown() {
		// evaluate all mock expectations
		\Mockery::close();
	}

	public function testSearchCommandQueriesDb() {
		// CommandSearchController must call DB's query-method once with
		// following sql-command
		$this->testObj->db->shouldReceive('query')->with("SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname> WHERE status = 1")->andReturn(array())->once();
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandWithZeroDbResultsRendersNoResults() {
		// CommandSearchController must call view's render-method with first
		// parameter as an empty array since DB will return empty result
		// by default
		$this->testObj->view->shouldReceive('render')->with(array(), Mockery::any(), Mockery::any())->once();
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandRendersExactResults() {
		// CommandSearchController must tell the view that an exact match was
		// found (#3 parameter) when query() returns command that matches to
		// searched word
		$this->testObj->db->shouldReceive('query')->andReturn(array(
			$this->buildCmdRow("foobar", "FOOBAR_MODULE", "help text", "desc text")
		));
		$this->testObj->view->shouldReceive('render')->with(Mockery::any(), Mockery::any(), true)->once();
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandRendersFuzzyResults() {
		// CommandSearchController must tell the view that an fuzzy matching
		// was done (#3 parameter) when query() returns no command that would
		// exactly match to the searched word
		$this->testObj->db->shouldReceive('query')->andReturn(array(
			$this->buildCmdRow("foobar", "FOOBAR_MODULE", "help text", "desc text")
		));
		$this->testObj->view->shouldReceive('render')->with(Mockery::any(), Mockery::any(), false)->once();
		$this->testObj->searchCommand('cmdsearch foobaz', 'notused', 'user', 'user', array('cmdsearch foobaz', 'foobaz'));
	}

	public function testSearchCommandAccessLevelIsCalled() {
		// CommandSearchController must check sender's access level
		// with checkAccess() 
		$this->testObj->accessLevel->shouldReceive('checkAccess')->with('postman', 'mod')->once();
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'postman', 'user', array('cmdsearch foobar', 'foobar'));
	}

	private function buildCmdRow($cmd, $module, $help, $description) {
		$Row = new StdClass;
		$object->cmd = $cmd;
		$object->module = $module;
		$object->help = $help;
		$object->description = $description;
		return $object;
	}
}

