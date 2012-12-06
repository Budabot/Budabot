<?php

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
	 * This method is asynchronous, the execution should return immediately
	 * from this method.
	 *
	 * You can get both HTTP and HTTPS URIs with method.
	 *
	 * For more info, see AsyncHttp class.
	 *
	 * Example usage:
	 * <code>
	 * $this->http->get("http://www.google.com/")->withCallback(function($response) {
	 *     print $response->body;
	 * });
	 * </code>
	 *
	 * @param string $uri the requested URI
	 * @return AsyncHttp
	 */
	public function get($uri) {
		$http = new AsyncHttp('get', $uri);
		Registry::injectDependencies($http);
		$this->timer->callLater(0, array($http, 'execute'));
		return $http;
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
		$http = new AsyncHttp('post', $uri);
		Registry::injectDependencies($http);
		$this->timer->callLater(0, array($http, 'execute'));
		return $http;
	}
}

?>
