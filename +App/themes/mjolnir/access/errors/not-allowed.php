<?
	namespace app;
	
	\app\GlobalEvent::fire('webpage:title', 'Access Denied');
?>

<h1>Not Allowed</h1>

<p>The requested operation is not allowed.</p>

<?= \app\Exception::debuginfo_for($exception) ?>