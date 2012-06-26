<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>User Roles</h1>

<? $roles = $context->roles($page, $pagelimit) ?>

<? if ( ! empty($roles)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('roles-delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table class="table table-striped marginless">
		<? foreach (['thead'] as $tag): ?>
			<<?= $tag ?>>
				<tr>
					<th>&nbsp;</th>
					<th>role</th>
					<th>&nbsp;</th>
				</tr>
			</<?= $tag ?>>
		<? endforeach; ?>
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
							->action($control->backend('role-edit'))
							->field_template(':field')
							->classes(['form-inline', 'pull-left']) ?>

							<fieldset>
								<?= $edit_form->hidden('id')->value($role['id']) ?>
								<?= $edit_form->submit('Edit')->classes(['btn', 'btn-mini', 'btn-warning']) ?>
							</fieldset>

						<?= $edit_form->close() ?>

						<?= $delete_form = \app\Form::instance() 
							->method(\ibidem\types\HTTP::POST)
							->action($control->action('role-delete'))
							->field_template(':field') 
							->classes(['form-inline', 'pull-left']) ?>

							<fieldset>
								<?= $delete_form->hidden('id')->value($role['id']) ?>
								<?= $delete_form->submit('Delete')->classes(['btn', 'btn-mini', 'btn-danger']) ?>
							</fieldset>

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
				<?= $context->roles_pager()
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
	<p class="alert alert-info">There are currently <strong>no roles</strong> defined.</p>
<? endif; ?>

<hr/>
	
<section role="application">
	<h2>New Role</h2>
	<br/>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->errors($errors['\ibidem\access\backend\role-new'])
		->action($control->action('role-new'))
		->classes(['form-horizontal']) ?>

		<fieldset>
			<?= $form->text('Title', 'title')->autocomplete(false) ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Create Role</button>
			</div>
		</fieldset>
	
		

	<?= $form->close() ?>
</section>
