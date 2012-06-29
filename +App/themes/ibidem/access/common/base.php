<? 
	namespace app; 
	
	$base_config = \app\CFS::config('ibidem/base');
	$landing_page = '//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend'];
?>

<div id="page" role="main">
	
	<div class="container">
		
		<ul class="nav nav-pills">
			<li><a href="<?= $landing_page ?>"><i class="icon-home"></i> <?= $base_config['site:title'] ?></a></li>
			<? if (\app\Access::can('\ibidem\backend')): ?>
			<li><a href="<?= \app\Relay::route('\ibidem\backend')->url() ?>"><i class="icon-briefcase"></i> <?= Lang::tr('Backend') ?></a></li>
			<? endif; ?>
		</ul>
		
		<hr/>
		
		<?= $view->render() ?>
	</div>

</div>