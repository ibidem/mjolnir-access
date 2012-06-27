<? namespace app; 
	/* @var $context \app\Backend_Access */
?>

<? $id = $_POST['id'] ?>
<? $role = \array_merge($context->entry($id), $_POST) ?>

<section role="application">
	
	<h1>Edit Role #<?= $id ?></h1>
	<br/>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['role-update'])
		->action($control->action('update'))
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->classes(['form-horizontal']) ?>
	
		<fieldset>
			<?= $form->hidden('id')->value($id) ?>
			<?= $form->text('Title', 'title')->value($role['title']) ?>
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