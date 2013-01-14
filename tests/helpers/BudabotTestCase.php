<?php

require_once "lib/addendum-0.4.1/annotations.php";
require_once "core/annotations.php";

class ConstraintIsCallable extends \PHPUnit_Framework_Constraint {
	public function matches($other) {
		return is_callable($other);
	}

	public function toString() {
		return 'is callable';
	}
}

abstract class BudabotTestCase extends \PHPUnit_Framework_TestCase {

	protected function hasSetupHandler($object) {
		return $this->getSetupHandlerName($object) != '';
	}

	protected function callSetupHandler($object) {
		$name = $this->getSetupHandlerName($object);
		if ($name) {
			$object->$name();
		}
	}

	protected function injectMock($target, $injectName, $mockName) {
		$mock = Phake::mock($mockName);
		$this->injectDependency($target, $injectName, $mock);
		return $mock;
	}

	protected function isCallable() {
		return new ConstraintIsCallable;
	}

	protected function hasInjection($object, $injectName) {
		return $this->findInjectionVarName($object, $injectName) != '';
	}

	protected function isAutoInstanced($object) {
		$reflection = new \ReflectionAnnotatedClass($object);
		return $reflection->hasAnnotation('Instance');
	}

	private function injectDependency($object, $injectName, $dependency) {
		$varName = $this->findInjectionVarName($object, $injectName);
		if ($varName) {
			$object->$varName = $dependency;
		}
	}

	private function getSetupHandlerName($object) {
		$reflection = new \ReflectionAnnotatedClass($object);
		forEach ($reflection->getMethods() as $method) {
			if ($method->hasAnnotation('Setup')) {
				return $method->name;
			}
		}
		return '';
	}

	private function findInjectionVarName($object, $injectName) {
		$reflection = new \ReflectionAnnotatedClass($object);
		forEach ($reflection->getProperties() as $property) {
			if (strtolower($injectName) == strtolower($property->name)
				&& $property->hasAnnotation('Inject')) {
				return $property->name;
			}
		}
		return '';
	}
}
