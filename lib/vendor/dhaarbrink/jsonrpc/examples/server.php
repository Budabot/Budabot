<?php

	//just in case, show all errors we've got
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', 1);

    //get the autoloader
    require_once __DIR__ . '/../vendor/autoload.php';

    //define a simple service on which methods are called
    class TestClass
    {
        public function testfunc()
        {
            $ret = new stdClass();
            $ret->foo = array('bar' => 'baz');
            return $ret;
        }
        public function testfunc2()
        {
        	return 42;
        }
        public function throwsException($param)
        {
        	throw new \Exception(sprintf(
        		"The client passed '%s'",
        		$param
        	));
        }
    }

    //get a server instance
    $server = new \JsonRpc\RpcServer();
    //register our service class
    $server->setClass('TestClass');
    //handle the incoming payload
    $server->handle();
