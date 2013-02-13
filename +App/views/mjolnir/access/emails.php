<?
	namespace app;
	
	/* @var $theme ThemeView */
	
	$h = isset($h) ? $h : H::up();
	$h1 = $h;
	$h2 = H::up($h1);
	
	$email_manager = \app\CFS::config('mjolnir/auth')['default.emails_manager'];
?>

<?= $f = Form::i('twitter.general', $email_manager)
	->errors($errors['\mjolnir\access\Auth::update_mainemail']) ?>

	<fieldset>
		
		<?= $f->hidden('action')->value('change-main-email') ?>
		
		<?= $f->composite
			(
				'Main Email',
				$f->text(null, 'email')
					->value($control->mainemail()),
				$f->submit(\app\Lang::term('Update'))
					->classes(['btn', 'btn-primary'])
			)
			->format('%1 %2')
			->help('<i class="icon-warning-sign"></i> If the email belongs to another account, once verified, that account will be locked.') ?>
		
	</fieldset>

<?= $f->close() ?>

<hr/>

<p class="alert alert-info"><?= \app\Lang::key('mjolnir:access/emails-intructions') ?></p>

<? $secondary_emails = $control->secondaryemails() ?>
<? if (\count($secondary_emails) > 0): ?>
	<<?= $h2 ?>><?= Lang::term('Secondary Emails') ?></<?= $h2 ?>>

	<table class="table">
		<? foreach ($secondary_emails as $secondary_email): ?>
			<tr>
				<td style="width: 1%;">
					<?= $f = Form::i('twitter.general', $email_manager) ?>
						<?= $f->hidden('id')->value($secondary_email['id']) ?>
						<?= $f->hidden('action')->value('remove-secondary-email') ?>
						<button type="submit" class="btn btn-warning btn-mini"><?= Lang::term('Remove') ?></button>
					<?= $f->close() ?>
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

<br/>

<?= $f = Form::i('twitter.general', $email_manager)
	->errors($errors['\mjolnir\access\Auth::update_mainemail']) ?>

	<fieldset>

		<?= $f->hidden('action')->value('add-secondary-email') ?>
		
		<?= $f->composite
			(
				'Secondary Email',
				$f->text(null, 'email'),
				$f->submit(Lang::term('Send Authorization Code'))
					->classes(['btn', 'btn-primary'])
			)
			->format('%1 %2')
			->help('<i class="icon-warning-sign"></i> If the email belongs to another account, once verified, that account will be locked.') ?>
		
	</fieldset>
	
<?= $f->close() ?>

<<?= $h2 ?>><?= Lang::term('Link Account') ?></<?= $h2 ?>>

<?= \app\View::instance()
	->file('mjolnir/access/auth')
	->variable('context', $context)
	->render() ?>