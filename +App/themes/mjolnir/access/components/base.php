<?
	namespace app;

	$base_config = \app\CFS::config('mjolnir/base');
	$landing_page = '//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend'];
?>

<div id="page" role="main">

	<div class="container">

		<ul class="nav nav-pills">
			<li><a href="<?= $landing_page ?>"><i class="icon-home"></i> <?= $base_config['site:title'] ?></a></li>

			<? if (\app\Auth::role() !== \app\Auth::guest()): ?>
				<li>
					<a href="<?= \app\URL::route('\mjolnir\access\a12n')->url([]) ?>">
						<i class="icon-key"></i> <?= Lang::term('Account') ?>
					</a>
				</li>

				<li>
					<a href="<?= \app\URL::route('\mjolnir\access\a12n')->url(['action' => 'emails']) ?>">
						<i class="icon-envelope-alt"></i> <?= Lang::term('Emails') ?>
					</a>
				</li>
			<? endif; ?>

			<? if (\app\Access::can('\mjolnir\backend')): ?>
				<li><a href="<?= \app\URL::route('\mjolnir\backend')->url() ?>"><i class="icon-briefcase"></i> <?= Lang::term('Backend') ?></a></li>
			<? endif; ?>

			<? if (\app\Auth::role() !== \app\Auth::guest()): ?>
				<li>
					<a href="<?= \app\URL::route('\mjolnir\access\a12n')->url(['action' => 'signout']) ?>">
						<i class="icon-signout"></i> <?= Lang::term('Sign Out') ?>
					</a>
				</li>
			<? endif; ?>

		</ul>

		<hr/>

		<?= $theme->partial('components/announcements')->render() ?>

		<?= $view->render() ?>
	</div>

</div>