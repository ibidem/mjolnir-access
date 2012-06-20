<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h2>User Roles</h2>

<? $roles = $context->roles($page, $pagelimit) ?>

<? if ( ! empty($roles)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('roles-delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>role</th>
					<th>options</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($roles as $role): ?>
					<tr>
						<td>
							<?= $form->checkbox(null, 'selected[]')
								->attribute('form', $form->form_id())
								->value($role['id']) ?>
						</td>
						<td><?= $role['title'] ?></td>
						<td>					
							<?= $edit_form = \app\Form::instance() 
								->method(\ibidem\types\HTTP::POST)
								->action($control->action('role-edit'))
								->field_template(':field') ?>
							
								<div>
									<?= $edit_form->hidden('id')->value($role['id']) ?>
									<?= $edit_form->submit('Edit') ?>
								</div>
							
							<?= $edit_form->close() ?>
							
							<?= $delete_form = \app\Form::instance() 
								->method(\ibidem\types\HTTP::POST)
								->action($control->action('role-delete'))
								->field_template(':field') ?>
							
								<div>
									<?= $delete_form->hidden('id')->value($role['id']) ?>
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

	<?= $form->close() ?>
	
	<hr/>
	
	<?= $context->roles_pager()
		->pagelimit($pagelimit)
		->currentpage($page)
		->render() ?>

<? else: # no users in system ?>
	<p class="empty"><em>There are currently no roles defined.</em></p>
<? endif; ?>

<hr/>

<section role="application">
	<h3>Create Role</h3>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->field_template('<dt>:name</dt><dd>:field</dd>')
		->errors($errors['\ibidem\access\backend\role-new'])
		->action($control->action('role-new')) ?>

		<dl>
			<?= $form->text('Title', 'title')->autocomplete(false) ?>
		</dl>
	
		<div>
			<hr/>
			<button tabindex="<?= Form::tabindex() ?>">Create</button>
		</div>

	<?= $form->close() ?>
</section>
