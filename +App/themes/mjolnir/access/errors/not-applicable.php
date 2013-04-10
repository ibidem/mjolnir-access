<?
	namespace app;
	
	$context->channel()->set('title', 'Error');
?>

<h1>Not Applicable</h1>

<p>The requested operation is not applicable.</p>

<p><?= $exception->getMessage() ?></p>
