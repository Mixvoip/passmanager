<?php
	/*
	* Session Hijacking Security Option
	*/
	session_start();
	ini_set('session.cookie_httponly', true);
	ini_set('session.cookie_secure',true);
	ini_set('session.gc_maxlifetime', 60*60*8);
	$inactive = 60*60*8;
	if(isset($_SESSION['timeout']) ) {
		$session_life = time()-$_SESSION['timeout'];
		if($session_life > $inactive){
			$_SESSION['user_id']=null;
			session_unset();
			session_destroy();
			header("Location: index.php");
		}
	} else {
		$_SESSION['timeout'] = time();
	}

	/*
	* Session Hijacking Security Option
	* check if ip is set
	*/

	if(isset($_SESSION['last_ip']) === false){
		$_SESSION['last_ip'] = $_SERVER['REMOTE_ADDR'];
	}

	/*
	* Session Hijacking Security Option
	* check if ip is the same
	*/

	if($_SESSION['last_ip'] !== $_SERVER['REMOTE_ADDR']){
		$_SESSION['user_id']=null;
		session_unset();
		session_destroy();
	}
?>
