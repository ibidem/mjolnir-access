<? namespace app; ?>

<? $access_relay = \app\CFS::config('ibidem/relays') ?>

<?= $form = \app\Form::instance()
	->action($access_relay['\ibidem\access\a12n']['route']->url(array('action' => 'signin')))
	->method('POST')
	->errors($errors['ibidem\a12n\signin'])
	->field_template('<dt>:name</dt><dd>:field</dd>')
	->secure()
	?>

	<div>
		<dl>
			<?= $form->text(\app\Lang::tr('Username'), 'nickname')->attribute('autofocus', 'autofocus') ?>
			<?= $form->password(\app\Lang::tr('Password'), 'password') ?>
		</dl>
		<button tabindex="<?= \app\Form::tabindex() ?>"><?= \app\Lang::tr('Sign In') ?></button>
	</div>

<?= $form->close() ?>