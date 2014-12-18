<?php
require_once('protected/config.php');
require_once('global.php');

if($logged == 0){
	header("Location: login.php");
}elseif($role != "admin"){
	die("No permission.");
}

if(isset($_GET['createstream'])){
	$title = "Create Stream";
	$createstream = $_GET['createstream'];
	
	if(isset($_POST['name']) && isset($_POST['url'])){
		$name = $_POST['name'];
		$url = $_POST['url'];
		
		if( (!$name) || (!$url) ){
			$message = "Complete all the fields below.";
		}else{
			$stmt = $mysqli->prepare("SELECT id FROM streams WHERE name = ? OR url = ?");
			echo($mysqli->error);
			$stmt->bind_param('ss', $name, $url);
			$stmt->execute();
			if(!($stmt->fetch())){
				$stmt->close();
				
				if(substr($url, 0, 7) === "http://" || substr($url, 0, 8) === "https://"){
					$stmt = $mysqli->prepare("INSERT INTO streams (name, url) VALUES (?, ?)");
					echo($mysqli->error);
					$stmt->bind_param('ss', $name, $url);
					$stmt->execute();
					$stmt->close();
					
					$message = "Stream Added";
					header('Refresh: 2; URL= manage.php');
				}else{
					$message = "URL must be HTTP or HTTPS protocol.";
				}
			}else{
				$message = "That a stream with that name or URL already exists.";
			}
		}
	}
}else{
	$title = "Manage Streams";
}

function streams(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT id,name,url FROM streams ORDER BY id");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->bind_result($out_id,$out_name,$out_url);
	$streams = array();
	
	while($stmt->fetch()){
		$streams[] = array('id' => $out_id, 'name' => $out_name, 'url' => $out_url);
	}
	
	$stmt->close();
	
	return $streams;
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo($title); ?> | <?php echo($app); ?></title>
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
					<?php if(isset($createstream)){ ?>
					
					<h1>Add a stream to choose from</h1>
					<?php echo($message); ?>
					<form action="manage.php?createstream" method="post" role="form">
						<input type="text" class="form-control" name="name" placeholder="Stream Name" required autofocus><br>
						<input type="text" class="form-control" name="url" placeholder="Twitch URL" required><br>
						<button class="btn btn-warning" type="submit" name="submit">Create</button>
					</form>
					
					<?php }else{ ?>
					
					<h1>Stream Management <small>Add, Remove, and choose what to display</small> <a href="?createstream" class="btn btn-sm btn-info">Create Stream</a></h1>
					<div class="well">
						<table class="table table-striped">
							<tr>
								<th>Name</th>
								<th>URL</th>
								<th></th>
							</tr>
							<?php $streams = streams();
							foreach($streams as $stream): ?>
							<tr>
								<td><?php echo($stream['name']); ?></td>
								<td><?php echo($stream['url']); ?></td>
								<td><a href="?delstream=<?php echo($stream['id']); ?>" onclick="return confirmation()"><span class="glyphicon glyphicon-remove"></span></a>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
					
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php include_once('footer.php'); ?>
		
	</body>
</html>
