<?
	namespace app;

	/* @var $theme ThemeView */

	$route_matcher = \app\URL::route('\mjolnir\access\a12n');

	if (isset($errors) && isset($errors['\mjolnir\a12n\signup']))
	{
		$form_errors = $errors['\mjolnir\a12n\signup']['form'];
		unset($errors['\mjolnir\a12n\signup']['form']);
	}
?>

<?= $f = Form::i('twitter.general', $route_matcher->url(['action' => 'signup']))
	->errors($errors['\mjolnir\a12n\signup'])
	->classes(['marginless'])
	->secure() ?>

	<fieldset>

		<? if (isset($_POST) && isset($_POST['form']) && $_POST['form'] === $f->form_id()): ?>
			<div class="control-group">
				<? if (isset($form_errors)): ?>
					<? foreach ($form_errors as $error): ?>
						<span class="alert alert-error"><?= $error ?></span>
					<? endforeach; ?>
				<? endif; ?>
			</div>
		<? endif; ?>


		<?= $f->text(Lang::tr('Name'), 'nickname')->attr('autofocus', 'autofocus') ?>
		<?= $f->text(Lang::tr('Email'), 'email') ?>
		<?= $f->password(Lang::tr('Password'), 'password') ?>
		<?= $f->password(Lang::tr('Password (again)'), 'verifier') ?>

		<hr/>

		<?=	\app\ReCaptcha::html() ?>

		<div class="form-actions">
			<button class="btn btn-primary btn-large" <?= $f->sign() ?>>
				<i class="icon-signin"></i> <?= Lang::tr('Sign Up') ?>
			</button>
		</div>
	</fieldset>