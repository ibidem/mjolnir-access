<?
	namespace app;

	$route_matcher = \app\URL::route('\mjolnir\access\a12n');

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

		<?= $f->text(Lang::msg('mjolnir.access.signin.username_or_email'), 'identity')
			->attr('autofocus', 'autofocus') ?>

		<?= $f->password(Lang::tr('Password'), 'password') ?>

		<?= $f->select
			(
				null,
				'remember_me',
				[
					Lang::msg('mjolnir.access.signin.remember_me') => 'on',
					Lang::msg('mjolnir.access.signin.dont_remember_me') => 'off'
				]
			)
			->value('off') ?>

		<? if (isset($_POST, $_POST['show_captcha'])): ?>
			<?= \app\ReCaptcha::html() ?>
		<? endif; ?>

		<div class="form-actions">
			<button class="btn btn-primary btn-large" <?= $f->sign() ?>>
				<i class="icon-signin"></i> <?= Lang::tr('Sign In') ?>
			</button>

			&nbsp;
			<a href="<?= \app\CFS::config('mjolnir/a12n')['default.pwdreset'] ?>">
				<?= \app\Lang::tr('Reset Password') ?>
			</a>
		</div>

	</fieldset>

<?= $f->close() ?>