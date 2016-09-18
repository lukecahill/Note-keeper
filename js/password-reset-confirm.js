(function() {
    $('#reset-password-button').on('click', function(e) {
        var newPassword = $('#reset-new-password').val();
        var $confirm = $('#reset-confirm-password');
        $('.validation-error').remove();
        if(newPassword.trim() === '' || $.trim($confirm.val()) === '') {
			e.preventDefault();
			$confirm.after('<span class="validation-error">Please enter your password in both fields!</span>');
			return;
        }

        if(newPassword !== $confirm.val()) {
            e.preventDefault();
            $confirm.after('<span class="validation-error">Please ensure that both the new and the confirmation password are the same!</span>');
            return;
        }
    });
})();