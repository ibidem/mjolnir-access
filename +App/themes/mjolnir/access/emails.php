<?
	namespace app;

	/* @var $theme ThemeView */

	$h1 = HH::next();
	$h2 = HH::next();
?>

<h1><?= Lang::key('mjolnir:access/emails-title') ?></h1>

<?= \app\View::instance('mjolnir/access/emails')
	->pass('route', \app\URL::href('mjolnir:access/auth.route', ['action' => 'emails']))
	->pass('control', $control)
	->pass('context', $context)
	->bind('errors', $errors)
	->pass('h', $h2)
	->render() ?>
