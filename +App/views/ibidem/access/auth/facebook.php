<?
	namespace app;
	
	$provider = \app\CFS::config('ibidem/a12n')['signin']['facebook'];
	
	$appid = $provider['AppID'];
	$redirect = \app\URL::route('\ibidem\access\channel')->url(['provider' => 'facebook']);
	$state = \app\Session::set('facebook_state', \md5(\uniqid(\rand(), true)));
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
?>

<a rel="nofollow" href="https://www.facebook.com/dialog/oauth?client_id=<?= $appid ?>&amp;redirect_uri=<?= $protocol ?>:<?= $redirect ?>&amp;scope=email&amp;state=<?= $state ?>">
	<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
</a>