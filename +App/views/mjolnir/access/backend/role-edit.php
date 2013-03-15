<?
	/* @var $context \app\Backend_Role */
	/* @var $control \app\Controller_Backend */

	namespace app;

	$id = $_REQUEST['id'];
	$role = \array_merge($context->entry($id), $_POST);
?>

<div role="application">

	<h1>Edit Role #<?= $id ?></h1>
	<br/>

	<?= $form = HTML::form($control->action('update'), 'mjolnir:twitter')
		->errors_are($errors['role-update']) ?>

	<div class="form-horizontal">
		<fieldset>

			<?= $form->hidden('id')
				->value_is($id) ?>

			<?= $form->text('Title', 'title')
				->value_is($role['title']) ?>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary" <?= $form->mark() ?>>Update</button>
				<a class="btn btn-small"
				   href="<?= \app\URL::href('mjolnir:backend.route', ['slug' => 'user-role-index']) ?>">
					Cancel
				</a>
			</div>

		</fieldset>
	</div>

</div>