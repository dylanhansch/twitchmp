<?php
require_once('protected/config.php');
require_once('global.php');

$prefix = "";
$message = "";

// Permission check
if($logged == 0){
	header("Location: ".$basedir);
}elseif($role != "admin"){
	die("No permission");
}

// Return a list of all outgoing streams
function outgoing(){
	global $mysqli;
	
	$stmt = $mysqli->prepare("SELECT id,name,path,type,status FROM outgoing");
	echo($mysqli->error);
	$stmt->execute();
	$stmt->bind_result($id,$name,$path,$type,$status);
	$streams = array();
	
	while($stmt->fetch()){
		$streams[] = array('id' => $id, 'name' => $name, 'path' => $path, 'type' => $type, 'status' => $status);
	}
	
	$stmt->close();
	
	return $streams;
}

if(isset($_GET['create'])){
	$prefix = "Create ";
	
	// Start process of inserting new stream information into the database
	if(isset($_POST['type'])){
		$type = $_POST['type'];
		$name = $_POST['name'];
		$path = $_POST['path'];
		$status = $_POST['status'];

		if( (!$type) || (!$name) || (!$path) || (!$status) ){
			$message = "Fill out all the fields.";
		}else{
			// Check if outgoing stream with the name in POST already exists
			$stmt = $mysqli->prepare("SELECT name FROM outgoing WHERE name = ?");
			echo($mysqli->error);
			$stmt->bind_param('s', $name);
			$stmt->execute();
			$stmt->bind_result($name_check);
			$stmt->fetch();
			$stmt->close();

			if($name_check == $name){ // Check if name already exists
				$message = "Name already in use.";
			}elseif($status != "active" && $status != "disabled"){ // Check for valid STATUS
				$message = "Invalid status. $status";
			}elseif($type != "twitch"){ // Check for valid TYPE
				$message = "Invalid type. $type";
			}else{ // Inserting the information ONLY IF the above checks detected ZERO errors
				$stmt = $mysqli->prepare("INSERT INTO outgoing (name,path,type,status) VALUES (?,?,?,?)");
				echo($mysqli->error);
				$stmt->bind_param('ssss', $name, $path, $type, $status);
				$stmt->execute();
				$stmt->close();

				$message = "Outgoing Stream Created as \"$name\" ";
			}
		}
	}
}

