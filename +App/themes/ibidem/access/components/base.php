<? 
	namespace app; 
	
	$base_config = \app\CFS::config('ibidem/base');
	$landing_page = '//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend'];
?>

<div id="page" role="main">
	
	<div class="container">
		
		<ul class="nav nav-pills">
			<li><a href="<?= $landing_page ?>"><i class="icon-home"></i> <?= $base_config['site:title'] ?></a></li>
			<? if (\app\Access::can('\mjolnir\backend')): ?>
				<li><a href="<?= \app\URL::route('\mjolnir\backend')->url() ?>"><i class="icon-briefcase"></i> <?= Lang::tr('Backend') ?></a></li>
			<? endif; ?>
			<? if (\app\A12n::instance()->role() !== \app\A12n::guest()): ?>
				<li>
					<a href="<?= \app\URL::route('\mjolnir\access\a12n')->url(['action' => 'signout']) ?>">
						<i class="icon-signout"></i> <?= Lang::tr('Sign Out') ?>
					</a>
				</li>
			<? endif; ?>
		</ul>
		
		<hr/>
		
		<?= $view->render() ?>
	</div>

</div>