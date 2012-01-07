<?php

require_once 'CommandSearchController.class.php';
require_once 'Mockery/Loader.php';
$loader = new \Mockery\Loader;
$loader->register();

class CommandSearchControllerTest extends PHPUnit_Framework_TestCase {
	public function setup() {
		$this->testObj = new CommandSearchController;
		$this->testObj->chatBot = Mockery::mock('Budabot');
		$this->testObj->db = Mockery::mock('Database');
		$this->testObj->accessLevel = Mockery::mock('AccessLevel');
		$this->testObj->view = Mockery::mock('CommandSearchView');
		
		// set default mock expectations
		$this->testObj->chatBot->shouldReceive('send')->byDefault();
		$this->testObj->db->shouldReceive('query')->andReturn(array())->byDefault();
		$this->testObj->accessLevel->shouldReceive('checkAccess')->byDefault();
		$this->testObj->view->shouldReceive('render')->byDefault();
	}

	public function teardown() {
		\Mockery::close();
	}

	public function testSearchCommandQueriesDb() {
		$this->testObj->db->shouldReceive('query')->with("SELECT DISTINCT module, cmd, help, description FROM cmdcfg_<myname> WHERE status = 1")->andReturn(array())->once();
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandWithZeroDbResultsRendersNoResults() {
		// set mock expectations
		$this->testObj->db->shouldReceive('query')->andReturn(array());
		$this->testObj->view->shouldReceive('render')->with(array(), Mockery::any(), Mockery::any());
		// try searching
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandRendersExactResults() {
		// set mock expectations
		$this->testObj->db->shouldReceive('query')->andReturn(array(
			$this->buildCmdRow("foobar", "FOOBAR_MODULE", "help text", "desc text")
		));
		$this->testObj->view->shouldReceive('render')->with(Mockery::any(), Mockery::any(), true);
		// try searching
		$this->testObj->searchCommand('cmdsearch foobar', 'notused', 'user', 'user', array('cmdsearch foobar', 'foobar'));
	}

	public function testSearchCommandRendersFuzzyResults() {
		// set mock expectations
		$this->testObj->db->shouldReceive('query')->andReturn(array(
			$this->buildCmdRow("foobar", "FOOBAR_MODULE", "help text", "desc text")
		));
		$this->testObj->view->shouldReceive('render')->with(Mockery::any(), Mockery::any(), false);
		// try searching
		$this->testObj->searchCommand('cmdsearch foobaz', 'notused', 'user', 'user', array('cmdsearch foobaz', 'foobaz'));
	}

	public function testSearchCommandAccessLevelIsCalled() {
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

