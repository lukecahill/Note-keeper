(function() {
	var registerBtn = document.getElementById('register-btn');

	registerBtn.addEventListener('click', function(e) {
		var $confirmPassword = document.getElementById('confirm-user-password');
		var $password = document.getElementById('user-password');
		var $email = document.getElementById('user-email');

		var items = document.getElementsByClassName('validation-error');
		var l = items.length;

		for(var i = 0; i < l; i++) {
			var parent = items[0].parentNode;
			parent.removeChild(items[0]);
		}

		var confirmPassword = $confirmPassword.value;
		var password = $password.value;
		
		if($email.value.trim() === '') {
			e.preventDefault();
			$email.insertAdjacentHTML('afterend', '<span class="validation-error">Please enter an email address!</span>');
			return;
		}
		
		if(password.trim() === '') {
			e.preventDefault();
			$password.insertAdjacentHTML('afterend', '<span class="validation-error">Please enter a password!</span>');

			return;
		}
		
		if(confirmPassword.trim() === '') {
			e.preventDefault();
			$confirmPassword.insertAdjacentHTML('afterend', '<span class="validation-error">Please confirm your password!</span>');
			return;
		}
		
		if(confirmPassword.trim() !== password) {
			e.preventDefault();			
			$confirmPassword.insertAdjacentHTML('afterend', '<span class="validation-error">The confirmation password is different! </span>');
			return;
		}
	});
})();