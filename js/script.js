(function() {
	
	// load the available notes and tags.
	var showingComplete = false;
	var $noteList = $('#note-list'), $completedNoteButton = $('#complete-notes-button');
	var $newNoteSection = $('#new-note-section'), $tagChooser = $('#tag-chooser'), $noteTags = $('#add-note-tags');
	var color = '', dropdownTags = [];
	var $systemNotification = $('#system-notification-group');

	// configuration for toastr notificiations.
	if(typeof(toastr) != 'undefined') {
		toastr.options = {
			"timeOut": "2000",
			"preventDuplicates": false,
			"closeButton": true
		};
	}
	
	var initialLoad = { 
		userId : userId,
		complete: 0,
		action: 'loadnote',
		auth: auth
	};
	loadNotes(initialLoad);

	checkNotifications();

	/**
	* Check the local storage theme item. 
	* If the item is light then remove the dark-them class.
	* If the item is dark then add the dark-theme class onto the HTML.
	**/
	if(localStorageTest()) {
		if(localStorage.getItem('theme') === 'light') {
			$('html').removeClass('dark-theme');
			$('.container-fluid.account-dark').removeClass('account-dark');
		} else if(localStorage.getItem('theme') === 'dark') {
			$('html').addClass('dark-theme');
			$('.container-fluid.account-dark').addClass('account-dark');
		}
	}

	function setTagColor() {
		if(localStorageTest()) {
			var item = localStorage.getItem('color');

			if(typeof(item) !== 'undefined') {
				color = item;
			}
		}
	}

	function checkNotifications() {
		if(!localStorageTest()) {
			console.log('localStorage is not supported.');
			return;
		}
		var last = localStorage.getItem('notification');

		$.ajax({
			method: 'POST',
			url: 'includes/Notifications.php',
			data: {
				last: last,
				id: userId,
				action: 'check'
			}
		})
		.done(function(data, result) {
			data = JSON.parse(data);
			if(data === 'none_found') {
				return;
			}
			if(data > last) {
				getNotification(last);
				$systemNotification.show();
			}
		})
		.fail(function(error) {
			console.log(error);
		});
	}

	// could this be changed by getting the latest as the same time as checking?
	function getNotification(last) {
		$.ajax({
			method: 'POST',
			url: 'includes/Notifications.php',
			data: {
				last: last,
				id: userId,
				action: 'get'
			}
		})
		.done(function(data, result) {
			console.log(data, result);
			Notification.requestPermission().then(function(result) {
				showNotification(data, 'Note Keeper', '');
			});
		})
		.fail(function(error) {
			console.log(error);
		});
	}
	
	function showNotification(body, title, icon) {
		var options = {
			body: body,
			icon: icon
		};

		var notification = new Notification(title, options);
	}

	/**
	* @function handleError
	*
	* Handle anything which has gone wrong with a request.
	* @param {string} data
	**/
	function handleError(data) {
		console.log(data);

		switch (data) {
			case 'no_results':
				console.log('No results could be found');
				break;
			case '0': 
				console.log(data);
				break;
			default:
				alert('Something went wrong! Check the console for more');
				break;
		}
	}

	/**
	* @function loadNotes
	*
	* Load the notes from the database
	* @param {object} toSend
	**/
	function loadNotes(toSend) {
		
		$.ajax({
			url: 'includes/LoadNote.php',
			method: 'POST',
			data: toSend,
			action: 'loadnote'
		})
		.done(function(data, result) {
			loadNotesSuccess(data, result, toSend);
		})
		.fail(function(error) {
			handleError(error);
		});
	}
	
	/**
	* @function loadNotesSuccess
	*
	* Function which occurs when the loading of the from the database is successful.
	* @param {object} data
	* @param {string} result
	* @param {object} toSend
	**/
	function loadNotesSuccess(data, result, toSend) {
		if(result === 'success') {

			if(toSend.action === 'searchnote') {
				$noteList.innerHTML = '';
			}

			data = JSON.parse(data);
			if(data !== 'none' && data !== 'no_results') {
				$('#no-results').remove();
				if(toSend.complete === 0) {
					$noteTags.innerHTML = '';
					data[0].forEach(function(value, index) {
						$noteTags.append('<div class="checkbox"><label><input type="checkbox" name="new-tag" data-tag="{0}" value="{0}">{0}</label></div>'.format(value));
					});
				}
				
				$tagChooser.innerHTML = '';
				$tagChooser.append($('<option></option>').attr('value', 'note_keeper_showall').attr('selected', true).text('-- Show all --')); 
				data[1].forEach(function(value, key) {   
					$tagChooser
						.append($('<option></option>')
						.attr('value', value)
						.attr('data-tag', value)
						.text(value)); 
					dropdownTags.push(value);
				});

				buildNote(data[2]);
				color = data[3];
			} else if(data === 'no_results') {
				// no search results found
				$noteList.append('<p id="no-results">No note with that search could be found!</p>');
			} else {
				$noteList.append('<p id="first-note">It appears that you have not yet created any notes. Create your first one.</p>');
			}
		}
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
			action: 'searchnote',
			search: search,
			auth: auth
		};
		
		loadNotes(data);
	}
	
	/**
	* @function addTag
	*
	* Add a new checkbox for tags to the DOM
	* @param {string} where
	* @param {string} input
	* @param {boolean} edit
	**/
	function addTag(where, input, edit) {
		var newTag = $(input).val();
		
		if(newTag.trim() !== '') {
			if(edit === 1) {
				$(where).append('<div class="checkbox"><label><input type="checkbox" checked name="edit-tag" data-tag="{0}" value="{0}">{0}</label></div>'.format(newTag));
			} else if(edit === 0) {
				$(where).append('<div class="checkbox"><label><input type="checkbox" checked name="new-tag" data-tag="{0}" value="{0}">{0}</label></div>'.format(newTag));
			} else if(edit === 2) {
				$(where).append('<div class="checkbox new-note-checkbox"><label><input type="checkbox" checked name="new-tag" data-tag="{0}" value="{0}">{0}</label></div>'.format(newTag));
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
		tag = tag.toLowerCase();
		
		$.each(notes, function(index, value) {
			$value = $(value);
			
			var tags = $value.find('.note-tags');
			var tagData = [];
			
			$.each(tags, function(i, childTag) {
				$this = $(childTag);
				var data = $this.data('tag');
				tagData.push(data.toLowerCase());
			});
			
			if(tagData.indexOf(tag) === -1) {
				hide.push(value);
			}
		});
			
		hide.forEach(function(value, index) {
			$(value).hide();
		});
		
		toastr.info('Showing all notes with the tag "{0}"'.format(tag));
	}

	/**
	* @function buildNote
	*
	* Build the note and then append it to the DOMs notelist
	* @param {object} data
	**/
	function buildNote(data) {
		var note = '';

		data.forEach(function(value, index) {
			note = '<div class="note" data-id="{0}">'.format(value.id);
			note += '<h4 class="note-title">{0}</h4><p class="note-text">{1}</p>'.format(value.title, value.text);
			if(data[index][0].length > 0) {
				note += '<div class="tag-container"><span class="glyphicon glyphicon-tag glyph-tag-lower"></span>';
				data[index][0].forEach(function(v, i) {
					note += '<span class="note-tags note-tags-{0}" title="Click to show all notes with this tag." data-tag="{1}">{1}</span>'.format(value.color, v);
				});
				note += '</div>';
			}

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

	/**
	* @function
	*
	* Hide these sections until they are clicked to show
	**/
	$('.note-text-validation, .edit-note-text-validation, #search-input, #tag-chooser-input').hide();
	$newNoteSection.hide();
	
	/**
	* @function
	*
	* Show the section to add a new note
	**/
	document.getElementById('new-note-button').addEventListener('click', function(){
		$newNoteSection.toggle();
	});

	/**
	* @function
	*
	* The function which is run to add a new note to the database.
	* Also then appends the note to the DOM - this could be changed to refresh the whole DOM via AJAX instead. 
	* Hides the new note section after success.
	**/
	document.getElementById('add-note-button').addEventListener('click', function() {
		
		var noteText = document.getElementById('add-note-text').value;
		var noteTitle = document.getElementById('add-note-title').value;
		var tagArray = [];
		
		if(noteText.trim() === '' && noteTitle.trim() === '') {
			$('.note-text-validation').show(); 
			return;
		}
		
		$('input:checkbox[name=new-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});

		document.getElementById('add-note-button').disabled = true;
		
		$.ajax({
			url: 'includes/NoteApi.php',
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

			data = JSON.parse(data);
			if(data[0] === true) {
				var tags = '';
				$.each(tagArray, function(index, value) {
					tags += '<span class="note-tags note-tags-{0}" title="Click to show all notes with this tag." data-tag="{1}">{1}</span>'.format(color, value);
					if(dropdownTags.indexOf(value) === -1) {
						dropdownTags.push(value);					
						$tagChooser
						.append($('<option></option>')
						.attr('value', value)
						.attr('data-tag', value)
						.text(value));
					}
				});
				noteText = noteText.replace(/\n/g, '<br>');
				$noteList.append('<div class="note" data-id="{0}"><h4 class="note-title">{1}</h4><p class="note-text">{2}</p><div class="tag-container"><span class="glyphicon glyphicon-tag glyph-tag-lower"></span>{3}</div><div class="note-glyphicons"><span class="glyphicon glyphicon-remove remove-note" title="Delete this note"></span><span class="glyphicon glyphicon-edit edit-note" title="Edit this note"></span><span class="glyphicon glyphicon-ok note-done" title="Mark as done"></span></div></div>'.format(data[1], noteTitle, noteText, tags));
					
				// Reset and confirmation.
				document.getElementById('add-note-title').value = '';
				document.getElementById('add-note-text').value = '';
				$('input:checkbox[name=new-tag]').removeAttr('checked');
				$('.new-note-checkbox').remove();

				if(document.getElementById('first-note')) {
					var el = document.getElementById('first-note');
					el.parentNode.removeChild(el);
				}

				tagsToAdd = [];
				document.getElementById('add-note-button').disabled = false;
				toastr.success('Note has been added successfully!');
				$newNoteSection.hide();
			} else {
				handleError(data);				
			}
		})
		.fail(function(error) {
			handleError(error);
		});
		
	});
	
	document.getElementById('show-new-tag-button').addEventListener('click', function() {
		addTag('#add-note-tags', '#add-new-tag-text', 2);
	});
	
	document.getElementById('edit-new-tag-button').addEventListener('click', function() {
		addTag('#edit-note-tags', '#edit-new-tag-text', 1);
	});
	
	/**
	* @function
	*
	* Allow the user to click on the notes tags to show notes with the same tag.
	* This will run the showTags() function.
	**/
	$noteList.on('click', '.note-tags', function() {
		
		var tag = this.dataset.tag;
		showTags(tag);
	});
	
	/**
	* @function
	*
	* Function to run when the user clicks to delete the note.
	* This will both remove the note from the database and the DOM.
	**/
	document.getElementById('note-list').addEventListener('click', function(e) {
		if(e.target && e.target.matches('.remove-note')) {
			if(!confirm('Are you sure you wish to remove this note?')) {
				return;
			}
			
			$this = $(this);
			var deleteId = $this.closest('.note').data('id');
			
			$.ajax({
				method: 'POST',
				url: 'includes/NoteApi.php',
				data: {
					noteId: deleteId,
					action: 'deletenote'
				}
			})
			.done(function(data, result) {
				$this.closest('.note').fadeOut(500, function() {
					$this.closest('.note').remove();
				});
				toastr.success('Note has been deleted!');
			})
			.fail(function(error) {
				handleError(error);
			}); 
		}
	});
	
	/**
	* @function
	*
	* Function to run when the user clicks to edit the note.
	* This will show a modal which contains the note information for editing.
	* This will both edit the note in the database and the DOM.
	* TODO : Changing to use addEventListener causes sending ID to break
	**/
	$noteList.on('click', '.edit-note', function() {
		$this = $(this);
		var noteId = $this.closest('.note').data('id');
		document.getElementById('save-note-button').setAttribute('data-id', noteId);
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
		$('#edit-note-tags').innerHTML = '';
		
		document.getElementById('edit-note-title').value = title;
		document.getElementById('edit-note-text').value = text;
		tagArray.forEach(function(value, index) {
			$('#edit-note-tags').append('<div class="checkbox"><label><input type="checkbox" checked name="edit-tag" data-tag="{0}" value="{0}">{0}</label></div>'.format(value));
		});
		
	});
	
	/**
	* @function
	*
	* Mark the note as complete in the database.
	* This note will then not appear on the active notes screen.
	* TODO : Changing to use addEventListener causes sending ID to break
	**/
	$noteList.on('click', '.note-done', function() {
		
		$this = $(this);
		var noteId = $this.closest('.note').data('id');
		
		$.ajax({
			method: 'POST',
			url: 'includes/NoteApi.php',
			data: {
				noteId: noteId,
				complete: 1,
				action: 'setcomplete'
			}
		})
		.done(function(data, result) {
			if(data == 1) {
				$this.closest('.note').fadeOut(500, function() {
					$this.closest('.note').remove();
				});
				toastr.success('Note marked as complete!');	
			} else {
				handleError(data);
			}
		})
		.fail(function(error) {
			handleError(error);
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
			action: 'loadnote',
			auth: auth
		};
		
		if(showingComplete) {
			$noteList.innerHTML = '';
			showingComplete = false;
			data.complete = 0;
			loadNotes(data);
		} else {
			$noteList.innerHTML = '';
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
			url: 'includes/NoteApi.php',
			data: {
				noteId: noteId,
				complete: 0,
				action: 'setcomplete'
			}
		})
		.done(function(data, result) {
			if(data === '1') {
				$this.closest('.note').fadeOut(500, function() {
					$this.closest('.note').remove();
				});
				toastr.success('Note marked as active!');	
			} else {
				handleError(data);
				console.log(data);
			}
		})
		.fail(function(error) {
			handleError(error);
		}); 
		
	});
	
	/**
	* @function
	* 
	* Save the note after editing via the modal.
	**/
	document.getElementById('save-note-button').addEventListener('click', function() {
		
		var title = document.getElementById('edit-note-title').value;
		var text = document.getElementById('edit-note-text').value;
		var tagArray = [];
		var noteId = document.getElementById('save-note-button').getAttribute('data-id');
		
		$('input:checkbox[name=edit-tag]:checked').each(function() {
			tagArray.push($(this).val());
		});
		
 		$.ajax({
			method: 'POST',
			url: 'includes/NoteApi.php',
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
			if(data === '1') {
				//text = text.replace(/\n/g, '<br>');
				var note = $('[data-id="' + noteId + '"]');
				$(note).children('.note-title')[0].textContent = title;
				$(note).children('.note-text')[0].textContent = text;
				var newText = $(note).children('.note-text')[0];
				$(note).find('.note-tags').remove();
				var tags = '';
				
				tagArray.forEach(function(value, index) {
					tags += '<span class="note-tags note-tags-{0}" title="Click to show all notes with this tag." data-tag="{1}">{1}</span>'.format(color, value);
				});
				
				$(newText).after(tags);
				
				$('#note-edit-modal').modal('hide');	
				
				toastr.success('Note successfully updated!');				
			} else {
				handleError(data);
			}

		})
		.fail(function(error) {
			handleError(error);
		});
	});
	
	/**
	* @function
	* 
	* Fired when an option in the dropdown is chosen.
	* Will show all notes with the chosen tag, or all if they have chosen to show all.
	**/
	document.getElementById('tag-chooser').addEventListener('change', function() {
		var value = this.value;
		if(value !== 'note_keeper_showall') {
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
		var text = document.getElementById('search-note-text').value;
		searchNotes(text);
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the search note input
	* @param {event} event
	**/
	$('#search-note-text').on('keyup', function(event) {
		if(event.keyCode === 13) {
			var text = document.getElementById('search-note-text').value;
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
		document.getElementById('tag-chooser-input').style.display = 'none';
		$('#search-input').toggle();
		//$('#tag-chooser-input').hide();
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the search note input box
	* @param {event} event
	**/
	$('#show-tag-chooser-button').on('click', function() {
		document.getElementById('search-input').style.display = 'none';
		//$('#search-input').hide();
		$('#tag-chooser-input').toggle();
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
	* Hides the sytem notification when the close button is clicked.
	**/
	$('#notification-close').on('click', function() {
		//document.getElementById('system-notification-group').style.display = 'none';
		$('#system-notification-group').hide();
	});
	
	/**
	* @function
	* 
	* Hides the validate notice displayed on the note text input when a character is entered
	**/
	$('#add-note-text').on('keyup', function() {
		//document.getElementsByClassName('note-text-validation').style.display = 'none';
		$('.note-text-validation').hide();
	});
	
	/**
	* @function
	* 
	* Hides the validate notice displayed on the edit note text input when a character is entered
	**/
	$('#edit-note-text').on('keyup', function() {
		//document.getElementsByClassName('edit-note-text-validation').style.display = 'none';
		$('.edit-note-text-validation').hide();
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the add new tag input box
	* @param {event} event
	**/
	$('#add-new-tag-text').on('keyup', function(event) {
		if(event.keyCode === 13) {
			addTag('#add-note-tags', '#add-new-tag-text', 2);
		}
	});
	
	/**
	* @function
	* 
	* Fire the searchNotes function when the return key is pressed in the edit note tag input box
	* @param {event} event
	**/
	$('#edit-new-tag-text').on('keyup', function(event) {
		if(event.keyCode === 13) {
			addTag('#edit-note-tags', '#edit-new-tag-text', 1);
		}
	});
	
	/**
	* @function
	* 
	* Refresh the note list - empties the DOM notelist
	* Then fires the loadNotes() function to re-populate the DOM
	**/
	$('#refresh-button').on('click', function() {
		document.getElementById('note-list').innerHTML = '';

		var checkboxes = document.getElementsByClassName('checkbox');
		var l = checkboxes.length;
		for(var i = 0; i < l; i++) {
			var parent = checkboxes[0].parentNode;
			parent.removeChild(checkboxes[0]);
		}

		loadNotes(initialLoad);
	});

})();