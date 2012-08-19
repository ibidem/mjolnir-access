<?  
	/* @var $context \app\Backend_User */
	/* @var $control \app\Controller_Backend */

	namespace app;
	
	$id = $_GET['id'];
	$user = $context->entry($id);
?>

<section>
	
	<h1><small>User:</small> <?= $user['nickname'] ?></h1>

	<br/>
	
	<section>
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
	</section>
	
	<? $profile_info = $context->profile_info($id) ?>
	
	<? if ($profile_info !== null): ?>
		<section>
			<h2>Profile Information</h2>
			<dl class="dl-horizontal">
				<? $profile_config = \app\CFS::config('ibidem/profile-fieldtypes') ?>
				<? foreach ($profile_info as $field): ?>

					<dt><?= $field['title'] ?></td>
						<dd><?= $profile_config[$field['type']]['render']($field['value']) ?></dd>

				<? endforeach; ?>
			</dl>
		</section>
	<? endif; ?>
	
</section>

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
   href="<?= \app\URL::route('\ibidem\backend')->url(['slug' => 'user-index']) ?>">
	Back to Index
</a>