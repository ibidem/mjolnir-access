<? namespace app; ?>

<? $access_relay = \app\CFS::config('ibidem/relays') ?>

<?= $form = \app\Form::instance()
	->action($access_relay['\ibidem\access\A12n']['route']->url(array('action' => 'signin')))
	->method('POST')
	->field_template('<dt>:name</dt><dd>:field</dd>')
	->secure()
	?>

	<div>
		<dl>
			<?= $form->text(\app\Lang::tr('Username'), 'username') ?>
			<?= $form->password(\app\Lang::tr('Password'), 'password') ?>
		</dl>
		<button tabindex="<?= \app\Form::tabindex() ?>"><?= \app\Lang::tr('Sign In') ?></button>
	</div>

<?= $form->close() ?>