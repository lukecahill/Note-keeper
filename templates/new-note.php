<div class="col-sm-12" id="new-note-section">
	<div class="form-group new-note-group">
		<h3>
			Add a new note.
		</h3>
		<div class="form-group">
			<label for="add-note-title">
				Note Title
			</label>
			<input type="text" id="add-note-title" class="form-control">
		</div>
		<div class="form-group">
			<label for="add-note-title">
				Note Text
			</label>
			<input type="text" id="add-note-text" required class="form-control">
			
			<p class="validation-error note-text-validation">
				Invalid - Note must contain text!
			</p>
		</div>
		<div class="form-group">
			<label for="add-note-title">
				Note Tags - Enter the tag and click 'Add New Tag' to add.
			</label>
			<div class="input-group">
				<input type="text" id="add-new-tag-text" class="form-control">
				<span class="input-group-btn">
					<button class="btn btn-success" id="show-new-tag-button">
					<span class="glyphicon glyphicon-tag"></span>
						Add New Tag
					</button>
				</span>
			</div>
			<div id="add-note-tags">
				<h5>
					Check box to add tag to the note.
				</h5>
			</div>
		</div>
		<button class="btn btn-success" id="add-note-button">
			<span class="glyphicon glyphicon-plus"></span>
			Add Note
		</button>
	</div>
</div>