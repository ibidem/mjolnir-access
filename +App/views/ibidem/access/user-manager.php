<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h2>User List</h2>

<? $users = $context->users($page, $pagelimit) ?>

<? if ( ! empty($users)): ?>
	<table>
		<thead>
			<tr>
				<th>nickname</th>
				<th>email</th>
				<th>role</th>
				<th>ipaddress</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($users as $user): ?>
				<tr>
					<td><?= $user['nickname'] ?></td>
					<td><?= $user['email'] ?></td>
					<td><?= $user['role'] ?></td>
					<td><?= $user['ipaddress'] ?></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<?= $context->users_pager()
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
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title')->autocomplete(false) ?>
		</dl>
	
		<div>
			<hr/>
			<button tabindex="<?= Form::tabindex() ?>">Create</button>
		</div>

	<?= $form->close() ?>
</section>
