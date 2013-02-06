<?php

namespace Guzzle\Http;

use Guzzle\Common\Event;
use Guzzle\Http\Url;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Http\Exception\TooManyRedirectsException;
use Guzzle\Http\Exception\CouldNotRewindStreamException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Plugin to implement HTTP redirects. Can redirect like a web browser or using strict RFC 2616 compliance
 */
class RedirectPlugin implements EventSubscriberInterface
{
    const REDIRECT_COUNT = 'redirect.count';
    const MAX_REDIRECTS = 'redirect.max';
    const STRICT_REDIRECTS = 'redirect.strict';
    const PARENT_REQUEST = 'redirect.parent_request';
    const DISABLE = 'redirect.disable';

    /**
     * @var int Default number of redirects allowed when no setting is supplied by a request
     */
    protected $defaultMaxRedirects = 5;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'request.sent'  => array('onRequestSent', 100),
            'request.clone' => 'onRequestClone'
        );
    }

    /**
     * Clean up the parameters of a request when it is cloned
     *
     * @param Event $event Event emitted
     */
    public function onRequestClone(Event $event)
    {
        $event['request']->getParams()->remove(self::REDIRECT_COUNT)->remove(self::PARENT_REQUEST);
    }

    /**
     * Called when a request receives a redirect response
     *
     * @param Event $event Event emitted
     */
    public function onRequestSent(Event $event)
    {
        $response = $event['response'];
        $request = $event['request'];

        // Only act on redirect requests with Location headers
        if (!$response || !$response->isRedirect() || !$response->hasHeader('Location')
            || $request->getParams()->get(self::DISABLE)
        ) {
            return;
        }

        // Prepare the request for a redirect and grab the original request that started the transaction
        $originalRequest = $this->prepareRedirection($request);

        // Create a redirect request based on the redirect rules set on the request
        $redirectRequest = $this->createRedirectRequest(
            $request,
            $event['response']->getStatusCode(),
            trim($response->getHeader('Location')),
            $originalRequest
        );

        // Send the redirect request and hijack the response of the original request
        $redirectResponse = $redirectRequest->send();
        $redirectResponse->setPreviousResponse($event['response']);
        $request->setResponse($redirectResponse);
    }

    /**
     * Create a redirect request for a specific request object
     *
     * Takes into account strict RFC compliant redirection (e.g. redirect POST with POST) vs doing what most clients do
     * (e.g. redirect POST with GET).
     *
     * @param RequestInterface $request    Request being redirected
     * @param RequestInterface $original   Original request
     * @param int              $statusCode Status code of the redirect
     * @param string           $location   Location header of the redirect
     *
     * @return RequestInterface Returns a new redirect request
     * @throws CouldNotRewindStreamException If the body needs to be rewound but cannot
     */
    protected function createRedirectRequest(
        RequestInterface $request,
        $statusCode,
        $location,
        RequestInterface $original
    ) {
        $redirectRequest = null;
        $strict = $original->getParams()->get(self::STRICT_REDIRECTS);
        // Use a GET request if this is an entity enclosing request and we are not forcing RFC compliance, but rather
        // emulating what all browsers would do
        if ($request instanceof EntityEnclosingRequestInterface && !$strict && $statusCode <= 302) {
            $redirectRequest = $this->cloneRequestWithGetMethod($request);
        } else {
            $redirectRequest = clone $request;
        }

        $location = Url::factory($location);
        // If the location is not absolute, then combine it with the original URL
        if (!$location->isAbsolute()) {
            $originalUrl = $redirectRequest->getUrl(true);
            // Remove query string parameters and just take what is present on the redirect Location header
            $originalUrl->getQuery()->clear();
            $location = $originalUrl->combine((string) $location);
        }

        $redirectRequest->setUrl($location);
        $redirectRequest->getParams()->set(self::PARENT_REQUEST, $request);

        // Rewind the entity body of the request if needed
        if ($redirectRequest instanceof EntityEnclosingRequestInterface && $redirectRequest->getBody()) {
            $body = $redirectRequest->getBody();
            // Only rewind the body if some of it has been read already, and throw an exception if the rewind fails
            if ($body->ftell() && !$body->rewind()) {
                throw new CouldNotRewindStreamException(
                    'Unable to rewind the non-seekable entity body of the request after redirecting. cURL probably '
                    . 'sent part of body before the redirect occurred. Try adding acustom rewind function using on the '
                    . 'entity body of the request using setRewindFunction().'
                );
            }
        }

        return $redirectRequest;
    }

    /**
     * Clone a request while changing the method to GET. Emulates the behavior of
     * {@see Guzzle\Http\Message\Request::clone}, but can change the HTTP method.
     *
     * @param EntityEnclosingRequestInterface $request Request to clone
     *
     * @return RequestInterface Returns a GET request
     */
    protected function cloneRequestWithGetMethod(EntityEnclosingRequestInterface $request)
    {
        // Create a new GET request using the original request's URL
        $redirectRequest = $request->getClient()->get($request->getUrl());
        $redirectRequest->getCurlOptions()->replace($request->getCurlOptions()->getAll());
        // Copy over the headers, while ensuring that the Content-Length is not copied
        $redirectRequest->setHeaders($request->getHeaders()->getAll())->removeHeader('Content-Length');
        $redirectRequest->setEventDispatcher(clone $request->getEventDispatcher());
        $redirectRequest->getParams()
            ->replace($request->getParams()->getAll())
            ->remove('curl_handle')->remove('queued_response')->remove('curl_multi');

        return $redirectRequest;
    }

    /**
     * Prepare the request for redirection and enforce the maximum number of allowed redirects per client
     *
     * @param RequestInterface $request Request to prepare and validate
     *
     * @return RequestInterface Returns the original request
     */
    protected function prepareRedirection(RequestInterface $request)
    {
        $original = $request;
        // The number of redirects is held on the original request, so determine which request that is
        while ($parent = $original->getParams()->get(self::PARENT_REQUEST)) {
            $original = $parent;
        }

        // Always associate the parent response with the current response so that a chain can be established
        if ($parent = $request->getParams()->get(self::PARENT_REQUEST)) {
            $request->getResponse()->setPreviousResponse($parent->getResponse());
        }

        $params = $original->getParams();
        // This is a new redirect, so increment the redirect counter
        $current = $params->get(self::REDIRECT_COUNT) + 1;
        $params->set(self::REDIRECT_COUNT, $current);

        // Use a provided maximum value or default to a max redirect count of 5
        $max = $params->hasKey(self::MAX_REDIRECTS)
            ? $params->get(self::MAX_REDIRECTS)
            : $this->defaultMaxRedirects;

        // Throw an exception if the redirect count is exceeded
        if ($current > $max) {
            return $this->throwTooManyRedirectsException($request);
        }

        return $original;
    }

    /**
     * Throw a too many redirects exception for a request
     *
     * @param RequestInterface $request Request
     * @throws TooManyRedirectsException when too many redirects have been issued
     */
    protected function throwTooManyRedirectsException(RequestInterface $request)
    {
        $responses = array();

        // Create a nice message to use when throwing the exception that shows each request/response transaction
        do {
            $response = $request->getResponse();
            $responses[] = '> ' . $request->getRawHeaders() . "\n\n< " . $response->getRawHeaders();
            $request = $response->getPreviousResponse() ? $response->getPreviousResponse()->getRequest() : null;
        } while ($request);

        $transaction = implode("* Sending redirect request\n", array_reverse($responses));

        throw new TooManyRedirectsException("Too many redirects were issued for this transaction:\n{$transaction}");
    }
}
