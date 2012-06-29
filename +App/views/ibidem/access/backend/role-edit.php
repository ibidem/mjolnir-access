<?  
	/* @var $context \app\Backend_Role */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$id = $_REQUEST['id'];
	$role = \array_merge($context->entry($id), $_POST);
?>

<section role="application">
	
	<h1>Edit Role #<?= $id ?></h1>
	<br/>
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['role-update'])
		->action($control->action('update')) ?>
	
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