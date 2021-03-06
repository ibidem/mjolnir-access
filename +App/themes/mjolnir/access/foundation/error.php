<?
	namespace app;

	$base_config = \app\CFS::config('mjolnir/base');
	$landing_page = \app\Server::url_frontpage();
?>

<div class="container">

	<ul class="nav nav-pills">
		<li><a href="<?= $landing_page ?>"><i class="icon-home"></i> <?= $base_config['system']['title'] ?></a></li>
	</ul>

	<hr/>

	<div class="alert alert-error">
		<?= $entrypoint->render() ?>
	</div>

</div>
