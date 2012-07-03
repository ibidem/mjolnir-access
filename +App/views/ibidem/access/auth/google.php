<?
	namespace app;
	
	$provider = \app\CFS::config('ibidem/a12n')['signin']['google'];
?>

<a rel="nofollow" href="#">
	<i class="icon-signin"></i> <?= $provider['title'] ?>
</a>