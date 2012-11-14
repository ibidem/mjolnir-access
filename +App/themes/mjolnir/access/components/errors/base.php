<? 
	namespace app; 
	
	$base_config = \app\CFS::config('mjolnir/base');
	$landing_page = '//'.$base_config['domain'].$base_config['path'].$base_config['site:frontend'];
?>

<div id="page" role="main">
	
	<div class="container">
		
		<ul class="nav nav-pills">
			<li><a href="<?= $landing_page ?>"><i class="icon-home"></i> <?= $base_config['site:title'] ?></a></li>
		</ul>
		
		<hr/>
		
		<div class="alert alert-error">
			
			<?= $view->render() ?>
		</div>
		
	</div>

</div>