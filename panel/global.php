<?php
session_name("twitchMP");
session_start();
require_once('protected/config.php');

// Checking if the sessions are set
if(isset($_SESSION['username']) && isset($_SESSION['pass']) && isset($_SESSION['id'])){
	$session_username = $_SESSION['username'];
	$session_pass = $_SESSION['pass'];
	$session_id = $_SESSION['id'];
	
	//check if the member exists
	$stmt = $mysqli->prepare("SELECT id,password FROM `users` WHERE `id` = ? AND `password` = ?");
	echo($mysqli->error);
	$stmt->bind_param('is', $session_id, $session_pass);
	$stmt->execute();
	$stmt->bind_result($id,$pass);
	
	if($stmt->fetch()){
		//logged in stuff here
		$logged = 1;
	}else{
		header("Location: logout.php");
		exit();
	}
	$stmt->close();
}else if(isset($_COOKIE['id_cookie'])){
	$session_id = $_COOKIE['id_cookie'];
	$session_pass = $_COOKIE['pass_cookie'];
	
	//check if the member exists
	
	$stmt = $mysqli->prepare("SELECT id,password FROM `users` WHERE `id` = ? AND `password` = ?");
	echo($mysqli->error);
	$stmt->bind_param('is', $session_id, $session_pass);
	$stmt->execute();
	$stmt->bind_result($id,$pass);
	
	if($stmt->fetch()){
		while($row = $stmt->fetch_array()){
			$session_username = $row['username'];
		}
		//create sessions
		$_SESSION['username'] = $session_username;
		$_SESSION['id'] = $session_id;
		$_SESSION['pass'] = $session_pass;
		
		//logged in stuff here
		$logged = 1;
	}else{
		header("Location: logout.php");
		exit();
	}
	$stmt->close();
}else{
	//if the user is not logged in
	$logged = 0;
}

if(isset($_SESSION['id'])){
	$stmt = $mysqli->prepare("SELECT `role` FROM `users` WHERE id = ?");
	echo($mysqli->error);
	$stmt->bind_param('i', $session_id);
	$stmt->execute();
	$stmt->bind_result($role);
	$stmt->fetch();
	$stmt->close();
}

// Clean the input of possibly malicious or interfering code
function clean($input){
	$output = preg_replace("#[^0-9]#","",$input);
	return $output;
}