<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h2>User List</h2>

<? $users = $context->users($page, $pagelimit) ?>

<? if ( ! empty($users)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('users-delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>nickname</th>
				<th>email</th>
				<th>role</th>
				<th>ipaddress</th>
				<th>options</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($users as $user): ?>
				<tr>
					<td>
						<?= $form->checkbox(null, 'selected[]')
								->attribute('form', $form->form_id())
								->value($user['id']) ?>
					</td>
					<td><?= $user['nickname'] ?></td>
					<td><?= $user['email'] ?></td>
					<td><?= $user['role'] ?></td>
					<td><?= $user['ipaddress'] ?></td>
					<td>

						<?= $edit_form = \app\Form::instance() 
							->method(\ibidem\types\HTTP::POST)
							->action($control->backend('user-edit'))
							->field_template(':field') ?>

							<div>
								<?= $edit_form->hidden('id')->value($user['id']) ?>
								<?= $edit_form->submit('Edit') ?>
							</div>

						<?= $edit_form->close() ?>

						<?= $delete_form = \app\Form::instance() 
							->method(\ibidem\types\HTTP::POST)
							->action($control->action('user-delete'))
							->field_template(':field') ?>

							<div>
								<?= $delete_form->hidden('id')->value($user['id']) ?>
								<?= $delete_form->submit('Delete') ?>
							</div>

						<?= $delete_form->close() ?>

					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<div>
		<button form="<?= $form->form_id() ?>">Delete Selected</button>
	</div>

	<hr/>

	<?= $context->users_pager()
		->url_base('?')
		->pagelimit($pagelimit)
		->currentpage($page)
		->render() ?>

<? else: # no users in system ?>
	<p class="empty"><em>There are currently no users.</em></p>
<? endif; ?>

<hr/>

<section role="application">
	<h3>Create User</h3>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->field_template('<dt>:name</dt><dd>:field</dd>')
		->errors($errors['\ibidem\access\backend\user-new'])
		->action($control->action('user-new')) ?>

		<dl>
			<?= $form->text('Nickname', 'nickname')->autocomplete(false) ?>
			<?= $form->text('Email', 'email')->autocomplete(false) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title') ?>
		</dl>
	
		<div>
			<hr/>
			<button tabindex="<?= Form::tabindex() ?>">Create</button>
		</div>

	<?= $form->close() ?>
</section>
