<?
	namespace app;

	/* @var $theme ThemeView */

	$form_standard = isset($form_standard) ? $form_standard : 'mjolnir:twitter';

	if (isset($route))
	{
		$pwdreset_manager = $route;
	}
	else # $route not set
	{
		$authconfig = CFS::config('mjolnir/auth');
		$pwdreset_manager = $authconfig['default.pwdreset'];
	}

	if (isset($errors, $errors['\mjolnir\a12n\pwdreset'], $errors['\mjolnir\a12n\pwdreset']['form']) && ! empty($errors['\mjolnir\a12n\pwdreset']['form']))
	{
		$form_errors = $errors['\mjolnir\a12n\pwdreset']['form'];
		unset($errors['\mjolnir\a12n\pwdreset']['form']);
	}
?>

<? if (isset($_POST, $_POST['notice'])): ?>

	<p><?= $_POST['notice'] ?></p>

<? else: # pwdreset form ?>

	<? $f = HTML::form($pwdreset_manager, $form_standard)
		->errors_are($errors['\mjolnir\a12n\pwdreset']) ?>

	<? View::frame() ?>
	
		<div class="form-horizontal">

			<fieldset>

				<? if (isset($_POST) && isset($_POST['form']) && $_POST['form'] === $f->get('id')): ?>
					<div class="control-group">
						<? if (isset($form_errors)): ?>
							<? foreach ($form_errors as $error): ?>
								<span class="alert alert-error"><?= $error ?></span>
							<? endforeach; ?>
						<? endif; ?>
					</div>
				<? endif; ?>

				<? if (isset($_GET, $_GET['user'], $_GET['key'])): ?>

					<?= $f->hidden('user')->value_is($_GET['user']) ?>

					<?= $f->hidden('key')->value_is($_GET['key']) ?>

					<?= $f->password(Lang::term('New Password'), 'password') ?>

				<? else: ?>

					<?= $f->text(Lang::term('<b>Username</b> or <b>Email</b>'), 'identity')
						->set('autofocus', 'autofocus') ?>

					<div class="control-group">
						<div class="controls">
							<?=	\app\ReCaptcha::html() ?>
						</div>
					</div>

				<? endif; ?>

				<div class="form-actions">
					<button class="btn btn-primary btn-large" <?= $f->mark() ?>>
						<i class="icon-unlock"></i> <?= Lang::term('Reset Password') ?>
					</button>
				</div>

			</fieldset>

		</div>
	
	<?= $f->appendtagbody(View::endframe()); ?>

<? endif; ?>