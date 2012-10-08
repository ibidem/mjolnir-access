<?  
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */
	 
	namespace app;

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>Users</h1>

<? $users = $context->entries($page, $pagelimit, 0, ['role' => 'asc', 'nickname' => 'asc']) ?>

<? if ( ! empty($users)): ?>

	<?= $form = Form::instance()
		->method(\mjolnir\types\HTTP::POST)
		->action($control->action('delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table class="table table-striped marginless">
		<thead>
			<tr>
				<th class="micro-col">&nbsp;</th>
				<th>nickname</th>
				<th>role</th>
				<th>email</th>
				<th>ipaddress</th>
				<th>&nbsp;</th>
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
					<td><em><?= $user['roletitle'] ?></em></td>
					<td><?= $user['email'] ?></td>
					<td><?= $user['ipaddress'] ?></td>
					<td class="table-controls">
						
						<a href="<?= $control->backend('user-profile') ?>?id=<?= $user['id'] ?>"
						   class="btn btn-mini">
							View Profile
						</a>
						
						<a href="<?= $control->backend('user-edit') ?>?id=<?= $user['id'] ?>"
						   class="btn btn-mini btn-warning">
							Edit
						</a>
						
						<?= $delete_form = \app\Form::instance() 
							->standard('twitter.table-controls')
							->action($control->action('erase')) ?>

							<?= $delete_form->hidden('id')->value($user['id']) ?>
							<?= $delete_form->submit('Delete')->classes(['btn', 'btn-mini', 'btn-danger']) ?>

						<?= $delete_form->close() ?>
						
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<div class="row">
		
		<div class="span9">
			<br/>
			<div class="pull-right marginless-pagination">
				<?= $context->pager()
					->pagelimit($pagelimit)
					->currentpage($page)
					->standard('twitter')
					->render() ?>
			</div>
			<button class="btn btn-danger btn-mini" form="<?= $form->form_id() ?>"><i class="icon-trash"></i> Delete Selected</button>
		</div>

	</div>
	
<? else: # no users in system ?>
	<br/>
	<p class="alert alert-info">There are currently <strong>no users</strong> in the system.</p>
<? endif; ?>

<hr/>
	
<section role="application">
	
	<h2>New User</h2>
	<br>
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['user-new'])
		->auto_complete($_POST)
		->action($control->action('new')) ?>

		<fieldset>
			<?= $form->text('Nickname', 'nickname')->autocomplete(false) ?>
			<?= $form->text('Email', 'email')->autocomplete(false) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<?= $form->select('Role', 'role')->values($context->roles(), 'id', 'title') ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Create User</button>
			</div>
		</fieldset>

	<?= $form->close() ?>
	
</section>
