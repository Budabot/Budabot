var LogConsole = (function() {

	logMaxLines = 1000;

	function isLogConsoleAtBottom() {
		if (log_console.scrollTopMax != undefined) {
			return log_console.scrollTop == log_console.scrollTopMax;
		}
		return true;
	}

	function scrollLogConsoleToBottom() {
		if (log_console.scrollTopMax != undefined) {
			log_console.scrollTop = log_console.scrollTopMax;
		} else {
			log_console.scrollTop = 999999999;
		}
	}

	function limitLogConsoleLines() {
		if (log_console.children.length > logMaxLines) {
			$('#log_console').find(':first-child').remove();
		}
	}

	function addMessageToLogConsole(message) {
		atBottom = isLogConsoleAtBottom();

		log_console.innerHTML += "<div>" + message + "</div>\n";

		limitLogConsoleLines();

		if (atBottom) {
			scrollLogConsoleToBottom();
		}
	}

	function getAttribute(name) {
		return $('#log_console').attr(name);
	}

	$.subscribe('wamp_success', function(topic, session) {
		session.subscribe(getAttribute('data-topic'), function (topic, event) {
			addMessageToLogConsole(event);
		});
	});

	$.subscribe('wamp_failed', function(topic, data) {
		addMessageToLogConsole(data.reason);
	});

})();
