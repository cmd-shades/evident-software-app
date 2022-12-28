<html>
	<head>
		<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
		<style>
			body {

				width: 100%;
			  color: #0092CD;
			  background: -webkit-linear-gradient(#0092CD, #67c8f0);
				
				height: 100%;
				overflow: hidden;
			}
			
			.pointer{
				cursor:pointer;
			}
			
			.evident-logo {
				width: 500px;
				border-radius: 3px;
			}
			
			h1{
			  font-size: 82px;
			  background-color: #ededed;
			  background-image: linear-gradient(to bottom, white, #c7c7c7);
			  -webkit-background-clip: text;
			  -webkit-text-fill-color: transparent;
			  margin-bottom: 0px;
			  text-align: center;
			  margin-top: 121px;
			}
			
			h2{
			  font-size: 20.5px;
			  background-color: #ededed;
			  -webkit-background-clip: text;
			  -webkit-text-fill-color: transparent;
			  margin-bottom: 0px;
			  text-align: center;
			  margin-top: 10px;
			}
			
			.error-info-container {
				width:500px;
				margin-left:calc(50vw - 250px);
				text-align:center;
				font-family: 'roboto', sans-serif;
				margin-top: calc(40vh - 250px);
				height: 500px;
			}
			
			span {
				color: white;
			}
			
			a:link {
				text-decoration: none;
				color: white;
				font-weight:bold;
			}

			a:visited {
			  text-decoration: none;
			  color: white;
			}

			a:hover {
			  text-decoration: none;
			}

			a:active {
			  text-decoration: none;
			}
			
			p {
				color: white;
			}
			
			
		</style>
	</head>
	<body>
		<div class='error-info-container'>
			<img src="<?php echo base_url('assets/images/logos/web-login-logo-small.png'); ?>" class='evident-logo'>
			<h1>- Error 404 -</h1>
			<h2><span style="font-size:30px;">Oops! something went wrong</h2>
			<h3><span style="font-size:19px;">We are unable to find the page you are you looking for!</h2>
			
			<br><br>
			<p>Click <a href="<?php echo base_url( 'webapp/home' ); ?>">here</a> to go to the <a href="<?php echo base_url( 'webapp/home' ); ?>" >homepage</a> or <a>Back</a> to return to your previous page.</p>
		</div>
	</body>
	<script>
		window.goBack = function (e){
			var homeLocation = "<?php echo base_url('webapp/home'); ?>";
			var oldHash = window.location.hash;

			history.back(); // Try to go back

			var newHash = window.location.hash;

			if( newHash === oldHash && ( typeof(document.referrer ) !== "string" || document.referrer  === "" )	){
				window.setTimeout(function(){
					// redirect to default location
					window.location.href = homeLocation;
				},1000); // set timeout in ms
			}
			
			if(e){
				if( e.preventDefault )
					e.preventDefault();
				if( e.preventPropagation )
					e.preventPropagation();
			}
			return false; // stop event propagation and browser default event
		}
	</script>
</html>