(function() {
	$('#register-btn').on('click', function(e) {
		$('.validation-error').remove();
		var confirmPassword = $('#confirm-user-password').val();
		var password = $('#user-password').val();
		var $email = $('#user-email');
		
		if($email.val().trim() === '') {
			e.preventDefault();
			$email.after('<span class="validation-error">Please enter an email address!</span>');
			return;
		}
		
		if(password.trim() == '') {
			e.preventDefault();
			$('#user-password').after('<span class="validation-error">Please enter a password!</span>');
			return;
		}
		
		if(confirmPassword == '') {
			e.preventDefault();
			$('#confirm-user-password').after('<span class="validation-error">Please confirm your password!</span>');
			return;
		}
		
		if(confirmPassword !== password) {
			e.preventDefault();
			$('#confirm-user-password').after('<span class="validation-error">The confirmation password is different! </span>');
			return;
		}
	});
})();