(function() {
	
	getSettings();
	
	function getSettings() {		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				id: userId,
				action: 'get-tag-color'
			}
		})		
		.done(function(data, status) {
			data = JSON.parse(data);
			$('#user-tag-color').val(data);
		})
		.fail(function(error) {
			// fail
			console.log('An error occurred', error);
		});
	}
	
	$('#tag-color-button').on('click', function() {
		var color = $('#select-tag-color').val();
		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				color: color,
				id: userId,
				action: 'set-tag-color'
			}
		})
		.done(function(data, status) {
			// getSettings();
			toastr.success('Tag color updated!');
		})
		.fail(function(error) {
			// fail
			console.log('An error occurred', error);
		});
	});
	
	$('#change-password-form').on('submit', function(e) {
		var newPassword = $('#new-password').val();
		var confirmPassword = $('#confirm-password').val();
		var old = $('#old-password').val();
		
		if(!confirm('Please confirm you wish to change your password.')) {
			e.preventDefault();
			return;
		}
		
		if(old === '') {
			$('#old-password').after('<span class="validation-error">Please enter your old password!</span>');
			e.preventDefault();
			return;
		}
		
		if(newPassword !== confirmPassword) {
			console.log('These passwords are different!');
			e.preventDefault();
			$('#confirm-password').after('<span class="validation-error">The confirmation password is different!</span>');
			return;
		}
	});
	
})();