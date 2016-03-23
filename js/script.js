(function() {
	
	// load the available notes and tags.
	loadNotes();
	loadTags();
	
	function loadNotes() {
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
	}
	
	function loadTags() {
		$.ajax({
			url: 'load-tags.php',
			method: 'POST'
		})
		.done(function(data, result) {
			console.log(data);
			$('#add-note-tags').append(data);
		})
		.fail(function(error) {
			console.log(result);
		});
	}

	// Hide the form to create new note until clicked
	$('#new-note-section').hide();
	
	$('#new-note-button').on('click', function(){
		$('#new-note-section').show();
	});
	
	$('#add-note-button').on('click', function() {
		// add the note
		var noteText = $('#add-note-text').val();
		var noteTitle = $('#add-note-title').val();
		var tagArray = [];
		
		$('input:checkbox[name=new-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});
		
		$.ajax({
			url: 'add-new-note.php',
			method: 'POST',
			data: { 
				noteText: noteText, 
				noteTags: tagArray, 
				noteTitle: noteTitle 
			}
		})
		.done(function(data, result) {
			console.log(data);
		})
		.fail(function(error) {
			console.log('There was a failure: ', error);
		});
		
		// console.log(noteTags, noteText, noteTitle);
		
		console.log('Eventually will actually add the note.');
	});

})();