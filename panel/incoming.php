<?php
require_once('protected/config.php');
require_once('global.php');

if($logged == 0){
	header("Location: ".$basedir);
}elseif($role != "admin"){
	die("No permission");
}

function incoming(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT name FROM streams");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->bind_result($out_name);
	$streams = array();
	
	while($stmt->fetch()){
		$streams[] = array('name' => $out_name);
	}
	
	$stmt->close();
	
	return $streams;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Incoming | <?php echo($app); ?></title>
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
					<h1>Manage Incoming Streams</h1>
					
					<?php $streams = incoming();
					foreach($streams as $stream){
						echo($stream['name']);
					} ?>
					
				</div>
			</div>
		</div>
		
		<?php include_once('footer.php'); ?>
		
	</body>
</html>
