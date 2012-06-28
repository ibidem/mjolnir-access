<?  
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$id = $_REQUEST['id'];
	$profile_info = $context->profile_info($id);
	$user = $context->entry($id);
	
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
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['user-update-profile'])
		->action($control->action('update-profile'))
		->field_template
			(
				'<div class="control-group"><span class="control-label">:name</span><div class="controls">:field</div></div>'
			)
		->classes(['form-horizontal']) ?>
	
		<fieldset>
			
			<?= $form->hidden('id', $user['id']) ?>
			
			<? $field_types = \app\CFS::config('ibidem/profile-fieldtypes') ?>
			<? foreach ($context->profile_fields() as $field): ?>
				<? $value = isset($values[$field['id']]) ? $values[$field['id']] : null ?>
				<?= $field_types[$field['type']]['form']($form, $field['title'], 'field-'.$field['id'], $value) ?>
			<? endforeach; ?>
			
			<div class="form-actions">
				<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
				<a class="btn btn-small" 
				   href="<?= \app\Relay::route('\ibidem\backend')->url(['slug' => 'user-index']) ?>">
					Cancel
				</a>
			</div>
			
		</fieldset>
	
	<?= $form->close() ?>
	
</section>