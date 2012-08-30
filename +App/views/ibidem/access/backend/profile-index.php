<? 
	/* @var $context \app\Backend_Profile */
	/* @var $control \app\Controller_Backend */

	namespace app; 

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>Profile Fields</h1>

<? $fields = $context->entries($page, $pagelimit) ?>

<? if ( ! empty($fields)): ?>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->action($control->action('delete'))
		->field_template(':field') ?>

	<?= $form->close() ?>

	<table class="table table-striped marginless">
		<thead>
			<tr>
				<th class="micro-col">&nbsp;</th>
				<th class="micro-col">#idx</th>
				<th class="micro-col">type</th>
				<th class="micro-col">name</th>
				<th>field name</th>
				<th>data-type</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($fields as $field): ?>
				<tr>
					<td>
						<?= $form->checkbox(null, 'selected[]')
							->attribute('form', $form->form_id())
							->value($field['id']) ?>
					</td>
					
					<td><?= $field['idx'] ?></td>
					<td>
						<? if ( ! $field['required']): ?>
							<span class="label">optional</span>
						<? else: ?>
							<span class="label label-important">required</span>
						<? endif; ?>
					</td>
					<td><?= $field['name'] ?></td>
					<td><?= $field['title'] ?></td>
					<td><?= $field['type'] ?></td>
					<td class="table-controls">

						<a href="<?= $control->backend('user-profile-edit') ?>?id=<?= $field['id'] ?>"
						   class="btn btn-mini btn-warning">
							 Edit
						</a>

						<?= $delete_form = \app\Form::instance() 
							->standard('twitter.table-controls')
							->action($control->action('erase')) ?>

							<fieldset>
								<?= $delete_form->hidden('id')->value($field['id']) ?>
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
	<p class="alert alert-info">There are currently <strong>no fields</strong> defined.</p>
<? endif; ?>

<hr/>
	
<section role="application">
	<h2>New Profile Field</h2>
	<br/>
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['profilefield-new'])
		->action($control->action('new')) ?>

		<fieldset>
			<?= $form->text('Field Title', 'title')->autocomplete(false) ?>
			<?= $form->text('Name', 'name')->autocomplete(false) ?>
			<?= $form->text('Order Index', 'idx')->value(10)->autocomplete(false) ?>
			<?= $form->select('Data Type', 'type')->values($context->fieldtypes()) ?>
			<?= $form->select('Type', 'required')->values([ 'Optional' => 'false', 'Required' => 'true'])->value('false') ?>
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Define Field</button>
			</div>
		</fieldset>
	
		

	<?= $form->close() ?>
</section>
