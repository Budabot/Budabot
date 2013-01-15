function redirectToIndex() {
	window.location.href = '/webui_module/';
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
		url: 'check_login',
		type: 'post',
		data: data,
		success: function(result) {
			if (result === '1') {
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
