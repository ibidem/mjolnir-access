<?
	namespace app;

	/* @var $theme ThemeView */

	$user = \app\Model_User::entry(\app\A12n::instance()->user());
?>

<div class="content">

	<div class="row">
		<div class="span12">
			<? if (empty($user['provider'])): ?>
				<h1>Welcome back <?= $user['nickname'] ?>!</h1>
			<? else: # provider nickname ?>
				<h1>Welcome back <small><?= $user['provider'] ?>:</small><?= $user['nickname'] ?>!</h1>
			<? endif; ?>
		</div>
	</div>

</div>