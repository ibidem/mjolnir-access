<?
	namespace app;

	/* @var $theme ThemeView */

	$baseconfig = CFS::config('mjolnir/base');
	$contact = $baseconfig['system.info']['contact.email'];
	$site = $baseconfig['domain'].$baseconfig['path'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Email</title>
	<style type="text/css">
		#outlook a{padding:0}
		body{width:100%!important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;margin:0;padding:0}
		.ExternalClass{width:100%}
		.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:100%}
		#backgroundTable{margin:0;padding:0;width:100%!important;line-height:100%!important}
		img{outline:0;text-decoration:none;-ms-interpolation-mode:bicubic}
		a img{border:0}
		.image_fix{display:block}
		p{margin:1em 0}
		h1,h2,h3,h4,h5,h6{color:black!important}
		h1 a,h2 a,h3 a,h4 a,h5 a,h6 a{color:blue!important}
		h1 a:active,h2 a:active,h3 a:active,h4 a:active,h5 a:active,h6 a:active{color:red!important}
		h1 a:visited,h2 a:visited,h3 a:visited,h4 a:visited,h5 a:visited,h6 a:visited{color:purple!important}
		table td{border-collapse:collapse}
		table{border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0}
		a{color:blue}
		@media only screen and (max-device-width:480px){a[href^="tel"],a[href^="sms"]{text-decoration:none;color:black;pointer-events:none;cursor:default}
		.mobile_link a[href^="tel"],.mobile_link a[href^="sms"]{text-decoration:default;color:orange!important;pointer-events:auto;cursor:default}
		}
		@media only screen and (min-device-width:768px) and (max-device-width:1024px){a[href^="tel"],a[href^="sms"]{text-decoration:none;color:blue;pointer-events:none;cursor:default}
		.mobile_link a[href^="tel"],.mobile_link a[href^="sms"]{text-decoration:default;color:orange!important;pointer-events:auto;cursor:default}
		}
	</style>
</head>

<body>
	<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" >
	<tr>
		<td>
			<table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td valign="top"><h1 style="font-family: Verdana">Account Activation</h1></td>
			</tr>
			<tr>
				<td valign="top">

					<div style="text-align: center;">

						<p>
							To activate your account, you must authorize this email via the following link,<br/>
							<br/>
							&gt;&gt;
							<big style="text-align: center; color: orange; text-decoration: none;">
								<a target ="_blank" title="Token" style="color: red; text-decoration: none;" href="<?= $token_url ?>">Activate Account</a>
							</big>
							&lt;&lt;
							<p><small>Expires in 7 days from the time it was issued.</small></p>
						</p>
					</div>
					<br/>
					<h2 style="font-family: Verdana">What is this?</h2>
					<p>You have recieved this email as part of an attempt to sign up to our site, <a href="<?= $site ?>" style="text-decoration:none;" target="_blank"><?= $site ?></a>.</p>
					<p>The user name you have solicited is <strong><?= $nickname ?></strong>.</p>
					<p>If you believe someone is impersonating you please <a style="text-decoration:none;" target="_blank" href="mailto:<?= $contact ?>">contact us</a>.</p>
					<h2 style="font-family: Verdana">Link is Broken. Help!</h2>
					<p>To get a new activation link simply attempt a signin.</p>
					<p>The valid password corresponding to the registered account is required; as a security measure.</p>
					<p>No other (automated) method exists. Please contact us with any additional issues.</p>
					<p>
						<hr/>
						<small>If you did not request this account please ignore this email.</small><br/>
						<small>Please report any issues at <a href="mailto:<?= $contact ?>"><?= $contact ?></a></small>
					</p>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</body>