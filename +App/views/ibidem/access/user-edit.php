<? namespace app; ?>

<? $id = $_POST['id'] ?>
<? $user = \app\Model_DB_User::user($id) ?>

<section role="application">
	<h2>Edit User #<?= $id ?></h2>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->action($control->action('update'))
		->field_template('<dt>:name</dt> <dd>:field</dd>') ?>

		<? $form->hidden('id')->value($user) ?>
	
		<dl>
			<?= $form->text('Nickname', 'nickname')->value($user['nickname']) ?>
			<?= $form->text('Email', 'email')->value($user['email']) ?>
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title')->value($user['role']) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
		</dl>
	
		<div>
			<hr/>
			<button tabindex="<?= Form::tabindex() ?>">Update</button>
		</div>

	<?= $form->close() ?>
</section>