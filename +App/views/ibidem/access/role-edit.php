<? namespace app; 
	/* @var $context \app\Backend_Access */
?>

<? $id = $_POST['id'] ?>
<? $role = \array_merge($context->role($id), $_POST) ?>

<section role="application">
	<h2>Edit User #<?= $id ?></h2>

	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST) 
		->errors($errors['\ibidem\access\backend\role-update'])
		->action($control->action('role-update'))
		->field_template('<dt>:name</dt> <dd>:field</dd>') ?>
	
		<dl>
			<?= $form->text('Title', 'title')->value($role['title']) ?>
		</dl>
	
		<div>
			<hr/>
			<?= $form->hidden('id')->value($id) ?>
			<button tabindex="<?= Form::tabindex() ?>">Update</button>
		</div>

	<?= $form->close() ?>
</section>