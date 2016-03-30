(function() {
	
	$('#tag-color-button').on('click', function() {
		var color = $('#user-tag-color').val();
		
		if(color.indexOf('#') === 0) {
			color = color.substring(1, color.length);
		}
		
		$.ajax({
			method: 'POST',
			url: 'includes/user-settings.php',
			data: {
				color: color,
				id: userId
			}
		})
		.done(function(data, status) {
			console.log(data);
			$('#user-tag-color').val('');
			toastr.success('Tag color updated!');
		})
		.fail(function(error) {
			// fail
			console.log('An error occurred', error);
		})
	});
	
})();