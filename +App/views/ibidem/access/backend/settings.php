<? 
	/* @var $context \app\Backend_Registration */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$boolean_fields = array
		(
			'\ibidem\access\signup\public', 
			'\ibidem\access\signup\capcha',
			'\ibidem\access\signin\facebook',
			'\ibidem\access\signin\twitter',
			'\ibidem\access\signin\google',
			'\ibidem\access\signin\yahoo',
		);
	
	$fields = \app\Register::pull($boolean_fields);
	
	$switch_format = ['Enabled' => 'on', 'Disabled' => 'off'];
?>

<section role="application">
	
	<h1>Access Settings</h1>
	
	<br/>
	
	<?= $form = Form::instance()->standard('twitter.general')
		->errors($errors['registration-update'])
		->action($control->action('update')) ?>
	
		<fieldset>
			<legend>Sign Up</legend>
			<?= $form->select('Public Registration', '\ibidem\access\signup\public', $switch_format)
				->value($fields['\ibidem\access\signup\public']) ?> 
			
			<?= $form->select('Capcha', '\ibidem\access\signup\capcha', $switch_format)
				->value($fields['\ibidem\access\signup\capcha']) ?> 
		</fieldset>
	
		<fieldset>
			<legend>Sign In</legend>
			<?= $form->select('Facebook', '\ibidem\access\signin\facebook', $switch_format)
				->value($fields['\ibidem\access\signin\facebook']) ?>
			
			<?= $form->select('Twitter', '\ibidem\access\signin\twitter', $switch_format)
				->value($fields['\ibidem\access\signin\twitter']) ?>
			
			<?= $form->select('Google', '\ibidem\access\signin\google', $switch_format)
				->value($fields['\ibidem\access\signin\google']) ?>
			
			<?= $form->select('Yahoo', '\ibidem\access\signin\yahoo', $switch_format)
				->value($fields['\ibidem\access\signin\yahoo']) ?>
		</fieldset>
	
		<div class="form-actions">
			<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
			<a class="btn btn-small" 
			   href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'role-index']) ?>">
				Cancel
			</a>
		</div>
	
	<?= $form->close() ?>
	
</section>