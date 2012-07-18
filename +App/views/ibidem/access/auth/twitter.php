<?
	namespace app;
	
	$provider = \app\CFS::config('ibidem/a12n')['signin']['twitter'];

?>

<a rel="nofollow" href="<?= \app\Relay::route('\ibidem\access\channel')->url(['provider' => 'twitter']) ?>">
	<i class="icon-facebook"></i> <?= $provider['title'] ?>
</a>