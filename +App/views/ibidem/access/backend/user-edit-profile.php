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

<section role="application">
	
	<h1>Edit Profile #<?= $id ?></h1>
	
	<br/>
	<p><small>User: <strong><?= $user['nickname'] ?></strong></small></p>
	<?= $form = Form::instance()
		->standard('twitter.general')
		->errors($errors['user-update-profile'])
		->action($control->action('update-profile')) ?>
	
		<fieldset>
			
			<?= $form->hidden('id')->value($user['id']) ?>
			
			<? $field_types = \app\CFS::config('ibidem/profile-fieldtypes') ?>
			<? foreach ($context->profile_fields() as $field): ?>
				<? $value = isset($values[$field['id']]) ? $values[$field['id']] : null ?>
				<?= $field_types[$field['type']]['form']($form, $field['title'], 'field-'.$field['id'], $value) ?>
			<? endforeach; ?>
			
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
				<a class="btn btn-small" 
				   href="<?= $control->backend('user-profile') ?>?id=<?= $user['id'] ?>">
					Cancel
				</a>
			</div>
			
		</fieldset>
	
	<?= $form->close() ?>
	
</section>