<?
	namespace app;

	/* @var $theme ThemeView */

	$user = \app\Model_User::entry(\app\Auth::instance()->user());
?>

<div class="hero-unit">

	<h1>Welcome back!</h1>
	<br/>
	<table class="table">
		<tr>
			<th>username</th>
			<td>
				<? if (empty($user['provider'])): ?>
					<?= $user['nickname'] ?>
				<? else: # provider nickname ?>
					<strong><?= $user['provider'] ?>:</strong><?= $user['nickname'] ?>
				<? endif; ?>
			</td>
		<tr>
		<tr>
			<th>last sign in</th>
			<td>
				<?= \app\Auth::userinfo()['last_signin'] !== null ? \app\Auth::userinfo()['last_signin']->format('Y-m-d H:i') : 'an unspecified date' ?>
			</td>
		<tr>
		<tr>
			<th>access level</th>
			<td>
				<?= \app\Auth::userinfo()['roletitle'] ?>
			</td>
		<tr>
		<tr>
			<th>member since</th>
			<td>
				<?= \app\Auth::userinfo()['timestamp']->format('Y-m-d') ?>
			</td>
		</tr>
	</table>
	<br/>
	<p><a href="<?= \app\Server::url_frontpage() ?>" class="btn btn-primary btn-large"><i class="icon-home"></i> Proceed to <?= \app\CFS::config('mjolnir/base')['site:title'] ?></a></p>
</div>