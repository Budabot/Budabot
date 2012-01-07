<?php

require_once 'CommandSearchView.class.php';

/**
 * Fake implementation of Budabot's Text-class.
 */
class Text {
	public function make_chatcmd( $title, $links ) {
		return "<link value=\"$links\">$title</link>";
	}
	
	public function make_blob( $name, $content, $style = NULL ) {
		return "<blob content=\"$content\">$name</blob>";
	}
}

class CommandSearchViewTest extends PHPUnit_Framework_TestCase {
	public function setup() {
		$this->testObj = new CommandSearchView;
	}
	
	public function testNoResultsReturnsNoCommandFound() {
		$msg = $this->testObj->render(array(), false, false);
		$this->assertEquals('No results found.', $msg);
	}
	
	public function teardown() {
		\Mockery::close();
	}
}

