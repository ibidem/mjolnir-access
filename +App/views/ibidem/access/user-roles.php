<? namespace app; 
	/* @var $context \app\Backend_Access */
	/* @var $control \app\Controller_Backend */
	 
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$pagelimit = 10;
?>

<h2>User Roles</h2>

<? $roles = $context->roles($page, $pagelimit) ?>

<? if ( ! empty($roles)): ?>

	<table>
		<thead>
			<tr>
				<th>role</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($roles as $role): ?>
				<tr>
					<td><?= $role['title'] ?></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>

	<?= $context->roles_pager()
		->pagelimit($pagelimit)
		->currentpage($page)
		->render() ?>

<? else: # no users in system ?>
	<p class="empty"><em>There are currently no roles defined.</em></p>
<? endif; ?>

<hr/>

<section role="application">
	<h3>Create Role</h3>
	<?= $form = Form::instance()
		->method(\ibidem\types\HTTP::POST)
		->field_template('<dt>:name</dt><dd>:field</dd>')
		->errors($errors['\ibidem\access\backend\role-new'])
		->action($control->action('role-new')) ?>

		<dl>
			<?= $form->text('Title', 'title')->autocomplete(false) ?>
		</dl>
	
		<div>
			<hr/>
			<button tabindex="<?= Form::tabindex() ?>">Create</button>
		</div>

	<?= $form->close() ?>
</section>
