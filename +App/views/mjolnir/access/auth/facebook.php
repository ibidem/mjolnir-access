<?
	namespace app;

	$provider = CFS::config('mjolnir/auth')['signin']['facebook'];
?>

<a rel="nofollow" href="<?= \app\AccessChannel_Facebook::signin_url() ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>