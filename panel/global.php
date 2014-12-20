<?php
session_name("twitchMP");
session_start();
require_once('protected/config.php');

//checking is the sessions are set
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

// List links you've got permission to in a table on index.php
function links(){
	global $mysqli, $logged, $role, $session_id;
	if($role == "admin"){
		$stmt = $mysqli->prepare("SELECT links.id, links.short, links.url, links.privacy, users.username FROM `links` INNER JOIN `privileges` ON privileges.link_id = links.id INNER JOIN `users` ON users.id = privileges.user_id");
		echo($mysqli->error);
		$stmt->execute();
		$stmt->bind_result($out_id,$out_short,$out_url,$out_privacy,$out_owner);
		$links = array();

		while($stmt->fetch()){
			$links[] = array('id' => $out_id, 'short' => $out_short, 'url' => $out_url, 'privacy' => $out_privacy, 'owner' => $out_owner);
		}
		$stmt->close();

		return $links;
	}elseif($logged == 1){
		$stmt = $mysqli->prepare("SELECT links.id, links.short, links.url, links.privacy, users.username FROM `links` INNER JOIN `privileges` ON privileges.link_id = links.id INNER JOIN `users` ON users.id = privileges.user_id WHERE privacy = 'public' OR privileges.user_id = ?");
		echo($mysqli->error);
		$stmt->bind_param('i', $session_id);
		$stmt->execute();
		$stmt->bind_result($out_id,$out_short,$out_url,$out_privacy,$out_owner);
		$links = array();
		
		while($stmt->fetch()){
			$links[] = array('id' => $out_id, 'short' => $out_short, 'url' => $out_url, 'privacy' => $out_privacy, 'owner' => $out_owner);
		}
		$stmt->close();
		
		return $links;
	}else{
		$stmt = $mysqli->prepare("SELECT id,short,url FROM `links` WHERE `privacy` = 'public'");
		echo($mysqli->error);
		$stmt->execute();
		$stmt->bind_result($out_id,$out_short,$out_url);
		$links = array();

		while($stmt->fetch()){
			$links[] = array('id' => $out_id, 'short' => $out_short, 'url' => $out_url);
		}
		$stmt->close();

		return $links;
	}
}

// Delete specified link from database
function del_link($link_id){
	global $mysqli, $session_id, $role;
	
	if(isset($session_id) && $role == "admin"){
		$stmt = $mysqli->prepare("DELETE FROM links WHERE id = ?");
		echo($mysqli->error);
		$stmt->bind_param("i", $link_id);
		$stmt->execute();
		$stmt->close();

		$stmt = $mysqli->prepare("DELETE FROM privileges WHERE link_id = ?");
		echo($mysqli->error);
		$stmt->bind_param("i", $link_id);
		$stmt->execute();
		$stmt->close();
	}elseif(isset($session_id)){
		$stmt = $mysqli->prepare("SELECT role FROM privileges WHERE user_id = ? AND link_id = ?");
		echo($mysqli->error);
		$stmt->bind_param('ii', $session_id, $link_id);
		$stmt->execute();
		$stmt->bind_result($perm);
		if(!($stmt->fetch())){
			$perm = "view";
		}
		$stmt->close();
		
		if($perm != "view"){
			$stmt = $mysqli->prepare("DELETE FROM links WHERE id = ?");
			echo($mysqli->error);
			$stmt->bind_param("i", $link_id);
			$stmt->execute();
			$stmt->close();

			$stmt = $mysqli->prepare("DELETE FROM privileges WHERE link_id = ?");
			echo($mysqli->error);
			$stmt->bind_param("i", $link_id);
			$stmt->execute();
			$stmt->close();
		}else{
			die("No permission.");
		}
	}
}
