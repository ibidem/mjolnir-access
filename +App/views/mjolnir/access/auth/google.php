<?
	namespace app;
	
	$provider_id = 'google';
	
	$provider = \app\CFS::config('mjolnir/auth')['signin'][$provider_id];
?>

<a rel="nofollow" href="<?= \app\URL::route('mjolnir:access/channel.route')->url(['provider' => 'universal', 'id' => $provider_id]) ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>