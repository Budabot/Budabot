<?php

namespace Budabot\Core;

/**
 * @Instance
 */
class Http {
	
	/** @Inject */
	public $timer;

	/**
	 * Requests contents of given $uri using GET method and returns AsyncHttp
	 * object which has additional methods for controlling how the query is done.
	 *
	 * You can get both HTTP and HTTPS URIs with method.
	 *
	 * For more info, see AsyncHttp class.
	 *
	 * Asynchronous example:
	 * <code>
	 * $this->http->get("http://www.google.com/")->withCallback(function($response) {
	 *     print $response->body;
	 * });
	 * </code>
	 *
	 * Synchronous example:
	 * <code>
	 * $response = $this->http->get("http://www.google.com/")->waitAndReturnResponse();
	 * print $response->body;
	 * </code>
	 *
	 * @param string $uri the requested URI
	 * @return AsyncHttp
	 */
	public function get($uri) {
		$asyncHttp = new AsyncHttp('get', $uri);
		Registry::injectDependencies($asyncHttp);
		$this->timer->callLater(0, array($asyncHttp, 'execute'));
		return $asyncHttp;
	}

	/**
	 * Requests contents of given $uri using POST method and returns AsyncHttp
	 * object which has additional methods for controlling how the query is done.
	 *
	 * See get() for code example.
	 *
	 * @param string $uri the requested URI
	 * @return AsyncHttp
	 */
	public function post($uri) {
		$asyncHttp = new AsyncHttp('post', $uri);
		Registry::injectDependencies($asyncHttp);
		$this->timer->callLater(0, array($asyncHttp, 'execute'));
		return $asyncHttp;
	}
}
