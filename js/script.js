(function() {
	
	// load the available notes and tags.
	var showingComplete = false;
	var $noteList = $('#note-list');
	var $completedNoteButton = $('#complete-notes-button');
	var $showAllNotesButton = $('#show-all-notes-button');
	var $newNoteSection = $('#new-note-section');
	var $tagChooser = $('#tag-chooser');
	var $noteTags = $('#add-note-tags');
	
	var initialLoad = { 
			userId : userId,
			complete: 0,
			action: 'loadnote'
		};
	loadNotes(initialLoad);
	
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
						$noteTags.append('<div class="checkbox"><label><input type="checkbox" name="new-tag" data-tag="' + value + '" value="' + value + '">' + value + '</label></div>');
					});
					
					$tagChooser.empty();
					$tagChooser.append($('<option></option>').attr('disabled', true).attr('selected', true).text('-- Choose a tag --')); 
					$tagChooser.append($('<option></option>').attr('value', 'showall').text('-- Show all --')); 
					$.each(data[1], function(key, value) {   
						$tagChooser
							.append($('<option></option>')
							.attr('value', value)
							.attr('data-tag', value)
							.text(value)); 
					});
						
					$noteList.append(data[2]);
					$noteList.after(data[3]);
				} else {
					$noteList.append('It appears that you have not yet created any notes. Create your first one.');
				}
				
				if(toSend.complete !== 1) {
					$completedNoteButton.html('<span class="glyphicon glyphicon-asterisk"></span>	Show Completed Notes');		
				} else {
					$completedNoteButton.html('<span class="glyphicon glyphicon-asterisk"></span>	Show Active Notes');		
				} 
			}
		})
		.fail(function(error) {
			console.log('It failed: ', error);
		});
	}
	
	function searchNotes(search) {
		var data = {
			userId: userId,
			complete: 0,
			action: 'searchnote',
			search: search
		};
		
		loadNotes(data);
	}
	
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
	
	function showTags(tag) {
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
	}
	
	toastr.options = {
		"timeOut": "2000",
		"preventDuplicates": false,
		"closeButton": true
	};

	// Hide the form to create new note until clicked
	$newNoteSection.hide();
	$('.note-text-validation, .edit-note-text-validation, #seach-input').hide();
	
	$('#new-note-button').on('click', function(){
		$newNoteSection.toggle();
	});
	
	$showAllNotesButton.on('click', function() {
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
			url: 'includes/note.php',
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
				tags += '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$noteList.append('<div class="note" data-id="' + data + '"><span class="note-id" id="' + data + '">Note ID: ' + data + '</span><h4 class="note-title">' + noteTitle + '</h4><p class="note-text">' + noteText + '</p>' + tags + '<div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span><span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span><span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span></div></div>');
				
			// Reset and confirmation.
			$('#add-note-title').val('');
			$('#add-note-text').val('');
			$('input:checkbox[name=new-tag]').removeAttr('checked');
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
	
	$noteList.on('click', '.note-tags', function() {
		
		var tag = $(this).data('tag');
		showTags(tag);
	});
	
	$noteList.on('click', '.remove-note', function() {
		
		if(!confirm('Are you sure you wish to remove this note?')) {
			return;
		}
		
		$this = $(this);
		var deleteId = $this.closest('.note').data('id');
		
 		$.ajax({
			method: 'POST',
			url: 'includes/note.php',
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
			url: 'includes/note.php',
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
				tags += '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$(newText).after(tags);
			
			$('#note-edit-modal').modal('hide');	
			
			toastr.success('Note successfully updated!');
		})
		.fail(function(error) {
			console.log('An error has occurred: ', error);
		});
	});
	
	$('#tag-chooser').on('change', function() {
		var value = this.value;
		if(value !== 'showall') {
			showTags(this.value);
		} else {
			$('.note').show();
			toastr.info('Showing all notes');
		}
	});
	
	$('#search-note-button').on('click', function() {
		var text = $('#search-note-text').val();
		searchNotes(text);
	});
	
	$('#search-note-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			var text = $('#search-note-text').val();
			searchNotes(text);
		}
	});
	
	$('#show-search-button').on('click', function() {
		$('#seach-input').toggle();
		$tagChooser.toggle();
		$('label[for=tag-chooser').toggle();
	});
	
	$noteList.on('click', '#note-edit-modal', function() {
		$('#note-edit-modal').show();
	});
	
	$('#close-new-note').on('click', function() {
		$newNoteSection.hide();
	});
	
	$('#add-note-text').on('keyup', function() {
		$('.note-text-validation').hide();
	});
	
	$('#edit-note-text').on('keyup', function() {
		$('.edit-note-text-validation').hide();
	});
	
	$('#add-new-tag-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			addTag('#add-note-tags', '#add-new-tag-text', false);
		}
	});
	
	$('#edit-new-tag-text').on('keyup', function(event) {
		if(event.keyCode == 13) {
			addTag('#edit-note-tags', '#edit-new-tag-text', true);
		}
	});
	
	$('#refresh-button').on('click', function() {
		$noteList.empty();
		$('.checkbox').remove();
		loadNotes(initialLoad);
	});

})();