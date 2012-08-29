<?
	namespace app;
	
	$provider_id = 'google';
	
	$provider = \app\CFS::config('ibidem/a12n')['signin'][$provider_id];
?>

<a rel="nofollow" href="<?= \app\URL::route('\ibidem\access\channel')->url(['provider' => 'universal', 'id' => $provider_id]) ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>