(function() {
	var baseUrl = '../'
	
	$('#get').on('click', function() {
		$.ajax({
			url: 'load.php',
			method: 'POST'
		})
		.done(function(data, result) {
			console.log(data, result);
			if(result === 'success') {
				console.log(data);
			}
		})
		.fail(function(error) {
			console.log('It failed', error);
		})
	})
	
})();