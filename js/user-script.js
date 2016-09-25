(function() {
	
	// want to hide these initially
	$('#password-change, #search-div, #order-div, #color-div, #recent-ips-div, #theme-div').hide();
	var passwordDown = false, colorDown = false, orderDown = false, searchDown = false, ipsDown = false, themeDown = false;
	
	// cache the DOM
	var $passwordDropdown = $('.password_span'), $orderDropdown = $('.order_dropdown_span'), $searchDropdown = $('.search_dropdown_span'), 
		$colorDropdown = $('.color_dropdown_span'), $recentIps = $('#recent-ips-div'), $ipsDropdown = $('.recent_ips_span'), $themeDropdown = $('.theme_dropdown_span');
	
	
	/**
	* @function String format
	*
	* Add functionality to String object, for C# style string formatting.
	* Usage: "{0} is dead, but {1} is alive! {0} {2}".format("ASP", "ASP.NET")
	* From; http://stackoverflow.com/a/4673436
	**/
	if (!String.prototype.format) {
		String.prototype.format = function() {
			var args = arguments;
			return this.replace(/{(\d+)}/g, function(match, number) { 
				return typeof args[number] != 'undefined' ? args[number] : match;
			});
		};
	}
	
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

			data = $.parseJSON(data);
			var color = data[0];
			var order = data[1];
			
			var $selectColor = $('#select-tag-color');
			var $item = $selectColor.find('option[value={0}]'.format(color));
			$item.remove();
			$selectColor.find('option:eq(0)').before($item);
			$('#select-tag-color > option:eq(0)').attr('selected', true);
			
			$noteOrder = $('#options-note-order');
			$item = $noteOrder.find('option[value={0}]'.format(order));
			$item.remove();
			$noteOrder.find('option:eq(0)').before($item);
			$('#options-note-order > option:eq(0)').attr('selected', true);
			
			if(data[2] === "true") {
				$('#search_title').prop('checked', true);
			}
			
			if(data[3] === "true") {
				$('#search_text').prop('checked', true);
			}
			
			if(data[4] === "true") {
				$('#search_mark_done').prop('checked', true);
			}
			
			$('#total_notes').append(data[5]);
			
			data[6].reverse();
			$recentIps.append('<b>Most Recent</b>');
			$.each(data[6], function(i, v) {
				$recentIps.append('<p>{0}</p>'.format(v));
			});

			if(data[7] === 'dark') {
				$('#theme_checkbox').prop('checked', true);
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
		var searchComplete = $('#search_mark_done').is(':checked');
		
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
				title: title,
				complete: searchComplete
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
		$('.validation-error').remove();
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
		
		if(newPassword.trim() === '' || confirmPassword.trim() === '') {
			$('#new-password').after('<span class="validation-error">The new or confirmation input is empty!</span>');
			e.preventDefault();
			return;
		}
		
		if(newPassword !== confirmPassword) {
			$('#confirm-password').after('<span class="validation-error">The confirmation password is different!</span>');
			e.preventDefault();
			return;
		}
	});

	/**
	* @function
	*
	* Changes the front-end theme. 
	**/
	$('#theme-button').on('click', function() {
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				action: 'set-theme',
				id: userId
			}
		})
		.done(function(data, status) {
			toastr.success('Updated theme settings! Please refresh your page.');
		})
		.fail(function(error) {
			console.log(error);
		});
	});

	/**
	* @function
	* Toggle to show the form to change the users password 
	* 
	**/
	$('#password-header').on('click', function() {
		$('#password-change').toggle();
		
		if(passwordDown) {
			$passwordDropdown.addClass('glyphicon-chevron-up');
			$passwordDropdown.removeClass('glyphicon-chevron-down');
			passwordDown = false;
		} else {
			$passwordDropdown.addClass('glyphicon-chevron-down');
			$passwordDropdown.removeClass('glyphicon-chevron-up');
			passwordDown = true;
		}
	});
	
	/**
	* @function
	* Toggle to show the form for the user to choose their notes color
	* 
	**/
	$('#color-header').on('click', function() {
		$('#color-div').toggle();
		
		if(colorDown) {
			$colorDropdown.addClass('glyphicon-chevron-up');
			$colorDropdown.removeClass('glyphicon-chevron-down');
			colorDown = false;
		} else {
			$colorDropdown.addClass('glyphicon-chevron-down');
			$colorDropdown.removeClass('glyphicon-chevron-up');
			colorDown = true;
		}
	});
		
	/**
	* @function
	* Toggle to show the form for the user to choose what order to display the notes
	* 
	**/
	$('#order-header').on('click', function() {
		$('#order-div').toggle();
		
		if(orderDown) {
			$orderDropdown.addClass('glyphicon-chevron-up');
			$orderDropdown.removeClass('glyphicon-chevron-down');
			orderDown = false;
		} else {
			$orderDropdown.addClass('glyphicon-chevron-down');
			$orderDropdown.removeClass('glyphicon-chevron-up');
			orderDown = true;
		}
	});
		
	/**
	* @function
	* Toggle to show the form for the user to customise the search option
	* 
	**/
	$('#search-header').on('click', function() {
		$('#search-div').toggle();
		
		if(searchDown) {
			$searchDropdown.addClass('glyphicon-chevron-up');
			$searchDropdown.removeClass('glyphicon-chevron-down');
			searchDown = false;
		} else {
			$searchDropdown.addClass('glyphicon-chevron-down');
			$searchDropdown.removeClass('glyphicon-chevron-up');
			searchDown = true;
		}
	});

	/**
	* @function
	* Toggle to show the list of recently used IPs
	* 
	**/
	$('#recent-ips-header').on('click', function() {
		$('#recent-ips-div').toggle();

		if(ipsDown) {
			$ipsDropdown.addClass('glyphicon-chevron-up');
			$ipsDropdown.removeClass('glyphicon-chevron-down');
			ipsDown = false;
		} else {
			$ipsDropdown.addClass('glyphicon-chevron-down');
			$ipsDropdown.removeClass('glyphicon-chevron-up');
			ipsDown = true;
		}
	});
		
	/**
	* @function
	* Toggle to show the form for the user to choose what theme to use - currently dark or light theme.
	* 
	**/
	$('#theme-header').on('click', function() {
		$('#theme-div').toggle();
		
		if(themeDown) {
			$themeDropdown.addClass('glyphicon-chevron-up');
			$themeDropdown.removeClass('glyphicon-chevron-down');
			themeDown = false;
		} else {
			$themeDropdown.addClass('glyphicon-chevron-down');
			$themeDropdown.removeClass('glyphicon-chevron-up');
			themeDown = true;
		}
	});
	
})();