<?
	namespace app; 
	
	$providers = $context->authorized_a12n_providers();
?>

<? if ( ! empty($providers)): ?>
	<ul class="nav nav-pills nav-stacked">
		<? foreach ($providers as $provider): ?>
		<li><a href="<?= \app\Relay::route('\ibidem\access\openid')->url(['openid' => $provider['slug']]) ?>"><i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?></a></li>
		<? endforeach; ?>
	</ul>
<? else: # no providers ?>
	&nbsp;
<? endif; ?>
