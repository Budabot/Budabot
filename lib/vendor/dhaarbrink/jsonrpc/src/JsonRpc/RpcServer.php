<?php
/**
 * Copyright 2012 Dennis Haarbrink <dhaarbrink@gmail.com>
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for any purpose and without fee is hereby
 * granted, provided that the above copyright notice appear in all
 * copies and that both that the copyright notice and this
 * permission notice and warranty disclaimer appear in supporting
 * documentation, and that the name of the author not be used in
 * advertising or publicity pertaining to distribution of the
 * software without specific, written prior permission.
 *
 * The author disclaim all warranties with regard to this
 * software, including all implied warranties of merchantability
 * and fitness.  In no event shall the author be liable for any
 * special, indirect or consequential damages or any damages
 * whatsoever resulting from loss of use, data or profits, whether
 * in an action of contract, negligence or other tortious action,
 * arising out of or in connection with the use or performance of
 * this software.
 */
namespace JsonRpc;

use JsonRpc\Transport;

/**
 * 
 * Base server implementation
 *
 */
class RpcServer
{
	/**
	 * @var string incoming payload
	 */
    protected $payload;
    /**
     * @var Transport\Input input interface
     */
    protected $input;
    /**
     * @var Transport\Output output interface
     */
    protected $output;
    /**
     * @var mixed original error handler
     */
    protected $error_handler;
    /**
     * @var object service class
     */
    protected $service;
    /**
     * @var array list of service methods
     */
    protected $functions = array();
    /**
     * The beef of the server
     * @param string $payload
     * @param Transport\Input $input
     * @param Transport\Output $output
     */
    public function handle($payload = null, Transport\Input $input = null, Transport\Output $output = null)
    {
        $this->setupErrorHandler();

        $this->input = $input ?: new Transport\BasicInput();
        $this->output = $output ?: new Transport\BasicOutput();
        $payload = $payload ?: $this->input->getPayload();

        $request = new Request($payload);
        $response = new Response();
        $worker = new Worker($this->service, $this->functions);

        try {
            $request->parse();

            foreach ($request->getMessages() as $message) {
                $response->add($worker->handle($message), $message->id);
            }
        } catch (\Exception $e) {
            $mid = (isset($message) && isset($message->id)) ? $message->id : null;
            $response->add($e, $mid);
        }

        $response->setBatch($request->isBatch());
        $this->output->out($response);

        $this->restoreErrorHandler();
    }
    /**
     * Sets the class to get server methods from
     * @param string $class
     */
    public function setClass($class)
    {
        $this->service = new $class();
    }
    /**
     * Sets an object to get server methods from
     * @param object $obj
     */
    public function setObject($obj)
    {
        $this->service = $obj;
    }
    /**
     * Adds a single method
     * @param \Callable $function
     */
    public function addFunction($function)
    {
        $this->functions[] = $function;
    }
    /**
     * sets up the error handler
     */
    public function setupErrorHandler()
    {
        $this->error_handler = set_error_handler(array($this, 'error_handler'));
    }
    /**
     * Restores the original error handler
     */
    public function restoreErrorHandler()
    {
        restore_error_handler($this->error_handler);
    }
    /**
     * The actual error handler, converts errors to exceptions
     * @param integer $errno
     * @param string $errstr
     * @throws Exception\ServerErrorException
     */
    public function error_handler($errno, $errstr /* , $errfile, $errline, array $errcontext */)
    {
        throw new Exception\ServerErrorException($errstr, $errno);
    }
}
