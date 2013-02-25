<?
	namespace app;

	/* @var $context Backend_User */
	/* @var $control Controller_Backend */

	$id = $_REQUEST['id'];
	$user = \array_merge($context->entry($id), $_POST);
?>

<div role="application">

	<h1>Edit User #<?= $id ?></h1>

	<br/>

	<?= $form = HTML::form($control->action('update'), 'mjolnir:twitter')
		->errors_are($errors['user-update'])
		->autocomplete($user) ?>

	<div class="form-horizontal">
		<fieldset>
			<?= $form->hidden('id')
				->value_is($id) ?>

			<?= $form->text('Nickname', 'nickname') ?>
			<?= $form->text('Email', 'email') ?>

			<?= $form->select('Role', 'role')
				->options_table($context->roles(), 'id', 'title') ?>

			<?= $form->select('Active', 'active')
				->options_array(['yes' => 'Yes', 'no' => 'No'])
				->disable_autocomplete()
				->value_is($user['active'] == '1' ? 'yes' : 'no') ?>

			<?= $form->password('Password', 'password')
				->set('autocomplete', 'off') ?>

			<?= $form->password('Password (verify)', 'verifier')
				->set('autocomplete', 'off') ?>

			<div class="form-actions">
				<button class="btn btn-primary" <?= $form->mark() ?>>
					Update
				</button>
				<a class="btn btn-small"
				   href="<?= \app\URL::href('mjolnir:backend.route', ['slug' => 'user-index']) ?>">
					Back to User Index
				</a>
			</div>
		</fieldset>
	</div>

</div>