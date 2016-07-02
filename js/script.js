(function() {
	
	// load the available notes and tags.
	var showingComplete = false;
	var $noteList = $('#note-list');
	var $completedNoteButton = $('#complete-notes-button');
	var $newNoteSection = $('#new-note-section');
	var $tagChooser = $('#tag-chooser');
	var $noteTags = $('#add-note-tags');
	
	var initialLoad = { 
		userId : userId,
		complete: 0,
		action: 'loadnote'
	};
	loadNotes(initialLoad);
	
	/**
	* @function loadNotes
	*
	* Load the notes from the database
	* @param {object} toSend
	**/
	function loadNotes(toSend) {
		
		$.ajax({
			url: 'includes/load-note.php',
			method: 'POST',
			data: toSend,
			action: 'loadnote'
		})
		.done(function(data, result) {
			if(result === 'success') {
				
				if(toSend.action === 'searchnote') {
					$noteList.empty();
				}
				
				data = $.parseJSON(data);
 				if(data !== 'none') {
					$.each(data[0], function(index, value) {
						$noteTags.append('<div class="checkbox new-note-checkbox"><label><input type="checkbox" name="new-tag" data-tag="' + value + '" value="' + value + '">' + value + '</label></div>');
					});
					
					$tagChooser.empty();
					$tagChooser.append($('<option></option>').attr('value', 'showall').attr('selected', true).text('-- Show all --')); 
					$.each(data[1], function(key, value) {   
						$tagChooser
							.append($('<option></option>')
							.attr('value', value)
							.attr('data-tag', value)
							.text(value)); 
					});

					buildNote(data[2]);
					color = data[3];
				} else {
					$noteList.append('It appears that you have not yet created any notes. Create your first one.');
				}
				
				if(toSend.complete !== 1) {
					$completedNoteButton.html('<span class="glyphicon glyphicon-asterisk"></span>');		
				} else {
					$completedNoteButton.html('<span class="glyphicon glyphicon-asterisk"></span>');		
				} 
			}
		})
		.fail(function(error) {
			console.log('It failed: ', error);
		});
	}
	
	/**
	* @function searchNotes
	*
	* Searches the notes in the database
	* @param {string} search
	**/
	function searchNotes(search) {
		var data = {
			userId: userId,
			complete: 0,
			action: 'searchnote',
			search: search
		};
		
		loadNotes(data);
	}
	
	/**
	* @function addTag
	*
	* Add a new checkbox for tags to the DOM
	* @param {string} where
	* @param {string} input
	* @param {bool} edit
	**/
	function addTag(where, input, edit) {
		var newTag = $(input).val();
		
		if(newTag.trim() !== '') {
			if(edit) {
				$(where).append('<div class="checkbox"><label><input type="checkbox" checked name="edit-tag" data-tag="' + newTag + '" value="' + newTag + '">' + newTag + '</label></div>');
			} else {
				$(where).append('<div class="checkbox"><label><input type="checkbox" checked name="new-tag" data-tag="' + newTag + '" value="' + newTag + '">' + newTag + '</label></div>');
			}
			$(input).val('');
		}
	}
	
	/**
	* @function showTags
	*
	* Show all of the notes with the same tag as the one chosen
	* @param {string} tag
	**/
	function showTags(tag) {
		var notes = $('.note');
		var hide = [];
		$('.note').show();
		
		$.each(notes, function(index, value) {
			$value = $(value);
			
			var tags = $value.find('.note-tags');
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
	}

	/**
	* @function buildNote
	*
	* Build the note and then append it to the DOMs notelist
	* @param {object} data
	**/
	function buildNote(data) {
		var note = '';
		$.each(data, function(index, value) {
			note = '<div class="note" data-id="' + value.id + '">';
			note += '<h4 class="note-title">' + value.title + '</h4><p class="note-text">' + value.text + '</p>';
			note += '<div>';
			$.each(data[index][0], function(i, v) {
				note += '<span class="note-tags note-tags-' + value.color + '" title="Click to show all notes with this tag." data-tag="' + v + '">' + v + '</span>';
			});
			note += '</div>';

			if(value.complete === '0') {
				if(data[index][0].length > 0) {
					note += '<div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>';
				} else {
					note += '<div class="note-glyphicons note-glyphicons-empty"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>';
				}
				note += '<span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span><span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span></div>';
			} else {
				note +='<div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span>';
				note += '<span class="glyphicon glyphicon-asterisk mark-note-active" title="Mark as active"></span></div>';
			}

			$noteList.append(note);
		});
	}
	
	toastr.options = {
		"timeOut": "2000",
		"preventDuplicates": false,
		"closeButton": true
	};

	/**
	* @function
	*
	* Hide these sections until they are clicked to show
	**/
	$('.note-text-validation, .edit-note-text-validation, #seach-input').hide();
	$newNoteSection.hide();
	
	/**
	* @function
	*
	* Show the section to add a new note
	**/
	$('#new-note-button').on('click', function(){
		$newNoteSection.toggle();
	});

	/**
	* @function
	*
	* The function which is run to add a new note to the database.
	* Also then appends the note to the DOM - this could be changed to refresh the whole DOM via AJAX instead. 
	* Hides the new note section after success.
	**/
	$('#add-note-button').on('click', function() {
		
		var noteText = $('#add-note-text').val();
		var noteTitle = $('#add-note-title').val();
		var tagArray = [];
		
		if(noteText.trim() === '' && noteTitle.trim() === '') {
			$('.note-text-validation').show(); 
			return;
		}
		
		$('input:checkbox[name=new-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});
		
		$.ajax({
			url: 'includes/note-api.php',
			method: 'POST',
			data: { 
				noteText: noteText, 
				noteTags: tagArray, 
				noteTitle: noteTitle,
				userId: userId,
				action: 'addnote'
			}
		})
		.done(function(data, result) {
			var tags = '';
			$.each(tagArray, function(index, value) {
				tags += '<span class="note-tags note-tags-' + color + '" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$noteList.append('<div class="note" data-id="' + data + '"><span class="note-id" id="' + data + '">Note ID: ' + data + '</span><h4 class="note-title">' + noteTitle + '</h4><p class="note-text">' + noteText + '</p>' + tags + '<div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span><span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span><span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span></div></div>');
				
			// Reset and confirmation.
			$('#add-note-title').val('');
			$('#add-note-text').val('');
			$('input:checkbox[name=new-tag]').removeAttr('checked');
			// TODO : below...
			// $('div.new-note-checkbox').remove();
			tagsToAdd = [];
			toastr.success('Note has been added successfully!');
			$newNoteSection.hide();
		})
		.fail(function(error) {
			console.log('There was a failure: ', error);
		});
		
	});
	
	$('#show-new-tag-button').on('click', function() {
		addTag('#add-note-tags', '#add-new-tag-text', false);
	});
	
	$('#edit-new-tag-button').on('click', function() {
		addTag('#edit-note-tags', '#edit-new-tag-text', true);
	});
	
	/**
	* @function
	*
	* Allow the user to click on the notes tags to show notes with the same tag.
	* This will run the showTags() function.
	**/
	$noteList.on('click', '.note-tags', function() {
		
		var tag = $(this).data('tag');
		showTags(tag);
	});
	
	/**
	* @function
	*
	* Function to run when the user clicks to delete the note.
	* This will both remove the note from the database and the DOM.
	**/
	$noteList.on('click', '.remove-note', function() {
		
		if(!confirm('Are you sure you wish to remove this note?')) {
			return;
		}
		
		$this = $(this);
		var deleteId = $this.closest('.note').data('id');
		
 		$.ajax({
			method: 'POST',
			url: 'includes/note-api.php',
			data: {
				deleteNote: deleteId,
				action: 'deletenote'
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
	
	/**
	* @function
	*
	* Function to run when the user clicks to edit the note.
	* This will show a modal which contains the note information for editing.
	* This will both edit the note in the database and the DOM.
	**/
	$noteList.on('click', '.edit-note', function() {
		$this = $(this);
		var noteId = $this.closest('.note').data('id');
		$('#save-note-button').data('id', noteId);
		var parent = $this.closest('.note');
		
		var $parent = $(parent);
		var title = $parent.find('.note-title')[0].textContent;
		var text = $parent.find('.note-text')[0].textContent;
		var tags = $parent.find('.note-tags');
		
		var tagArray = [];
		
		$.each(tags, function(index, value) {
			tagArray.push(value.textContent);
		});
		
		$('#note-edit-modal').modal('show');		
		$('#edit-note-tags').empty();
		$('#edit-note-title').val(title);
		$('#edit-note-text').val(text);		
		$.each(tagArray, function(index, value) {
			$('#edit-note-tags').append('<div class="checkbox"><label><input type="checkbox" checked name="edit-tag" data-tag="' + value + '" value="' + value + '">' + value + '</label></div>');
		});
		
	});
	
	/**
	* @function
	*
	* Mark the note as complete in the database.
	* This note will then not appear on the active notes screen.
	**/
	$noteList.on('click', '.note-done', function() {
		
		$this = $(this);
		var noteId = $this.closest('.note').data('id');
		
		$.ajax({
			method: 'POST',
			url: 'includes/note-done.php',
			data: {
				noteId: noteId,
				complete: 1
			}
		})
		.done(function(data, result) {
			$this.closest('.note').remove();
			toastr.success('Note marked as complete!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		});
		
	});
	
	/** 
	* @function
	*
	* Toggle to show the active or completed notes.
	**/
	$completedNoteButton.on('click', function() {
		var data = {
			userId : userId,
			complete: 0,
			action: 'loadnote'
		};
		
		if(showingComplete) {
			$noteList.empty();
			showingComplete = false;
			data.complete = 0;
			loadNotes(data);
		} else {
			$noteList.empty();
			showingComplete = true;
			data.complete = 1;
			loadNotes(data);
		}
	});
	
	/**
	* @function
	*
	* Remove a note from the completed notes section, and mark it as active.
	**/
	$noteList.on('click', '.mark-note-active', function() {
		
 		$this = $(this);
		var noteId = $this.closest('.note').data('id');
		$.ajax({
			method: 'POST',
			url: 'includes/note-done.php',
			data: {
				noteId: noteId,
				complete: 0
			}
		})
		.done(function(data, result) {
			$this.closest('.note').remove();
			toastr.success('Note marked as active!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		}); 
		
	});
	
	/**
	* @function
	* 
	* Save the note after editing via the modal.
	**/
	$('#save-note-button').on('click', function() {
		
 		var title = $('#edit-note-title').val();
		var text = $('#edit-note-text').val();
		var tagArray = [];
		var noteId = $('#save-note-button').data('id');
		
		$('input:checkbox[name=edit-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});
		
 		$.ajax({
			method: 'POST',
			url: 'includes/note-api.php',
			data: {
				noteText: text,
				noteId: noteId,
				noteTitle: title,
				noteTags: tagArray,
				action: 'editnote'
			}
		})
		.done(function(data, result) {
			// update the DOM here.
			var note = $('[data-id="' + noteId + '"]');
			$(note).children('.note-title')[0].textContent = title;
			$(note).children('.note-text')[0].textContent = text;
			var newText = $(note).children('.note-text')[0];
			$(note).children('.note-tags').remove();
			var tags = '';
			
			$.each(tagArray, function(index, value) {
				tags += '<span class="note-tags note-tags-' + color + '" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$(newText).after(tags);
			
			$('#note-edit-modal').modal('hide');	
			
			toastr.success('Note successfully updated!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		});
	});
	
	/**
	* @function
	* 
	* Fired when an option in the dropdown is chosen.
	* Will show all notes with the chosen tag, or all if they have chosen to show all.
	**/
	$('#tag-chooser').on('change', function() {
		var value = this.value;
		if(value !== 'showall') {
			showTags(this.value);
		} else {
			$('.note').show();
			toastr.info('Showing all notes');
		}
	});
	
	/**
	* @function
	* 
	* Fired when the search button is clicked. 
	* Passes the value to the searchNotes function.
	**/
	$('#search-note-button').on('click', function() {
		var text = $('#search-note-text').val();
		searchNotes(text);
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the search note input
	* @param {event} event
	**/
	$('#search-note-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			var text = $('#search-note-text').val();
			searchNotes(text);
		}
	});
		
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the search note input box
	* @param {event} event
	**/
	$('#show-search-button').on('click', function() {
		$('#seach-input').toggle();
		$tagChooser.toggle();
		$('label[for=tag-chooser').toggle();
	});
	
	/**
	* @function
	* 
	* Delegate to show the edit modal.
	**/
	$noteList.on('click', '#note-edit-modal', function() {
		$('#note-edit-modal').show();
	});
	
	/**
	* @function
	* 
	* Hides the section to add a new note when the close button is clicked.
	**/
	$('#close-new-note').on('click', function() {
		$newNoteSection.hide();
	});
	
	/**
	* @function
	* 
	* Hides the validate notice displayed on the note text input when a character is entered
	**/
	$('#add-note-text').on('keyup', function() {
		$('.note-text-validation').hide();
	});
	
	/**
	* @function
	* 
	* Hides the validate notice displayed on the edit note text input when a character is entered
	**/
	$('#edit-note-text').on('keyup', function() {
		$('.edit-note-text-validation').hide();
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the add new tag input box
	* @param {event} event
	**/
	$('#add-new-tag-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			addTag('#add-note-tags', '#add-new-tag-text', false);
		}
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the edit note tag input box
	* @param {event} event
	**/
	$('#edit-new-tag-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			addTag('#edit-note-tags', '#edit-new-tag-text', true);
		}
	});
	
	/**
	* @function
	* 
	* Refresh the note list - empties the DOM notelist
	* Then fires the loadNotes() function to re-populate the DOM
	**/
	$('#refresh-button').on('click', function() {
		$noteList.empty();
		$('.checkbox').remove();
		loadNotes(initialLoad);
	});

})();