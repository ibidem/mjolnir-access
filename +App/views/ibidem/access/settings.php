<? 
	/* @var $context \app\Backend_Registration */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$boolean_fields = array
		(
			'\ibidem\access\signup\public', 
			'\ibidem\access\signup\capcha',
		);
	
	$fields = \app\Register::pull($boolean_fields);
	
	foreach ($boolean_fields as $key)
	{
		$fields[$key] = $fields[$key] == 'on' ? true : false;
	}
?>

<section role="application">
	
	<h1>Registration</h1>
	
	<br/>
	
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['registration-update'])
		->action($control->action('update')) ?>
	
		<fieldset>
			
			<?= $form->checkbox('Public Registration', '\ibidem\access\signup\public')->value($fields['\ibidem\access\signup\public']) ?> 
			<?= $form->checkbox('Capcha', '\ibidem\access\signup\capcha')->value($fields['\ibidem\access\signup\capcha']) ?> 
			<?= $form->checkbox('Facebook Singin', '\ibidem\access\signin\facebook')->value($fields['\ibidem\access\signin\facebook']) ?>
			
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
				<a class="btn btn-small" 
				   href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'role-index']) ?>">
					Cancel
				</a>
			</div>
			
		</fieldset>
	
	<?= $form->close() ?>
	
</section>