<?php

namespace Mini\Controller;

use Mini\Model\Auth;

class LoginController
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
	if($_POST != null) {

		$user_id = strip_tags($_REQUEST["username"]);
		$pass_in = strip_tags($_REQUEST["password"]);

		$Auth = new Auth();
		$juser = $Auth->getUser($user_id);

		if ($juser != null)
		{
			$password = hash('SHA256', $juser->salt.$pass_in);
			if (($password == $juser->password) && ($juser->status != 'inactive') && ($juser->status != 'disabled'))
			{
				$Auth->updateLastLogin($juser->id);
				$_SESSION["logged_in"] = "true";
				$_SESSION["user_id"] = $juser->id;
				$_SESSION["admin"] = $juser->admin;
			}
			else
			{
				$_SESSION['msg'] = "Username / Password not valid.";
			}
		}
		else if ($juser->status == 'inactive')
		{
			$_SESSION['msg'] = "Username / Password not valid.";
		}
	}

	@$destination = $_SESSION['page_target'];
	if (strpos($destination,'login')) {
		$destination='/';
	}
	header("location: $destination");

	require APP . 'view/_templates/header.php';
	require APP . 'view/login/index.php';
	require APP . 'view/_templates/footer.php';
    }

    public function genpass($password)
    {
	require("SessionUtil.php");

	require APP . 'view/_templates/header.php';
	if ($_SESSION['admin'] == 1) {
		$salt = hash('SHA256',microtime());
		echo "SALT " . $salt . "<br>";
		$pass = hash('SHA256',$salt.$password);
		echo "PASS " . $pass . "<br>";
	}

    }

/*
    public function twitch()
    {
	require("SessionUtil.php");
        if (!isset($_GET["code"]))      // not an authorization callback
        {
                require APP . 'view/_templates/header.php';
                require APP . 'view/login/twitch.php';
                require APP . 'view/_templates/footer.php';
        }
    }

    public function streamlabs()
    {
	require("SessionUtil.php");
        if (!isset($_GET["code"]))      // not an authorization callback
        {
                require APP . 'view/_templates/header.php';
                require APP . 'view/login/streamlabs.php';
                require APP . 'view/_templates/footer.php';
        }
    }
*/
}
