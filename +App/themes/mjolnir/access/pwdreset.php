<?
	namespace app;

	/* @var $theme ThemeView */
?>

<h1><?= Lang::key('mjolnir:access/pwdreset-title') ?></h1>

<?= \app\View::instance('mjolnir/access/pwdreset')
	->pass('route', \app\URL::href('mjolnir:access/auth.route', ['action' => 'pwdreset']))
	->bind('errors', $errors)
	->render() ?>
