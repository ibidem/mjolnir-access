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

	<?= $form = HTML::form($control->action('delete'))
		->addfieldtemplate(':field') ?>

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
							->value_is($field['id']) ?>
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

						<?= $delete_form = HTML::form($control->action('erase'), 'mjolnir:inline') ?>
						<?= $delete_form->hidden('id')->value_is($field['id']) ?>
						<button class="btn btn-mini btn-danger" <?= $delete_form->mark() ?>>
							Delete
						</button>

					</td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<div class="row-fluid">

		<br/>

		<div class="pull-right marginless-pagination">
			<?= $context->pager()
				->pagelimit_is($pagelimit)
				->page_is($page)
				->apply('twitter') ?>
		</div>

		<button class="btn btn-danger btn-mini" <?= $form->mark() ?>>
			<i class="icon-trash"></i> Delete Selected
		</button>

	</div>

<? else: # no users in system ?>
	<br/>
	<p class="alert alert-info">There are currently <strong>no fields</strong> defined.</p>
<? endif; ?>

<hr/>

<div role="application">
	<h2>New Profile Field</h2>
	<br/>
	<?= $form = HTML::form($control->action('new'), 'mjolnir:twitter')
		->errors_are($errors['profilefield-new']) ?>

	<div class="form-horizontal">
		<fieldset>

			<?= $form->text('Field Title', 'title')
				->set('autocomplete', 'off') ?>

			<?= $form->text('Name', 'name')
				->set('autocomplete', 'off') ?>

			<?= $form->text('Order Index', 'idx')
				->set('autocomplete', 'off')
				->value_is(10) ?>

			<?= $form->select('Data Type', 'type')
				->options_array($context->fieldtypes()) ?>

			<?= $form->select('Type', 'required')
				->options_array([ 'false' => 'Optional', 'true' => 'Required'])
				->value_is('false') ?>

			<div class="form-actions">
				<button class="btn btn-primary" <?= $form->mark() ?>>
					Define Field
				</button>
			</div>

		</fieldset>
	</div>
</div>
