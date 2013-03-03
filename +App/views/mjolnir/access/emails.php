<?
	namespace app;

	/* @var $theme ThemeView */

	$h = isset($h) ? $h : HH::next();
	$h1 = $h;
	$h2 = HH::raise($h1);

	$form_standard = isset($form_standard) ? $form_standard : 'mjolnir:access/twitter';

	if (isset($route))
	{
		$email_manager = $route;
	}
	else # $route not set
	{
		$authconfig = CFS::config('mjolnir/auth');
		$email_manager = $authconfig['default.emails_manager'];
	}
?>

<?= $f = HTML::form($email_manager, $form_standard)
	->errors_are($errors['\mjolnir\access\Auth::update_mainemail']) ?>

<div class="form-horizontal">

	<fieldset>

		<?= $f->hidden('action')->value_is('change-main-email') ?>

		<?= $f->composite
			(
				'Main Email',
				$f->text(null, 'email')
					->value_is($control->mainemail()),
				$f->submit(\app\Lang::term('Update'))
					->set('class', ['btn', 'btn-primary'])
			)
			->fieldmix('%1 %2')
			->hint('<i class="icon-warning-sign"></i> If the email belongs to another account, once verified, that account will be locked.') ?>

	</fieldset>

</div>

<hr/>

<p class="alert alert-info"><?= \app\Lang::key('mjolnir:access/emails-intructions') ?></p>

<? $secondary_emails = $control->secondaryemails() ?>
<? if (\count($secondary_emails) > 0): ?>
	<<?= $h2 ?>><?= Lang::term('Secondary Emails') ?></<?= $h2 ?>>

	<table class="table">
		<? foreach ($secondary_emails as $secondary_email): ?>
			<tr>
				<td style="width: 1%;">
					<?= $f = HTML::form('twitter.general', $email_manager) ?>
					<?= $f->hidden('id')->value_is($secondary_email['id']) ?>
					<?= $f->hidden('action')->value_is('remove-secondary-email') ?>
					<button type="submit" class="btn btn-warning btn-mini" <?= $f->mark() ?>>
						<?= Lang::term('Remove') ?>
					</button>
				</td>
				<td><?= $secondary_email['email'] ?></td>
			</tr>
		<? endforeach; ?>
	</table>
<? else: # blank state ?>
	<p class="alert alert-warning">
		<?= \app\Lang::key('mjolnir:access/emails-no-secondary-emails') ?>
	</p>
<? endif; ?>

<<?= $h2 ?>><?= Lang::term('Add Secondary Email') ?></<?= $h2 ?>>

<?= $f = HTML::form($email_manager, $form_standard)
	->errors_are($errors['\mjolnir\access\Auth::update_mainemail']) ?>

<div class="form-horizontal">

	<fieldset>

		<?= $f->hidden('action')->value_is('add-secondary-email') ?>

		<?= $f->composite
			(
				'Secondary Email',
				$f->text(null, 'email'),
				$f->submit(Lang::term('Send Authorization Code'))
					->set('class', ['btn', 'btn-primary'])
			)
			->fieldmix('%1 %2')
			->hint('<i class="icon-warning-sign"></i> If the email belongs to another account, once verified, that account will be locked.') ?>

	</fieldset>

</div>

<? $providers = $context->authorized_a12n_providers() ?>
<? if ( ! empty($providers)): ?>

	<<?= $h2 ?>><?= Lang::term('Link Account') ?></<?= $h2 ?>>

	<?= \app\View::instance('mjolnir/access/auth')
		->pass('context', $context)
		->render() ?>

<? endif; ?>