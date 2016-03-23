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
	
	// Hide the form to create new note until clicked
	$('#new-note-section').hide();
	
	$('#new-note-button').on('click', function(){
		$('#new-note-section').show();
	});
	
	$('#add-note-button').on('click', function() {
		// add the note
		var noteText = $('#add-note-text').val();
		var noteTags = $('#add-note-tags').val();
		var noteTitle = $('#add-note-title').val();
		
		console.log(noteTags, noteText, noteTitle);
		
		console.log('Eventually will actually add the note.');
	});

})();