<?
	namespace app;

	/* @var $context Backend_User */
	/* @var $control Controller_Backend */

	$columns = array
		(
			'title' => 'Role',
		);

	$actions = array
		(
			'user-role-edit' => array
				(
					'title' => 'Edit',
					'class' => 'btn-warning',
				)
		);
?>

<h1>Roles</h1>

<?= \app\View::instance('mjolnir/backend/template/indexer')
	->inherit($this) # inject context, control, etc
	->pass('columns', $columns)
	->pass('actions', $actions)
	->pass('plural', 'roles')
	->pass('singular', 'role')
	->render() ?>

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
				<button type="submit" class="btn btn-primary" <?= $form->mark() ?>>
					Create Role
				</button>
			</div>
		</fieldset>
	</div>
</div>
