<?
	namespace app;
?>

<div class="row">
	<div class="span12">
		<h1><?= Lang::msg('mjolnir.access.signin.title') ?></h1>
		<br/>
	</div>
</div>
<div class="row">

	<section class="span6">

		<div class="well">
			<br/>
			<?= \app\View::instance()
				->file('mjolnir/access/signin')
				->variable('errors', $errors)
				->render() ?>

		</div>



	</section>

	<section class="span3">

		<?= \app\View::instance()
			->file('mjolnir/access/auth')
			->variable('context', $context)
			->render() ?>

	</section>


	<? if (\app\Access::can('\mjolnir\access\a12n', ['action' => 'signup'])): ?>
		<section class="span3">
			<p>
				<span class="label label-info"><?= Lang::tr('Help') ?></span>
				<small>
					<?= Lang::msg('mjolnir.access.signin.not_yet_a_member') ?>
					<a class="nowrap" href="<?= \app\URL::route('\mjolnir\access\a12n')->url(['action' => 'signup']) ?>">
						<?= Lang::msg('mjolnir.access.sign_up_now') ?>
					</a>
				</small>
			</p>
		</section>
	<? endif; ?>
</div>
