<?
	namespace app;

	/* @var $context Backend_User */
	/* @var $control Controller_Backend */

	$columns = array
		(
			'nickname' => 'Nickname',
			'roletitle' => 'Role',
			'email' => 'Email',
			'ipaddress' => 'IP Address',
			'last_signin' => 'Last Signin',
		);

	$renderers = array
		(
			'roletitle' => function (array &$entry)
				{
					return "<em>{$entry['roletitle']}</em>";
				},
			'last_signin' => function (array &$entry)
				{
					return $entry['last_signin']->format('Y-m-d @ H:i');
				}
		);

	$actions = array
		(
			'user-profile' => array
				(
					'title' => 'View Profile',
				),
			'user-edit' => array
				(
					'title' => 'Edit',
					'class' => 'btn-warning',
				)
		);
?>

<h1>Users</h1>

<?= \app\View::instance('mjolnir/backend/template/indexer')
	->inherit($this) # inject context, control, etc
	->pass('search_columns', ['nickname', 'email', 'ipaddress'])
	->pass('default_order', ['role' => 'asc', 'nickname' => 'asc'])
	->pass('columns', $columns)
	->pass('renderers', $renderers)
	->pass('actions', $actions)
	->pass('plural', 'users')
	->pass('singular', 'user')
	->render() ?>

<div role="application">

	<h2>New User</h2>
	<br>
	<?= $form = HTML::form($control->action('new'), 'mjolnir:twitter')
		->autocomplete_array($_POST)
		->errors_are($errors['user-new']) ?>

	<div class="form-horizontal">
		<fieldset>

			<?= $form->text('Nickname', 'nickname')
				->set('autocomplete', 'off') ?>

			<?= $form->text('Email', 'email')
				->set('autocomplete', 'off') ?>

			<?= $form->password('Password', 'password')
				->set('autocomplete', 'off') ?>

			<?= $form->password('Password (verify)', 'verifier')
				->set('autocomplete', 'off') ?>

			<?= $form->select('Role', 'role')
				->options_table($context->roles(), 'id', 'title')
				->render()?>

			<?= $form->select('Active', 'active')
				->options_array(['yes' => 'Yes', 'no' => 'No'])
				->value_is('yes') ?>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary" <?= $form->mark() ?>>Create User</button>
			</div>
		</fieldset>
	</div>

</div>
