<?
	namespace app; 

	$route_matcher = \app\URL::route('\ibidem\access\a12n');
	
	if (isset($errors) && isset($errors['ibidem\a12n\signin']))
	{
		$form_errors = $errors['ibidem\a12n\signin']['form'];
		unset($errors['ibidem\a12n\signin']['form']);
	}
?>

<?= $f = Form::i('twitter.general', $route_matcher->url(['action' => 'signin']))
	->errors($errors['ibidem\a12n\signin'])
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
		
		<?= $f->text(Lang::msg('ibidem.access.signin.username_or_email'), 'identity')->attr('autofocus', 'autofocus') ?>
		<?= $f->password(Lang::tr('Password'), 'password') ?>
		<?= $f->select(null, 'remember_me', [ Lang::msg('ibidem.access.signin.remember_me') => 'on', Lang::msg('ibidem.access.signin.dont_remember_me') => 'off' ])
			->value('off') ?>

		<div class="form-actions">
			<button class="btn btn-primary btn-large" <?= $f->sign() ?>>
				<i class="icon-signin"></i> <?= Lang::tr('Sign In') ?>
			</button>
		</div>
	</fieldset>

<?= $f->close() ?>