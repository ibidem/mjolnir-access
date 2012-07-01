<? 
	namespace app; 
?>

<? if (\app\A12n::instance()->role() === \app\A12n::guest()): ?>
	<div class="row">
		<div class="span12">
			<h1><?= Lang::msg('ibidem.access.signin.title') ?></h1>
			<br/>
		</div>
	</div>
	<div class="row">
		
		<section class="span6">

			<div class="well">
				<br/>
				<?= \app\View::instance()
					->file('ibidem/access/signin')
					->variable('errors', $errors)
					->render() ?>

			</div>

			
			
		</section>

		<section class="span3">
			
			<?= \app\View::instance()
				->file('ibidem/access/auth')
				->variable('context', $context)
				->render() ?>
			
		</section>
		
		
		<? if (\app\Access::can('\ibidem\access\a12n', ['action' => 'signup'])): ?>
			<section class="span3">
				<p>
					<span class="label label-info"><?= Lang::tr('Help') ?></span>
					<small>
						<?= Lang::msg('ibidem.access.signin.not_yet_a_member') ?>
						<a class="nowrap" href="<?= \app\Relay::route('\ibidem\access\a12n')->url(['action' => 'signup']) ?>">
							<?= Lang::msg('ibiden.access.sign_up_now') ?> 
						</a>
					</small>
				</p>
			</section>
		<? endif; ?>
	</div>

	

<? else: # not guest (ie. logged in) ?>
	<div class="row">
		<h1><?= Lang::msg('ibidem.access.stats.title') ?></h1>
		<br/>
	</div>
	<div class="row">	
		<div class="span12">
			<? $user = \app\A12n::instance()->current() ?>
			<p><?= Lang::msg('ibidem.access.stats.currently_logged_as', [':username' => $user['nickname']]) ?></p>
		</div>
	</div>
<? endif; ?>