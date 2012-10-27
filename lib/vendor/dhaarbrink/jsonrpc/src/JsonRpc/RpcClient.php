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

/**
 * Client implementation
 *
 */
class RpcClient
{
	/**
	 * @var string server url
	 */
    protected $url;
    /**
     * @var integer id for next message
     */
    protected $message_id = 1;
    /**
     * @var string last request
     */
    protected $last_request;
    /**
     * @var string stores the raw response text
     */
    protected $response_raw;
    /**
     * @var boolean whether we are in batch mode
     */
    protected $batch = false;
    /**
     * @var mixed holds the outgoing message(s)
     */
    protected $message;
    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }
    /**
     * Proxy to the registered server methods
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments)
    {
        $message = $this->createMessage($method, $arguments);

        if ($this->batch) {
        	$this->message[] = $message;
        } else {
        	$this->message = $message;
        }
        
		if (!$this->batch) {
			//dirty trick to use send outside of batch mode
			$this->batch = true;
			$result = $this->send();
			$this->batch = false;
			
			return $result;
		}
		
		//we're in batch mode, return $this so we can chain
		return $this;
    }
    /**
     * Tells the client to go batch mode
     */
    public function batch()
    {
    	$this->batch = true;
    	$this->message = array();
    	return $this;
    }
    /**
     * Constructs and sends the payload and returns the server response
     * @return mixed 
     */
    public function send()
    {
    	if (!$this->batch) {
    		throw new Exception("Can't use send outside of batch mode");
    	}
    	
    	//back to normal mode
    	$this->batch = false;
    	
        $this->last_request = $this->message;

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => json_encode($this->message)
            )
        );

        $context  = stream_context_create($opts);
        $response = file_get_contents($this->url, false, $context);
        $this->response_raw = $response;
        return json_decode($response);
    }
    /**
     * Returns the last request
     * @return string
     */
    public function getLastRequest()
    {
        return $this->last_request;
    }
    /**
     * Returns the raw server response
     * @return string
     */
    public function getResponseRaw()
    {
        return $this->response_raw;
    }
    /**
     * Creates a message
     * @param string $method
     * @param array $arguments
     * @return array
     */
    protected function parseResponse($response)
    {
        if (isset($response->error)) {
            throw new Exception($response->error->message, $response->error->code);
        }
        return $response->result;
    }
    protected function createMessage($method, $arguments)
    {
        return array(
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $arguments,
            'id' => $this->getMessageId(),
        );
    }
    /**
     * Returns incrementing message id
     * @return integer
     */
    protected function getMessageId()
    {
        return $this->message_id++;
    }
}
