<?
	namespace app;
	
	/* @var $theme ThemeView */
	
	$h = isset($h) ? $h : H::up();
	$h1 = $h;
	$h2 = H::up($h1);
?>

<?= $f = Form::i('twitter.general', \app\CFS::config('mjolnir/a12n')['default.emails_manager'])
	->errors($errors['\mjolnir\access\A12n::update_mainemail']) ?>

	<fieldset>
		
		<?= $f->hidden('action', 'change-main-email') ?>
		
		<?= $f->composite
			(
				'Main Email',
				$f->text(null, 'email')
					->value($control->mainemail()),
				$f->submit(\app\Lang::tr('Update'))
					->classes(['btn', 'btn-primary'])
			)
			->format('%1 %2') ?>
		
	</fieldset>

<?= $f->close() ?>

<hr/>

<p class="alert alert-info"><?= \app\Lang::msg('\mjolnir\access\user:emails:intructions') ?></p>

<? $secondary_emails = $control->secondaryemails() ?>
<? if (\count($secondary_emails) > 0): ?>
	<<?= $h2 ?>><?= Lang::tr('Secondary Emails') ?></<?= $h2 ?>>

	<table>
		
	</table>
<? else: # blank state ?>
	<p class="alert alert-warning">
		<?= \app\Lang::msg('\mjolnir\access\user:emails:no_secondary_emails') ?>
	</p>
<? endif; ?>

<<?= $h2 ?>><?= Lang::tr('Add Secondary Email') ?></<?= $h2 ?>>

<br/>

<?= $f = Form::i('twitter.general', \app\CFS::config('mjolnir/a12n')['default.emails_manager'])
	->errors($errors['\mjolnir\access\A12n::update_mainemail']) ?>

	<fieldset>

		<?= $f->hidden('action', 'add-secondary-email') ?>
		
		<?= $f->text('Email', 'email')
			->help('<i class="icon-warning-sign"></i> If the email belongs to another account, that account will be locked.') ?>
		
		<div class="form-actions">
			<button class="btn btn-primary" type="submit">
				<?= Lang::tr('Send Authorization Code') ?>
			</button>
		</div>
		
	</fieldset>
	
<?= $f->close() ?>
