<?
	namespace app;

	$providers = $context->authorized_a12n_providers();
?>

<? if ( ! empty($providers)): ?>
	<ul class="nav nav-pills nav-bar">
		<? foreach ($providers as $provider): ?>
			<li>
				<?= \app\View::instance('mjolnir/access/auth/'.$provider['slug'])->render() ?>
			</li>
		<? endforeach; ?>
	</ul>
<? else: # no providers ?>
	&nbsp;
<? endif; ?>
