(function() {
	
	// load the available notes and tags.
	loadNotes(0);
	loadTags();
	var showingComplete = false;
	var $noteList = $('#note-list');
	
	function loadNotes(complete) {
		
		$.ajax({
			url: 'includes/load-note.php',
			method: 'POST',
			data: {
				userId: userId,
				complete: complete
			}
		})
		.done(function(data, result) {
			if(result === 'success') {
				$('#note-list').append(data);
				
				if(complete === 1) {
					// change the button text. Remove the show all notes.
					$('show-all-notes-button').hide();
					$('#complete-notes-button').html('<span class="glyphicon glyphicon-asterisk"></span>	Show Active Notes');
				} else {
					$('show-all-notes-button').show();
					$('#complete-notes-button').html('<span class="glyphicon glyphicon-asterisk"></span>	Show Completed Notes');				
				}
			}
		})
		.fail(function(error) {
			console.log('It failed: ', error);
		});
	}
	
	function loadTags() {
		
		$.ajax({
			url: 'includes/load-tags.php',
			method: 'POST'
		})
		.done(function(data, result) {
			if(result == 'success') {
				$('#add-note-tags').append(data);
				toastr.info('Loading success');
			}
		})
		.fail(function(error) {
			console.log(error);
		});
	}
	
	function addTag() {
		var newTag = $('#add-new-tag-text').val();
		
		if(newTag.trim() !== '') {
			$('#add-note-tags').append('<div class="checkbox"><label><input type="checkbox" checked name="new-tag" data-tag="' + newTag + '" value="' + newTag + '">' + newTag + '</label></div>');
			$('#add-new-tag-text').val('');
		}
	}
	
	toastr.options = {
		"timeOut": "2000",
		"preventDuplicates": false,
		"closeButton": true
	};

	// Hide the form to create new note until clicked
	$('#new-note-section').hide();
	$('.note-text-validation').hide();
	
	$('#new-note-button').on('click', function(){
		$('#new-note-section').toggle();
	});
	
	$('#show-new-tag-button').on('click', function() {
		$('#new-tag-section').toggle();
	});
	
	$('#show-all-notes-button').on('click', function() {
		$('.note').show();
	});
	
	$('#add-note-button').on('click', function() {
		
		var noteText = $('#add-note-text').val();
		var noteTitle = $('#add-note-title').val();
		var tagArray = [];
		
		if(noteText.trim() === '') {
			$('.note-text-validation').show(); 
			return;
		}
		
		$('input:checkbox[name=new-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});
		
		$.ajax({
			url: 'includes/add-new-note.php',
			method: 'POST',
			data: { 
				noteText: noteText, 
				noteTags: tagArray, 
				noteTitle: noteTitle 
			}
		})
		.done(function(data, result) {
			var tags = '';
			
			$.each(tagArray, function(index, value) {
				tags += '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$('#note-list').append('<div class="note" data-id="' + data + '"><span class="note-id" id="' + data + '">Note ID: ' + data + '</span><h4 class="note-title">' + noteTitle + '</h4><p class="note-text">' + noteText + '</p>' + tags + '<div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span><span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span></div></div>');
				
			// Reset and confirmation.
			$('#add-note-title').val('');
			$('#add-note-text').val('');
			$('input:checkbox[name=new-tag]').removeAttr('checked');
			tagsToAdd = [];
			toastr.success('Note has been added successfully!');
			$('#new-note-section').hide();
		})
		.fail(function(error) {
			console.log('There was a failure: ', error);
		});
		
	});
	
	$('#show-new-tag-button').on('click', addTag);
	
	$('#note-list').on('click', '.note-tags', function() {
		
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
		});
		
		toastr.info('Now only showing notes with the tag "' + tag + '"');
	});
	
	$('#note-list').on('click', '.remove-note', function() {
		
		if(!confirm('Are you sure you wish to remove this note?')) {
			return;
		}
		
		$this = $(this);
		var deleteId = $this.closest('.note').data('id');
		
 		$.ajax({
			method: 'POST',
			url: 'includes/delete-note.php',
			data: {
				deleteNote: deleteId
			}
		})
		.done(function(data, result) {
			$this.closest('.note').remove();
			toastr.success('Note has been deleted!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		}); 
		
	});
	
	$('#note-list').on('click', '.edit-note', function() {
		// edit the note in the database.
		// also edit the note which is in the DOM 
		// would probably be best to have this done in a modal.
		// TODO : above - skeleton code is below.		
		$this = $(this);
		var newText = '';
		var noteId = $this.closest('.note').data('id');
		
		$.ajax({
			method: 'POST',
			url: 'includes/',
			data: {
				noteText: newText,
				noteId: noteId
			}
		})
		.done(function(data, result) {
			console.log(data, result)
			// update the DOM here.
			
			toastr.success('Note successfully updated!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		});
		
	});
	
	$('#note-list').on('click', '.note-done', function() {
		// Mark the note as done and remove from the list. Or change color? Place in another area? Choose...
		// TODO : above	
		$this = $(this);
		var newText = '';
		var noteId = $this.closest('.note').data('id');
		
		$.ajax({
			method: 'POST',
			url: 'includes/note-done.php',
			data: {
				noteId: noteId
			}
		})
		.done(function(data, result) {
			console.log(data, result)
			// update the DOM here.
			$this.closest('.note').remove();
			toastr.success('Note marked as complete!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		});
		
	});
	
	$('#complete-notes-button').on('click', function() {
		if(showingComplete) {
			$noteList.empty();
			$('#complete-notes-button').html('<span class="glyphicon glyphicon-asterisk"></span>	Show Completed Notes');
			showingComplete = false;
			loadNotes(0);
		} else {
			$noteList.empty();
			$('#complete-notes-button').html('<span class="glyphicon glyphicon-asterisk"></span>	Show Active Notes');
			showingComplete = true;
			loadNotes(1);
		}
	});
	
	$('#add-note-text').on('keyup', function() {
		$('.note-text-validation').hide();
	});
	
	$('#add-new-tag-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			addTag();
		}
	});
	
	$('#refresh-button').on('click', function() {
		window.location.reload();
	});

})();