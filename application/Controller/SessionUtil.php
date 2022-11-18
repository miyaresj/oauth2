<?php

if (session_id() == '') {
	session_name('LBCSESS');
	session_save_path("/mnt/sessions/");
	ini_set('session.cache_expire', 86400);
	ini_set('session.cookie_lifetime', 86400);
	ini_set('session.gc_maxlifetime', 86400);
	session_set_cookie_params(86400);
	session_start();
}

if (!(isset($_SESSION["logged_in"])) || ($_SESSION["logged_in"] !== "true")) {

       	// page to return to once logged in
	$_SESSION['page_target'] = $_SERVER['REQUEST_URI'];

	// login page
	require APP . 'view/_templates/header.php';
	require APP . 'view/login/index.php';
	require APP . 'view/_templates/footer.php';
	exit;
}

?>
