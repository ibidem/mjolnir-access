<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>User List</h1>

<? $users = $context->users($page, $pagelimit) ?>

<? if ( ! empty($users)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('users-delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table class="table table-striped">
		<? foreach (['thead', 'tfoot'] as $tag): ?>
			<<?= $tag ?>>
				<tr>
					<th>&nbsp;</th>
					<th>nickname</th>
					<th>role</th>
					<th>email</th>
					<th>ipaddress</th>
					<th>&nbsp;</th>
				</tr>
			</<?= $tag ?>>
		<? endforeach; ?>
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
					<td>
						<?= $edit_form = \app\Form::instance() 
							->method(\ibidem\types\HTTP::POST)
							->action($control->backend('user-edit'))
							->field_template(':field')
							->classes(['form-inline', 'pull-left']) ?>

							<?= $edit_form->hidden('id')->value($user['id']) ?>
							<?= $edit_form->submit('Edit')->classes(['btn', 'btn-mini', 'btn-warning']) ?>

						<?= $edit_form->close() ?>
						
						<?= $delete_form = \app\Form::instance() 
							->method(\ibidem\types\HTTP::POST)
							->action($control->action('user-delete'))
							->field_template(':field')
							->classes(['form-inline', 'pull-left']) ?>

							<?= $delete_form->hidden('id')->value($user['id']) ?>
							<?= $delete_form->submit('Delete')->classes(['btn', 'btn-mini', 'btn-danger']) ?>

						<?= $delete_form->close() ?>
					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<div class="row">
		
		<div class="span2">
			<button class="btn btn-danger btn-mini" form="<?= $form->form_id() ?>"><i class="icon-trash"></i> Delete Selected</button>
		</div>
		
		<div class="span8">
			<div class="pull-right">
				<?= $context->users_pager()
					->pagelimit($pagelimit)
					->currentpage($page)
					->standard('twitter')
					->render() ?>
			</div>
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
		->method(\ibidem\types\HTTP::POST)
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->errors($errors['\ibidem\access\backend\user-new'])
		->auto_complte($_POST)
		->action($control->action('user-new'))
		->classes(['form-horizontal']) ?>

		<fieldset>
			<?= $form->text('Nickname', 'nickname')->autocomplete(false) ?>
			<?= $form->text('Email', 'email')->autocomplete(false) ?>
			<?= $form->password('Password', 'password')->autocomplete(false) ?>
			<?= $form->password('Password (verify)', 'verifier')->autocomplete(false) ?>
			<?= $form->select('Role', 'role')->values($context->user_roles(), 'id', 'title') ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Create User</button>
			</div>
		</fieldset>

	<?= $form->close() ?>
	
</section>
