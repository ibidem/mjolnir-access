<?
	namespace app; 
	
	$access_relay = \app\CFS::config('ibidem/relays');
	
	$route_matcher = \app\URL::route('\ibidem\access\a12n');
?>

<?= $form = \app\Form::instance()
	->standard('twitter.general')
	->action($route_matcher->url(array('action' => 'signin')))
	->errors($errors['ibidem\a12n\signin'])
	->classes(['marginless'])
	->secure() ?>

	<fieldset>
		<?= $form->text(\app\Lang::msg('ibidem.access.signin.username_or_email'), 'identity')->attribute('autofocus', 'autofocus') ?>
		<?= $form->password(\app\Lang::tr('Password'), 'password') ?>
		<?= $form->select(null, 'remember_me', [ Lang::msg('ibidem.access.signin.remember_me') => 'on', Lang::msg('ibidem.access.signin.dont_remember_me') => 'off' ])
			->value('off') ?>
		<div class="form-actions">
			<button class="btn btn-primary btn-large" tabindex="<?= \app\Form::tabindex() ?>"><i class="icon-signin"></i> <?= \app\Lang::tr('Sign In') ?></button>
		</div>
	</fieldset>

<?= $form->close() ?>