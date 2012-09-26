<?
	namespace app;
	
	$provider_id = 'github';
	
	$provider = \app\CFS::config('mjolnir/a12n')['signin'][$provider_id];
?>

<a rel="nofollow" href="<?= \app\URL::route('\mjolnir\access\channel')->url(['provider' => 'universal', 'id' => $provider_id]) ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>