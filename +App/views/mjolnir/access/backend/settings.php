<?
	/* @var $context \app\Backend_Registration */
	/* @var $control \app\Controller_Backend */

	namespace app;

	$signin_providers = \app\CFS::config('mjolnir/auth')['signin'];

	$providers = \app\Arr::gather($signin_providers, 'register');

	$access_fields = array
		(
			'mjolnir:access/signup/public.reg',
			'\mjolnir\access\signup\capcha',
		);

	$access_fields = \app\Register::pull($access_fields);
	$providers = \app\Register::pull($providers);

	$switch_format = ['Enabled' => 'on', 'Disabled' => 'off'];
?>

<section role="application">

	<h1>Access Settings</h1>

	<br/>

	<?= $form = Form::instance()->standard('twitter.general')
		->errors($errors['registration-update'])
		->action($control->action('update')) ?>

		<fieldset>
			<legend>Sign Up</legend>
			<?
				$public_registration = $form->select('Public Registration', 'mjolnir:access/signup/public.reg', $switch_format)
					->value($access_fields['mjolnir:access/signup/public.reg']);

				if ( ! \app\CFS::config('mjolnir/auth')['standard.signup'])
				{
					$public_registration
						->help('Disabled at static configuration level by [standard.signup], please check your private [mjolnir/a12n]')
						->attr('disabled', 'disabled')
						->value('off');
				}
			?>

			<?= $public_registration ?>

		</fieldset>

		<? if ( ! empty($signin_providers)): ?>
			<fieldset>
				<legend>Sign In</legend>

				<? foreach ($signin_providers as $provider): ?>

					<?= $form->select($provider['title'], $provider['register'], $switch_format)
						->value($providers[$provider['register']]) ?>

				<? endforeach; ?>

			</fieldset>
		<? endif; ?>

		<div class="form-actions">
			<button class="btn btn-primary" tabindex="<?= Form::tabindex() ?>">Update</button>
		</div>

	<?= $form->close() ?>

</section>