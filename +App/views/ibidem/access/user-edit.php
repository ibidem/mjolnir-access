<? namespace app; 
	/* @var $context \app\Backend_Access */
?>

<? $id = $_POST['id'] ?>
<? $user = \array_merge($context->user($id), $_POST) ?>

<section role="application">
	
	<h1>Edit User #<?= $id ?></h1>

	<br/>
	
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['\ibidem\access\backend\user-update'])
		->action($control->action('user-update'))
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->classes(['form-horizontal']) ?>
	
		<fieldset>
			<?= $form->hidden('id')->value($id) ?>
			<?= $form->text('Nickname', 'nickname')->value($user['nickname']) ?>
			<?= $form->text('Email', 'email')->value($user['email']) ?>
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title')->value($user['role']) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
				<a class="btn btn-small" href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'user-manager']) ?>">
					Cancel
				</a>
			</div>
		</fieldset>

	<?= $form->close() ?>
	
</section>