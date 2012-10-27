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
 * Does the actual work, converts requests into responses
 * @author dennishaarbrink
 *
 */
class Worker
{
	/**
	 * @var object service object
	 */
    protected $service;
    /**
     * @var array list of server methods
     */
    protected $functions;
    /**
     * @var array generated map of server methods
     */
    protected $map = array();
    /**
     * @param object $service
     * @param array $functions
     */
    public function __construct($service, $functions)
    {
        $this->service = $service;
        $this->functions = $functions;

        $this->map = $this->createCallMap();
    }
    /**
     * Converts request into response
     * @param unknown_type $message
     */
    public function handle($message)
    {
        $method = $message->method;
        $params = $message->params;

        if (!isset($this->map[$method])) {
            throw new Exception\MethodNotFoundException();
        }

        return call_user_func_array($this->map[$method], $params);
    }
    /**
     * Looks at the service and functions and populates the call map
     * @return array
     */
    protected function createCallMap()
    {
        $map = array();
        foreach ($this->functions as $func) {
            if (is_string($func)) {
                $map[$func] = $func;
            } elseif (is_array($func)) {
                $map[$func[1]] = $func;
            }
        }
        if (is_object($this->service)) {
            foreach (get_class_methods($this->service) as $func) {
                $map[$func] = array($this->service, $func);
            }
        }
        return $map;
    }
}
