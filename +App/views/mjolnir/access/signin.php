<?
	namespace app;

	$route_matcher = \app\URL::route('mjolnir:access/auth.route');

	if (isset($errors, $errors['\mjolnir\a12n\signin'], $errors['\mjolnir\a12n\signin']['form']))
	{
		$form_errors = $errors['\mjolnir\a12n\signin']['form'];
		unset($errors['\mjolnir\a12n\signin']['form']);
	}
?>

<?= $f = Form::i('twitter.general', $route_matcher->url(['action' => 'signin']))
	->errors($errors['\mjolnir\a12n\signin'])
	->classes(['marginless'])
	->secure() ?>

	<fieldset>

		<? if (isset($_POST) && isset($_POST['form']) && $_POST['form'] === $f->form_id()): ?>
			<div class="control-group">
				<? if (isset($form_errors)): ?>
					<? foreach ($form_errors as $error): ?>
						<div class="alert alert-error"><?= $error ?></div>
					<? endforeach; ?>
				<? endif; ?>
			</div>
		<? endif; ?>

		<?= $f->text(Lang::key('mjolnir:access/username-or-email'), 'identity')
			->attr('autofocus', 'autofocus') ?>

		<?= $f->password(Lang::term('Password'), 'password') ?>

		<?= $f->select
			(
				null,
				'remember_me',
				[
					Lang::key('mjolnir:access/remember-me') => 'on',
					Lang::key('mjolnir:access/dont-remember-me') => 'off'
				]
			)
			->value('off') ?>

		<? if (isset($_POST, $_POST['show_captcha'])): ?>
			<?= \app\ReCaptcha::html() ?>
		<? endif; ?>

		<div class="form-actions">
			<button class="btn btn-primary btn-large" <?= $f->sign() ?>>
				<i class="icon-signin"></i> <?= Lang::term('Sign In') ?>
			</button>

			&nbsp;
			<a href="<?= \app\CFS::config('mjolnir/auth')['default.pwdreset'] ?>">
				<?= \app\Lang::term('Reset Password') ?>
			</a>
		</div>

	</fieldset>

<?= $f->close() ?>