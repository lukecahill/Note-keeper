(function() {
	
	// load the available notes and tags.
	loadNotes();
	loadTags();
	
	function loadNotes() {
		$.ajax({
			url: 'load-note.php',
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
			if(result == 'success') {
				$('#add-note-tags').append(data);
				toastr.info('Loading success');
			}
		})
		.fail(function(error) {
			console.log(result);
		});
	}
	
	toastr.options = {
		"timeOut": "2000",
		"preventDuplicates": false,
		"closeButton": true
	};;

	// Hide the form to create new note until clicked
	$('#new-note-section').hide();
	$('#new-tag-section').hide();
	
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
			var tags = '';
			
			$.each(tagArray, function(index, value) {
				tags += '<span class="note-tags" title="Click to show all notes with this tag." data-tag="' + value + '">' + value + '</span>';
			});
			
			$('#note-list').append('<div class="note"><span class="note-id" id="' + data + '">Note ID: ' + data + '</span><p class="note-text">' + noteText + '</p>' + tags + '</div>');
		})
		.fail(function(error) {
			console.log('There was a failure: ', error);
		});
		
		toastr.success('Note has been added successfully!');
		console.log('Note added.');
	});
	
	$('#add-tag-button').on('click', function() {
		// TODO: implement this in the PHP file add-new-tag.php, and also actually create the database table - new table or column?
		var newTag = $('#add-tag-text').val();
		
		$.ajax({
			url: 'add-new-tag.php',
			method: 'POST',
			data: { 
				newTag: newTag
			}
		})
		.done(function(data, result) {
			console.log(data);
		})
		.fail(function(error) {
			console.log('There was a failure: ', error);
		});
		
		toastr.success('Tag has been added successfully!');
		console.log('Tag added.');
	});
	
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

})();