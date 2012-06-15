<? namespace app; ?>

<section id="page" role="main">

	<? if (\app\A12n::instance()->role() === \app\A12n::guest()): ?>
		<h1><?= Lang::msg('ibidem.access.signin.title') ?></h1>
		<?= \app\View::instance()
			->file('ibidem/signin')
			->variable('errors', $errors)
			->render() ?>

	<? else: # not guest (ie. logged in) ?>

		<h1><?= Lang::msg('ibidem.access.stats.title') ?></h1>
		<? $user = \app\A12n::instance()->current() ?>
		<p><?= Lang::msg('ibidem.access.stats.currently_logged_as', ['username' => $user['nickname']]) ?></p>

	<? endif; ?>

</section>