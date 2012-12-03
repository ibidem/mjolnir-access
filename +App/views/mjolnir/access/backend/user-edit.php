<?  
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$id = $_REQUEST['id'];
	$user = \array_merge($context->entry($id), $_POST);
?>

<section role="application">
	
	<h1>Edit User #<?= $id ?></h1>

	<br/>
	
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['user-update'])
		->action($control->action('update')) ?>
	
		<fieldset>
			<?= $form->hidden('id')->value($id) ?>
			<?= $form->text('Nickname', 'nickname')->value($user['nickname']) ?>
			<?= $form->text('Email', 'email')->value($user['email']) ?>
			<?= $form->select('Role', 'role')->values($context->roles(), 'id', 'title')->value($user['role']) ?>
			<?= $form->checkbox('Active', 'active')->check_value($user['active'])->autocomplete(false) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<div class="form-actions">
				<button class="btn btn-primary" 
						tabindex="<?= Form::tabindex() ?>">
					Update
				</button>
				<a class="btn btn-small" 
				   href="<?= \app\URL::route('\mjolnir\backend')->url(['slug' => 'user-index']) ?>">
					Back to User Index
				</a>
			</div>
		</fieldset>

	<?= $form->close() ?>
	
</section>