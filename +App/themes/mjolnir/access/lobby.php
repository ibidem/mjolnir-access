<?
	namespace app;

	/* @var $theme ThemeView */

	$user = UserLib::entry(\app\Auth::id());
	$baseconfig = CFS::config('mjolnir/base');
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
				<?= \app\Auth::info()['last_signin'] !== null ? \app\Auth::info()['last_signin']->format('Y-m-d @ H:i') : 'an unspecified date' ?>
			</td>
		<tr>
		<tr>
			<th>access level</th>
			<td>
				<?= \app\Auth::info()['roletitle'] ?>
			</td>
		<tr>
		<tr>
			<th>member since</th>
			<td>
				<?= \app\Auth::info()['timestamp']->format('Y-m-d') ?>
			</td>
		</tr>
	</table>
	<br/>
	<p><a href="<?= \app\Server::url_frontpage() ?>" class="btn btn-primary btn-large"><i class="icon-home"></i> Proceed to <?= $baseconfig['system']['title'] ?></a></p>
</div>