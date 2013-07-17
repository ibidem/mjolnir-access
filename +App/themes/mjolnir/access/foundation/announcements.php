<?
	namespace app;

	/* @var $theme ThemeView */

	$notices = \app\Notice::all();

	if ( ! empty($notices))
	{
		$notice = $notices[0];
	}
	else # empty notices
	{
		$notice = null;
	}
?>

<? if ($notice !== null): ?>

	<p class="alert <?= \implode(' ', $notice->get_classes()) ?>">
		<button type="button" data-dismiss="alert" class="close">×</button>
		<?= $notice->get_body() ?>
	</p>

<? endif; ?>



