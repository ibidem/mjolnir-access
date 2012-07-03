<?
	namespace app;
	
	$provider = \app\CFS::config('ibidem/a12n')['signin']['twitter'];
?>

<a rel="nofollow" href="#">
	<i class="icon-twitter"></i> <?= $provider['title'] ?>
</a>