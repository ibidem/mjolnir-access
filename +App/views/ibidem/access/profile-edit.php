<?  
	/* @var $context \app\Backend_Profile */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$id = $_REQUEST['id'];
	$entry = $context->entry($id);
	$entry['required'] = $entry['required'] ? 'true' : 'false';
	$field = \array_merge($entry, $_POST);
?>

<section role="application">
	
	<h1>Edit Field #<?= $id ?></h1>
	<br/>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['profile-update'])
		->action($control->action('update'))
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->classes(['form-horizontal']) ?>
	
		<fieldset>
			<?= $form->hidden('id')->value($id) ?>

			<?= $form->text('Field Name', 'title')->value($field['title'])->autocomplete(false) ?>
			<?= $form->text('Order Index', 'idx')->value($field['idx'])->autocomplete(false) ?>
			<?= $form->select('Type', 'required')->values([ 'Optional' => 'false', 'Required' => 'true'])->value($field['required']) ?>
			
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
				<a class="btn btn-small" 
				   href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'user-profile-index']) ?>">
					Cancel
				</a>
			</div>
		</fieldset>
	
	<?= $form->close() ?>
	
</section>