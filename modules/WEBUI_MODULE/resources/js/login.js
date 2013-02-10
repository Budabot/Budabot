var loginModule = (function() {
	function redirectToIndex() {
		window.location.href = '.';
	}

	function setErrorMessage(message) {
		$('#message').text(message);
	}

	function onLoginClicked() {
		var data = {
			username: $('#username').val(),
			password: $('#password').val()
		};
		$.ajax({
			url: 'do_login',
			type: 'post',
			data: data,
			dataType: 'text',
			success: function(response) {
				console.log(response);
				if (response === '1') {
					redirectToIndex();
				} else {
					setErrorMessage('Invalid username or password');
				}
			}
		});
		return false;
	}

	$(document).ready(function() {
		$('#login').click(onLoginClicked);
	});
})();
