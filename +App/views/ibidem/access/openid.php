<?
	namespace app; 
	
	$providers = $context->authorized_a12n_providers();
?>

<? if ( ! empty($providers)): ?>
	<ul class="nav nav-pills nav-stacked">
		<li><a href="#"><i class="icon-facebook"></i> Facebook</a></li>
		<li><a href="#"><i class="icon-twitter"></i> Twitter</a></li>
	</ul>
<? else: # no providers ?>
	&nbsp;
<? endif; ?>
