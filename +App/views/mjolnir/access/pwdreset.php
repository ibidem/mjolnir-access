<?
	namespace app;

	/* @var $theme ThemeView */

	$route_matcher = \app\URL::route('\mjolnir\access\a12n');

	if (isset($errors, $errors['\mjolnir\a12n\pwdreset'], $errors['\mjolnir\a12n\pwdreset']['form']) && ! empty($errors['\mjolnir\a12n\pwdreset']['form']))
	{
		$form_errors = $errors['\mjolnir\a12n\pwdreset']['form'];
		unset($errors['\mjolnir\a12n\pwdreset']['form']);
	}
?>

<? if (isset($_POST, $_POST['notice'])): ?>

	<p><?= $_POST['notice'] ?></p>

<? else: # pwdreset form ?>

	<?= $f = Form::i('twitter.general', \app\CFS::config('mjolnir/a12n')['default.pwdreset'])
		->errors($errors['\mjolnir\a12n\pwdreset'])
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

			<? if (isset($_GET, $_GET['user'], $_GET['key'])): ?>

				<?= $f->hidden('user')->value($_GET['user']) ?>

				<?= $f->hidden('key')->value($_GET['key']) ?>

				<?= $f->password(Lang::tr('New Password'), 'password') ?>

			<? else: ?>

				<?= $f->text(Lang::tr('<b>Name</b> or <b>Email</b>'), 'identity')
					->attr('autofocus', 'autofocus') ?>

				<div class="control-group">
					<div class="controls">
						<?=	\app\ReCaptcha::html() ?>
					</div>
				</div>

			<? endif; ?>

			<div class="form-actions">
				<button class="btn btn-primary btn-large" <?= $f->sign() ?>>
					<i class="icon-unlock"></i> <?= Lang::tr('Reset Password') ?>
				</button>
			</div>

		</fieldset>

	<?= $f->close() ?>

<? endif; ?>