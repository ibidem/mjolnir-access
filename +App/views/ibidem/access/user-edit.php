<?  
	/* @var $context \app\Backend_Role */
	/* @var $control \app\Controller_Backend */

	namespace app;
?>

<? $id = $_POST['id'] ?>
<? $user = \array_merge($context->entry($id), $_POST) ?>

<section role="application">
	
	<h1>Edit User #<?= $id ?></h1>

	<br/>
	
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['user-update'])
		->action($control->action('update'))
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->classes(['form-horizontal']) ?>
	
		<fieldset>
			<?= $form->hidden('id')->value($id) ?>
			<?= $form->text('Nickname', 'nickname')->value($user['nickname']) ?>
			<?= $form->text('Email', 'email')->value($user['email']) ?>
			<?= $form->select('Role', 'role')->values($context->roles(), 'id', 'title')->value($user['role']) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<div class="form-actions">
				<button class="btn btn-primary" 
						tabindex="<?= Form::tabindex() ?>">
					Update
				</button>
				<a class="btn btn-small" 
				   href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'user-index']) ?>">
					Cancel
				</a>
			</div>
		</fieldset>

	<?= $form->close() ?>
	
</section>