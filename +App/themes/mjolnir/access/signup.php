<?
	namespace app;

	/* @var $theme ThemeView */
?>

<div class="row">
	<div class="span12">
		<h1><?= Lang::key('mjolnir:access/signup-title') ?></h1>
		<br/>
	</div>
</div>

<div class="row">

	<div class="span12">

		<div class="well">
			<br/>
			<?= \app\View::instance()
				->file('mjolnir/access/signup')
				->variable('errors', $errors)
				->render() ?>

		</div>

	</div>

</div>
