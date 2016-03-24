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
			$('#add-note-tags').append(data);
			toastr.info('Loading success');
		})
		.fail(function(error) {
			console.log(result);
		});
	}

	// Hide the form to create new note until clicked
	$('#new-note-section').hide();
	$('#new-tag-section').hide();
	
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
		
		toastr.success('Note has been added successfully!');
		console.log('Note added.');
	});
	
	$('#note-list').on('click', '.note-tags', function() {
		// Just realised this bit will be a bit harder than I realised as the clicked tag may have a sibling which is another tag which will then hide the clicked...
		// Have to check the siblings of each, then hide if there are no spans with the clicked tag data attribute.
		
		var tag = $(this).data('tag');
		var notes = $('.note');
		var hide = [];
		$('.note').show();
		
		$.each(notes, function(index, value) {
			$value = $(value);
			
			var tags = $value.children('.note-tags');
			var tagData = [];
			
			$.each(tags, function(i, childTag) {
				$this = $(childTag);
				var data = $this.data('tag');
				tagData.push(data);
			});
			
			if(tagData.indexOf(tag) === -1) {
				hide.push(value);
			}
		});
			
		$.each(hide, function(index, value) {
			$(value).hide();
			// Half there - currently hides both the white/blue tags
		});
		
		toastr.info('Now only showing notes with the tag "' + tag + '"');
	});
	
	$('#show-all-notes-button').on('click', function() {
		$('.note').show();
	});
	
	$('#show-new-tag-button').on('click', function() {
		$('#new-tag-section').show();
	});

})();