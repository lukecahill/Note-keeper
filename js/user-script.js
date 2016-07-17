(function() {
	
	getSettings();
	/**
	* @function
	*
	* Will get the users chosen tag color from the database.
	**/
	function getSettings() {		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				id: userId,
				action: 'get-settings'
			}
		})
		.done(function(data, result) {
			data = $.parseJSON(data)
			var color = data[0];
			var order = data[1];
			
			var $selectColor = $('#select-tag-color');
			var $item = $selectColor.find('option[value=' + color + ']');
			$selectColor.find('option[value=' + color + ']').remove();
			$selectColor.find('option:eq(0)').before($item);
			$('#select-tag-color > option:eq(0)').attr('selected', true);
			
			$noteOrder = $('#options-note-order');
			$item = $noteOrder.find('option[value=' + order + ']');
			$noteOrder.find('option[value=' + color + ']').remove();
			$noteOrder.find('option:eq(0)').before($item);
			$('#options-note-order > option:eq(0)').attr('selected', true);
			
			if(data[2] === "true") {
				$('#search_title').prop('checked', true);
			}
			
			if(data[3] === "true") {
				$('#search_text').prop('checked', true);
			}
		})
		.fail(function(error) {
			console.log('An error occurred', error);
		});
	}
	
	/**
	*@function 
	*
	* Will update the users chosen color in the database
	**/
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
			toastr.success('Tag color updated!');
		})
		.fail(function(error) {
			console.log('An error occurred', error);
		});
	});
	
	/**
	*@function 
	*
	* Will update the users chosen order to sidplay the notes in. 
	* Order is taken from a select list.
	**/
	$('#options-order-button').on('click', function() {
		var order = $('#options-note-order').val();
		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				id: userId,
				action: 'set-note-order',
				order: order
			}
		})
		.done(function(data, status) {
			toastr.success('Note order has been successfully updated!');
		})
		.fail(function(error) {
			console.log('An error occurred', error);
		});
	});
	
	/**
	*@function 
	*
	* Will update the users choice of what to search the database for
	* when the search function is used.
	**/
	$('#options-search-button').on('click', function() {
		var title = $('#search_title').is(':checked');
		var text = $('#search_text').is(':checked');
		
		if(title === false && text === false) {
			alert('You must chose to search by either one or both of title and text!');
			return;
		}
		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				id: userId,
				action: 'set-search-parameters',
				text: text,
				title: title
			}
		})
		.done(function(data, status) {
			toastr.success('Search settings updated!');
		})
		.fail(function(error) {
			console.log('An error occurred', error);
		});
	});
	
	/**
	* @function
	*
	* Change the users password.
	* Will validate that the user wishes to change their password 
	* the old password has something entered into it's field,
	* and that the new password being entered has been confirmed.
	* @param {object} e
	**/
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