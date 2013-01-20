<?
	namespace app;
	
	/* @var $theme ThemeView */
?>

<div class="row">
	<div class="span12">
		<h1><?= Lang::key('mjolnir:access/emails-title') ?></h1>
		<br/>
	</div>
</div>
<div class="row">

	<div class="span12">

		<?= \app\View::instance('mjolnir/access/emails')
			->variable('control', $control)
			->variable('context', $context)
			->variable('errors', $errors)
			->render() ?>

	</div>
</div>




