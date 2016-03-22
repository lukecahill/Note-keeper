(function() {
	
	// load the notes right away
	$.ajax({
		url: 'load.php',
		method: 'POST'
	})
	.done(function(data, result) {
		if(result === 'success') {
			$('#note-list').append(data);
			
			// few more times just to add some more data
			$('#note-list').append(data);
			$('#note-list').append(data);
			$('#note-list').append(data);
			$('#note-list').append(data);
			$('#note-list').append(data);
		}
	})
	.fail(function(error) {
		console.log('It failed: ', error);
	});

})();