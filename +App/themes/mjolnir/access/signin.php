<?
	namespace app;

	/* @var $theme ThemeView */
?>

<div class="row">
	<div class="span12">
		<h1><?= Lang::key('mjolnir:access/signin-title') ?></h1>
		<br/>
	</div>
</div>
<div class="row">

	<div class="span6">

		<div class="well">
			<br/>
			<?= \app\View::instance()
				->file('mjolnir/access/signin')
				->variable('errors', $errors)
				->render() ?>

		</div>

	</div>

	<div class="span3">

		<?= \app\View::instance()
			->file('mjolnir/access/auth')
			->variable('context', $context)
			->render() ?>

	</div>

	<? if ($context->can_signup()): ?>
		<div class="span3">
			<p>
				<span class="label label-info"><?= Lang::term('Help') ?></span>
				<small>
					<?= Lang::key('mjolnir:access/not-yet-a-member') ?>
					<a class="nowrap" href="<?= \app\URL::route('\mjolnir\access\a12n')->url(['action' => 'signup']) ?>">
						<?= Lang::key('mjolnir:access/sign-up-now') ?>
					</a>
				</small>
			</p>
		</div>
	<? endif; ?>
</div>
