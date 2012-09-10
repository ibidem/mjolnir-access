<?
	namespace app;
	
	$provider = \app\CFS::config('mjolnir/a12n')['signin']['facebook'];
?>

<a rel="nofollow" href="<?= \app\AccessChannel_Facebook::signin_url() ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>