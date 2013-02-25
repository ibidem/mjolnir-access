<?
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */

	namespace app;

	$id = $_REQUEST['id'];
	$user = $context->entry($id);

	$profile_info = $context->profile_info($id);
	$values = [];
	foreach ($profile_info as $field)
	{
		$values[$field['id']] = $field['value'];
	}
?>

<div role="application">

	<h1>Edit Profile #<?= $id ?></h1>

	<br/>
	<p>User: <strong><?= $user['nickname'] ?></strong></p>

	<?= $form = HTML::form($control->action('update-profile'), 'mjolnir:twitter')
		->errors_are($errors['user-update-profile']) ?>

	<div class="form-horizontal">
		<fieldset>

			<?= $form->hidden('id')->value_is($user['id']) ?>

			<? $field_types = \app\CFS::config('mjolnir/profile-fieldtypes') ?>
			<? $profile_fields = $context->profile_fields() ?>

			<? if ( ! empty($profile_fields)): ?>
				<? foreach ($profile_fields as $field): ?>
					<? $value = isset($values[$field['id']]) ? $values[$field['id']] : null ?>
					<?= $field_types[$field['type']]['form']($form, $field['title'], 'field-'.$field['id'], $value) ?>
				<? endforeach; ?>
			<? else: # blank state ?>
				<p class="text-info">There are currently no profile fields defined.</p>
			<? endif; ?>

			<div class="form-actions">
				<button class="btn btn-primary" <?= $form->mark() ?>>Update</button>
				<a class="btn btn-small" href="<?= $control->backend('user-profile') ?>?id=<?= $user['id'] ?>">
					Cancel
				</a>
			</div>

		</fieldset>

	</div>

</div>