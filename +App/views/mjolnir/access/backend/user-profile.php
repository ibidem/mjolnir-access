<?
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */

	namespace app;

	$id = $_GET['id'];
	$user = $context->entry($id);
?>

<div>

	<h1><small>User:</small> <?= $user['nickname'] ?></h1>

	<br/>

	<div>
		<h2>Account Information</h2>
		<dl class="dl-horizontal">
			<dt>Role</dt>
				<dd><?= $user['roletitle'] ?></dd>
			<dt>Email Address</dt>
				<dd><em><?= $user['email'] ?></em></dd>
			<dt>Signup Date</dt>
				<dd><?= $user['timestamp']->format('d M, Y') ?></dd>
			<dt>ipaddress</dt>
				<dd><?= $user['ipaddress'] ?></dd>
		</dl>
	</div>

	<? $profile_info = $context->profile_info($id) ?>

	<? if ($profile_info !== null): ?>
		<div>
			<h2>Profile Information</h2>
			<dl class="dl-horizontal">
				<? foreach ($profile_info as $field): ?>
					<dt><?= $field['title'] ?></td>
					<dd><?= $field['render'] ?></dd>
				<? endforeach; ?>
			</dl>
		</div>
	<? endif; ?>

</div>

<hr/>

<a href="<?= $control->backend('user-edit') ?>?id=<?= $user['id'] ?>"
   class="btn btn-mini btn-warning">
	Edit Account
</a>

<a href="<?= $control->backend('user-edit-profile') ?>?id=<?= $user['id'] ?>"
   class="btn btn-mini btn-warning">
	Edit Profile
</a>

<a class="btn btn-small pull-right"
   href="<?= \app\URL::href('mjolnir:backend.route', ['slug' => 'user-index']) ?>">
	Back to Index
</a>