if(isset($_GET['edit'])){
	$prefix = "Edit ";
	$edit = clean($_GET['edit']);
	
	// Grab initial information based on the specified ID
	$stmt = $mysqli->prepare("SELECT name,path,type,status FROM outgoing WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $edit);
	$stmt->execute();
	$stmt->bind_result($name,$path,$type,$status);
	if(!($stmt->fetch())){
		header("Location: outgoing.php");
	}
	$stmt->close();
	
	// Start the process of updating the information in the database
	if(isset($_POST['type'])){
		$type = $_POST['type'];
		$name = $_POST['name'];
		$path = $_POST['path'];
		$status = $_POST['status'];
		
		if( (!$type) || (!$name) || (!$path) || (!$status) ){
			$message = "Fill out all the fields.";
		}else{
			// Check if outgoing stream with the name in POST already exists
			$stmt = $mysqli->prepare("SELECT name FROM outgoing WHERE id <> ? AND name = ?");
			echo($mysqli->error);
			$stmt->bind_param('is', $edit, $name);
			$stmt->execute();
			$stmt->bind_result($name_check);
			$stmt->fetch();
			$stmt->close();

			if($name_check == $name){ // Check if name already exists
				$message = "Name already in use.";
			}elseif($status != "active" && $status != "disabled"){ // Check for valid STATUS
				$message = "Invalid status. $status";
			}elseif($type != "twitch"){ // Check for valid TYPE
				$message = "Invalid type. $type";
			}else{ // Updating the information ONLY IF the above checks detected ZERO errors
				$stmt = $mysqli->prepare("UPDATE outgoing SET name = ?, path = ?, type = ?, status = ? WHERE id = ?");
				echo($mysqli->error);
				$stmt->bind_param('ssssi', $name, $path, $type, $status, $edit);
				$stmt->execute();
				$stmt->close();

				$message = "Outgoing Stream Updated as \"$name\" ";
			}
		}
	}
}

// Delete outgoing stream from application
function del_outgoing($id){
	global $mysqli, $session_id;
	
	$stmt = $mysqli->prepare("DELETE FROM outgoing WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->close();
}

if(isset($_GET['del'])){
	del_outgoing($_GET['del']);
	header("Location: outgoing.php");
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo($prefix); ?>Outgoing | <?php echo($app); ?></title>
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
					<?php if(isset($_GET['create'])){ ?>
					
					<h1>Create an Outgoing Stream</h1>
					<ol class="breadcrumb" style="margin-top:10px;">
						<li><a href="outgoing.php">Outgoing</a></li>
						<li class="active">Create</li>
					</ol>
					
					<?php echo($message); ?>
					<form action="outgoing.php?create" method="post" role="form">
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Type</label>
								<select name="type" class="form-control" required>
									<option value="twitch" selected="selected">Twitch</option>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" placeholder="Billy's Stream" required>
							</div>
						</div><br>
						
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Play Path/Stream Key</label>
								<input type="text" class="form-control" name="path" placeholder="twitch.com/example/stream" required>
							</div>
							<div class="col-sm-6">
								<label for="name">Status</label>
								<select name="status" class="form-control" required>
									<option value="active" selected="selected">Active</option>
									<option value="disabled">Disabled</option>
								</select>
							</div>
						</div><br>
						
						<button class="btn btn-warning" style="margin-bottom:20px" type="submit" name="submit">Create</button>
					</form>
					
					<?php }elseif(isset($_GET['edit'])){ ?>
					
					<h1>Edit an Outgoing Stream "<?php echo($name); ?>"</h1>
					<ol class="breadcrumb" style="margin-top:10px;">
						<li><a href="outgoing.php">Outgoing</a></li>
						<li class="active">Edit</li>
					</ol>
					
					<?php echo($message); ?>
					<form action="outgoing.php?edit=<?php echo($edit); ?>" method="post" role="form">
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Type</label>
								<select name="type" class="form-control" required>
									<option value="twitch" <?php if($type == "twitch"){ ?>selected="selected"<?php }?>>Twitch</option>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" value="<?php echo($name); ?>" required>
							</div>
						</div><br>
						
						<div class="row">
							<div class="col-sm-6">
								<label for="name">Play Path/Stream Key</label>
								<input type="text" class="form-control" name="path" value="<?php echo($path); ?>" required>
							</div>
							<div class="col-sm-6">
								<label for="name">Status</label>
								<select name="status" class="form-control" required>
									<option value="active" <?php if($status == "active"){ ?>selected="selected"<?php }?>>Active</option>
									<option value="disabled" <?php if($status == "disabled"){ ?>selected="selected"<?php }?>>Disabled</option>
								</select>
							</div>
						</div><br>
						
						<button class="btn btn-warning" style="margin-bottom:20px" type="submit" name="submit">Save</button>
					</form>
					
					<?php }else{ ?>
					
					<h1>Manage Outgoing Streams <a href="?create" class="btn btn-info">Create</a></h1>
					<table class="table table-striped">
						<tr>
							<th>Name</th>
							<th>Play Path/Stream Key</th>
							<th>Type</th>
							<th>Status</th>
							<th></th>
						</tr>
						<?php $streams = outgoing();
						foreach($streams as $stream){ ?>
						<tr>
							<td><?php echo($stream['name']); ?></td>
							<td><?php echo($stream['path']); ?></td>
							<td><?php echo($stream['type']); ?></td>
							<td><?php echo($stream['status']); ?></td>
							<td><a href="?edit=<?php echo($stream['id']); ?>"><span class="glyphicon glyphicon-pencil"></span></a> <a href="?del=<?php echo($stream['id']); ?>" onclick="return confirmation()"><span class="glyphicon glyphicon-remove"></span></a></td>
						</tr>
						<?php } ?>
					</table>
					
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php include_once('footer.php'); ?>
		
		<script type="text/javascript">
		function confirmation() {
			var r = confirm("WARNING!\nThis action is perminate and non reversable. Are you sure you want to continue?");
			if (r == true) {
				return true;
			} else {
				return false;
			}
		}
		</script>
	</body>
</html>
