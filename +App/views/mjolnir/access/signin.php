<?
	namespace app;

	$f = &$form;

	$errorkey = isset($errorkey) ? $errorkey : 'mjolnir:access/signin.errors';

	if (isset($errors, $errors[$errorkey], $errors[$errorkey]['form']))
	{
		$form_errors = $errors[$errorkey]['form'];
		unset($errors[$errorkey]['form']);
	}
?>

<? if (isset($_POST) && isset($_POST['form']) && $_POST['form'] == $f->get('id')): ?>
	<div class="control-group">
		<? if (isset($form_errors)): ?>
			<? foreach ($form_errors as $error): ?>
				<div class="alert alert-error"><?= $error ?></div>
			<? endforeach; ?>
		<? endif; ?>
	</div>
<? endif; ?>

<?= $f->text(Lang::key('mjolnir:access/username-or-email'), 'identity')
	->set('autofocus', 'autofocus') ?>

<?= $f->password(Lang::term('Password'), 'password') ?>

<?= $f->select(null, 'remember_me')
	->options_array
	(
		[
			'on' => Lang::key('mjolnir:access/remember-me'),
			'off' => Lang::key('mjolnir:access/dont-remember-me')
		]
	)
	->value_is('off') ?>

<? if (isset($_POST, $_POST['show_captcha'])): ?>
	<?= \app\ReCaptcha::html() ?>
<? endif; ?>

<div class="form-actions">
	<button type="submit" class="btn btn-primary btn-large" <?= $f->sign() ?>>
		<i class="icon-signin"></i> <?= Lang::term('Sign In') ?>
	</button>

	&nbsp;
	<a href="<?= \app\URL::href('mjolnir:access/auth.route', ['action' => 'pwdreset']) ?>">
		<?= \app\Lang::term('Reset Password') ?>
	</a>
</div>
