<?
	namespace app;

	$provider_id = 'aol';
	$provider = CFS::config('mjolnir/auth')['signin'][$provider_id];
?>

<a rel="nofollow" href="<?= \app\URL::href('mjolnir:access/channel.route', ['provider' => 'universal', 'id' => $provider_id]) ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>