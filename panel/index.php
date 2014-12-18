<?php
require_once('protected/config.php');
require_once('global.php');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Index | <?php echo($app); ?></title>
		<meta charset="utf-8">
		<meta name="author" content="Dylan Hansch">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<link rel="shortcut icon" content="none">
		
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	</head>
	<body>
		<?php include_once('navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<h1><?php echo($app); ?> <small>Choose which user to stream to a single channel</small></h1>
					
					<iframe src="http://www.twitch.tv/dylanhansch/embed" frameborder="0" scrolling="no" height="378" width="620" class="center"></iframe><a href="http://www.twitch.tv/dylanhansch?tt_medium=live_embed&tt_content=text_link" style="padding:2px 0px 4px; display:block; width:345px; font-weight:normal; font-size:10px;text-decoration:underline;"></a>
				</div>
			</div>
		</div>
		
		<?php include_once('footer.php'); ?>
		
	</body>
</html>
