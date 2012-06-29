<?
	namespace app; 
	
	$access_relay = \app\CFS::config('ibidem/relays');
?>

<?= $form = \app\Form::instance()
	->standard('twitter.general')
	->action($access_relay['\ibidem\access\a12n']['route']->url(array('action' => 'signin')))
	->errors($errors['ibidem\a12n\signin'])
	->classes(['marginless'])
	->secure() ?>

	<fieldset>
		<?= $form->text(\app\Lang::msg('ibidem.access.signin.username_or_email'), 'identity') ?>
		<?= $form->password(\app\Lang::tr('Password'), 'password') ?>
		<?= $form->select(null, 'remember_me', [ Lang::msg('ibidem.access.signin.remember_me') => 'on', Lang::msg('ibidem.access.signin.dont_remember_me') => 'off' ])
			->value('off') ?>
		<div class="form-actions">
			<button class="btn btn-primary btn-large" tabindex="<?= \app\Form::tabindex() ?>"><i class="icon-signin"></i> <?= \app\Lang::tr('Sign In') ?></button>
		</div>
	</fieldset>

<?= $form->close() ?>