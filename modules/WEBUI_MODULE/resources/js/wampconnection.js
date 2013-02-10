
var WampConnection = (function() {

	function connect(webSocketUri) {
		$(document).ready(function() {
			ab.connect(webSocketUri,

				function(session) {
					$.publish('wamp_success', session);
				},

				function(code, reason) {
					console.log('Failed to connect web socket at: ' + webSocketUri);
					$.publish('wamp_failed', { code: code, reason: reason } );
				}
			);
		});
	}

	return {
		connect: connect
	};
})();
