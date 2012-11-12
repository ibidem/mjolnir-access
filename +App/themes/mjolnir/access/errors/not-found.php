<?
	namespace app;
	
	\app\GlobalEvent::fire('webpage:title', '404');
?>

<h1>Not Found</h1>

<p>The requested resource could not be found.</p>

<?= \app\Exception::debuginfo_for($exception) ?>