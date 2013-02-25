<?
	namespace app;

	/* @var $context Backend_Registration */
	/* @var $control Controller_Backend */

	$signin_providers = \app\CFS::config('mjolnir/auth')['signin'];

	$providers = \app\Arr::gather($signin_providers, 'register');

	$access_fields = array
		(
			'mjolnir:access/signup/public',
			'mjolnir:access/signup/captcha',
		);

	$access_fields = \app\Register::pull($access_fields);
	$providers = \app\Register::pull($providers);

	$switch_format = ['on' => 'Enabled', 'off' => 'Disabled'];
?>

<div role="application">

	<h1>Access Settings</h1>

	<br/>

	<?= $form = HTML::form($control->action('update'), 'mjolnir:access/twitter')
		->errors_are($errors['registration-update']) ?>

	<div class="form-horizontal">

		<fieldset>
			<legend>Sign Up</legend>

			<?
				$public_registration = $form->select('Public Sign Up', 'mjolnir:access/signup/public')
					->options_array($switch_format)
					->value_is($access_fields['mjolnir:access/signup/public']);

				if ( ! \app\CFS::config('mjolnir/auth')['standard.signup'])
				{
					$public_registration
						->hint('Disabled at static configuration level by [standard.signup], please check your private [mjolnir/auth]')
						->set('disabled', 'disabled')
						->value_is('off');
				}
			?>

			<?= $public_registration ?>

			<?= $form->select('CAPTCHA Check', 'mjolnir:access/signup/captcha')
				->options_array($switch_format)
				->value_is($access_fields['mjolnir:access/signup/captcha']) ?>

		</fieldset>

		<? if ( ! empty($signin_providers)): ?>
			<fieldset>
				<legend>Sign In</legend>

				<? foreach ($signin_providers as $provider): ?>

					<?= $form->select($provider['title'], $provider['register'])
						->options_array($switch_format)
						->value_is($providers[$provider['register']]) ?>

				<? endforeach; ?>

			</fieldset>
		<? endif; ?>

		<div class="form-actions">
			<button class="btn btn-primary" <?= $form->mark() ?>>Update</button>
		</div>

	</div>

</div>