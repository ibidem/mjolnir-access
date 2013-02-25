<?
	namespace app;

	/* @var $context Backend_Role */
	/* @var $control Controller_Backend */

	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h1>Users</h1>

<? $roles = $context->entries($page, $pagelimit) ?>

<? if ( ! empty($roles)): ?>

	<?= $form = HTML::form($control->action('delete'))
		->addfieldtemplate(':field') ?>

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
							->value_is($role['id']) ?>
					</td>
					<td><?= $role['title'] ?></td>
					<td class="table-controls">

						<a href="<?= $control->backend('user-role-edit') ?>?id=<?= $role['id'] ?>"
						   class="btn btn-mini btn-warning">
							Edit
						</a>

						<?= $delete_form = HTML::form($control->action('erase'), 'mjolnir:inline') ?>
						<?= $delete_form->hidden('id')->value_is($role['id']) ?>
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
	<p class="alert alert-info">There are currently <strong>no roles</strong> defined.</p>
<? endif; ?>

<hr/>

<div role="application">
	<h2>New Role</h2>
	<br/>
	<?= $form = HTML::form($control->action('new'), 'mjolnir:twitter')
		->errors_are($errors['role-new']) ?>

	<div class="form-horizontal">
		<fieldset>

			<?= $form->text('Title', 'title')
				->set('autocomplete', 'off') ?>

			<div class="form-actions">
				<button class="btn btn-primary" <?= $form->mark() ?>>
					Create Role
				</button>
			</div>
		</fieldset>
	</div>
</div>
