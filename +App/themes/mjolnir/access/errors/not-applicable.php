<?
	namespace app;
	
	\app\GlobalEvent::fire('webpage:title', 'Error');
?>

<h1>Not Applicable</h1>

<p>The requested operation is not applicable.</p>

<p><?= $exception->getMessage() ?></p>

<?= \app\Exception::debuginfo_for($exception) ?>