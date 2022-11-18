<?php

namespace Mini\Controller;

class LogoutController
{
    public function index()
    {
	if(session_id() == '') {
	        session_name('LBCSESS');
		session_save_path("/mnt/sessions/");
		ini_set('session.cookie_lifetime', 86400);
		ini_set('session.gc_maxlifetime', 86400);
	        session_start();
	}
	session_destroy();
	require APP . 'view/_templates/header.php';
	require APP . 'view/logout/index.php';
	require APP . 'view/_templates/footer.php';
    }
}
