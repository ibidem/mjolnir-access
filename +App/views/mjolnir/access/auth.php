<?
	namespace app; 
	
	$providers = $context->authorized_a12n_providers();
?>

<? if ( ! empty($providers)): ?>
	<ul class="nav nav-pills nav-stacked">
		<? foreach ($providers as $provider): ?>
			<li>
				<?= \app\View::instance()->file('mjolnir/access/auth/'.$provider['slug'])->render() ?>
			</li>
		<? endforeach; ?>
	</ul>
<? else: # no providers ?>
	&nbsp;
<? endif; ?>
