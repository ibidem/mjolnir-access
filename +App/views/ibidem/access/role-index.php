<? 
	/* @var $context \app\Backend_Role */
	/* @var $control \app\Controller_Backend */

	namespace app; 

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>Users</h1>

<? $roles = $context->entries($page, $pagelimit) ?>

<? if ( ! empty($roles)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table class="table table-striped marginless">
		<thead>
			<tr>
				<th class="micro-col">&nbsp;</th>
				<th>role</th>
				<th>&nbsp;</th>
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
					<td class="table-controls">
						
						<a href="<?= $control->backend('user-role-edit') ?>?id=<?= $role['id'] ?>"
						   class="btn btn-mini btn-warning">
							Edit
						</a>

						<?= $delete_form = \app\Form::instance() 
							->standard('twitter.table-controls')
							->action($control->action('erase')) ?>

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
	<p class="alert alert-info">There are currently <strong>no roles</strong> defined.</p>
<? endif; ?>

<hr/>
	
<section role="application">
	<h2>New Role</h2>
	<br/>
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['role-new'])
		->action($control->action('new')) ?>

		<fieldset>
			<?= $form->text('Title', 'title')->autocomplete(false) ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Create Role</button>
			</div>
		</fieldset>
	
		

	<?= $form->close() ?>
</section>
