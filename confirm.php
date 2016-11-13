<?php require_once 'includes/register-script.php'; ?>

<?php require_once 'templates/unregistered-header.html'; ?>
	
	<div class="col-md-12 row">
		<h3>
			Confirm your email.
		</h3>
		<?php include 'includes/email-confirmation/ConfirmEmail.php'; ?>
	</div>
	
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<?php include 'templates/footer.html'; ?>