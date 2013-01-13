
$(document).ready(function() {
	$('#login').click(function() {
		$.ajax({
			url: 'check_login'
		});
	});
});
