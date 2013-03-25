<?
	namespace app;

	/* @var $theme      ThemeView */
	/* @var $entrypoint View */

	$baseconfig = \app\CFS::config('mjolnir/base');
?>

<div class="container">

	<br/>

	<div class="navbar navbar-inverse">
		<div class="navbar-inner">
			<div class="container">
				<a href="<?= \app\Server::url_frontpage() ?>" class="brand">
					<?= $baseconfig['system']['title'] ?>
				</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<? if (\app\Auth::role() !== \app\Auth::Guest): ?>
							<li>
								<a href="<?= \app\URL::href('mjolnir:access/auth.route') ?>">
									<i class="icon-key"></i> <?= Lang::term('Account') ?>
								</a>
							</li>

							<li>
								<a href="<?= \app\URL::href('mjolnir:access/auth.route', ['action' => 'emails']) ?>">
									<i class="icon-envelope-alt"></i> <?= Lang::term('Emails') ?>
								</a>
							</li>
						<? endif; ?>

						<? if (\app\Access::can('mjolnir:backend.route')): ?>
							<li>
								<a href="<?= \app\URL::href('mjolnir:backend.route') ?>">
									<i class="icon-briefcase"></i> <?= Lang::term('Backend') ?>
								</a>
							</li>
						<? endif; ?>
					</ul>
					<? if (\app\Auth::role() !== \app\Auth::Guest): ?>
						<ul class="nav pull-right">
							<li>
								<a href="<?= \app\URL::href('mjolnir:access/auth.route', ['action' => 'signout']) ?>">
									<i class="icon-signout"></i> <?= Lang::term('Sign Out') ?>
								</a>
							</li>
						</ul>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div>

	<?= $theme->partial('foundation/announcements')->render() ?>

	<?= $entrypoint->render() ?>

</div>