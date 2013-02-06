<?php
namespace Ratchet\Wamp;
use Ratchet\ConnectionInterface;
use Ratchet\AbstractConnectionDecorator;
use Ratchet\Wamp\ServerProtocol as WAMP;

/**
 * A ConnectionInterface object wrapper that is passed to your WAMP application
 * representing a client. Methods on this Connection are therefore different. 
 * @property stdClass $WAMP
 */
class WampConnection extends AbstractConnectionDecorator {
    /**
     * {@inheritdoc}
     */
    public function __construct(ConnectionInterface $conn) {
        parent::__construct($conn);

        $this->WAMP            = new \StdClass;
        $this->WAMP->sessionId = uniqid();
        $this->WAMP->prefixes  = array();

        $this->send(json_encode(array(WAMP::MSG_WELCOME, $this->WAMP->sessionId, 1, \Ratchet\VERSION)));
    }

    /**
     * Successfully respond to a call made by the client
     * @param string The unique ID given by the client to respond to
     * @param array An array of data to return to the client
     */
    public function callResult($id, array $data = array()) {
        return $this->send(json_encode(array(WAMP::MSG_CALL_RESULT, $id, $data)));
    }

    /**
     * Respond with an error to a client call
     * @param string The unique ID given by the client to respond to
     * @param string The URI given to identify the specific error
     * @param string A developer-oriented description of the error
     * @param string|null An optional human readable detail message to send back
     */
    public function callError($id, $errorUri, $desc = '', $details = null) {
        if ($errorUri instanceof Topic) {
            $errorUri = (string)$errorUri;
        }

        $data = array(WAMP::MSG_CALL_ERROR, $id, $errorUri, $desc);

        if (null !== $details) {
            $data[] = $details;
        }

        return $this->send(json_encode($data));
    }

    /**
     * @param string The topic to broadcast to
     * @param mixed Data to send with the event.  Anything that is json'able
     */
    public function event($topic, $msg) {
        return $this->send(json_encode(array(WAMP::MSG_EVENT, (string)$topic, $msg)));
    }

    /**
     * @param string
     * @param string
     */
    public function prefix($curie, $uri) {
        $this->WAMP->prefixes[$curie] = (string)$uri;

        return $this->send(json_encode(array(WAMP::MSG_PREFIX, $curie, (string)$uri)));
    }

    /**
     * Get the full request URI from the connection object if a prefix has been established for it
     * @param string
     * @return string
     */
    public function getUri($uri) {
        return (array_key_exists($uri, $this->WAMP->prefixes) ? $this->WAMP->prefixes[$uri] : $uri);
    }

    /**
     * @internal
     */
    public function send($data) {
        $this->getConnection()->send($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function close() {
        $this->getConnection()->close();
    }
}