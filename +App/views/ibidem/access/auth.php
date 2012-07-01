<?
	namespace app; 
	
	$providers = $context->authorized_a12n_providers();
?>

<? if ( ! empty($providers)): ?>
	<ul class="nav nav-pills nav-stacked">
		<? foreach ($providers as $provider): ?>
			<li>
				<a href="#" onclick="facebookLogin(); return false;">
					<i class="icon-<?= $provider['icon'] ?>"></i> <?= $provider['title'] ?>
				</a>
				<?= \app\View::instance()->file('ibidem/access/auth/'.$provider['slug'])->render() ?>
			</li>
		<? endforeach; ?>
	</ul>
<? else: # no providers ?>
	&nbsp;
<? endif; ?>
