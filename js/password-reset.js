(function() {
	$('#password-reset-btn').on('click', function(e) {
		$('.validation-error').remove();
		var $email = $('#user-email');
		
		if($email.val().trim() === '') {
			e.preventDefault();
			$email.after('<span class="validation-error">Please enter an email address!</span>');
			return;
		}
	});
})();