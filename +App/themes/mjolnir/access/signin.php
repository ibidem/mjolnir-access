<?
	namespace app;

	/* @var $theme ThemeView */

	$providers = $context->authorized_a12n_providers();
?>

<h1><?= Lang::key('mjolnir:access/signin-title') ?></h1>

<? $f = HTML::form(\app\URL::route('mjolnir:access/auth.route')->url(['action' => 'signin']), 'mjolnir:twitter')
	->errors_are($errors['mjolnir:access/signin.errors']) ?>

<? View::frame() ?>

	<div class="form-horizontal">
		<fieldset>

			<?= \app\View::instance('mjolnir/access/signin')
				->pass('errors', $errors)
				->pass('errorkey', 'mjolnir:access/signin.errors')
				->pass('form', $f)
				->render() ?>

		</fieldset>
	</div>

	<? if ( ! empty($providers)): ?>

		<h2>Authenticate Through Service</h2>

		<?= \app\View::instance('mjolnir/access/auth')
			->pass('context', $context)
			->render() ?>

	<? endif; ?>

	<? if ($context->can_signup()): ?>
		<div class="span3">
			<p>
				<span class="label label-info"><?= Lang::term('Help') ?></span>
				<small>
					<?= Lang::key('mjolnir:access/not-yet-a-member') ?>
					<a class="nowrap" href="<?= \app\URL::route('mjolnir:access/auth.route')->url(['action' => 'signup']) ?>">
						<?= Lang::key('mjolnir:access/sign-up-now') ?>
					</a>
				</small>
			</p>
		</div>
	<? endif; ?>

<?= $f->appendtagbody(View::endframe()); ?>
