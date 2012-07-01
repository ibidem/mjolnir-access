<?
	namespace app;
	
	$facebook_provider = \app\CFS::config('ibidem/a12n')['signin']['facebook'];
?>
<div id="fb-root"></div>
<script>
	
	window.fbAsyncInit = function () {
		FB.init({
			appId      : '<?= $facebook_provider['apikey'] ?>', // App ID
			channelUrl : '<?= \app\Relay::route('\ibidem\access\channel')->url(['provider' => 'facebook']) ?>', // Channel File
			status     : true, // check login status
			cookie     : true, // enable cookies to allow the server to access the session
			xfbml      : true  // parse XFBML
		  });
	  // Additional initialization code here
	};

	// Load the SDK Asynchronously
	(function (d) {
		var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
		if (d.getElementById(id)) { return; }
		js = d.createElement('script'); js.id = id; js.async = true;
		js.src = "//connect.facebook.net/en_US/all.js";
		ref.parentNode.insertBefore(js, ref);
	}(document));
	
	var facebookLogin = function () {  
		FB.login(function (response) {
			if (response.session) {
				FB.api('/me',  function(response) {
					alert('Welcome ' + response.name);
					alert('Full details: ' + JSON.stringify(response));
				});
			}
		} , {perms: ''}); 
	}
	
</script>