<? namespace app; 
	/* @var $context \app\Backend_Access */
?>

<? $id = $_POST['id'] ?>
<? $user = \array_merge($context->user($id), $_POST) ?>

<section role="application">
	<h2>Edit User #<?= $id ?></h2>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['\ibidem\access\backend\user-update'])
		->action($control->action('user-update'))
		->field_template('<dt>:name</dt> <dd>:field</dd>') ?>
	
		<dl>
			<?= $form->text('Nickname', 'nickname')->value($user['nickname']) ?>
			<?= $form->text('Email', 'email')->value($user['email']) ?>
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title')->value($user['role']) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
		</dl>
	
		<div>
			<hr/>
			<?= $form->hidden('id')->value($id) ?>
			<button tabindex="<?= Form::tabindex() ?>">Update</button>
		</div>

	<?= $form->close() ?>
</section>