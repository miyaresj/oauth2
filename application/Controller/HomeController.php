<?php

namespace Mini\Controller;

//use Mini\Model\Auth;

class HomeController
{
	public function index()
	{
		require("SessionUtil.php");
		require APP . 'view/_templates/header.php';
		require APP . 'view/home/index.php';
	        require APP . 'view/_templates/footer.php';
	}

	public function yo()
	{
                require APP . 'view/_templates/header.php';
                require APP . 'view/home/index.php';
                require APP . 'view/_templates/footer.php';
	}

/*
	public function login()
	{
		if(session_id() == '') {
			session_start();
		}
		if($_POST != null) {

			$Auth = new Auth();
			$user_id = strip_tags($_REQUEST["username"]);
			$pass_in = strip_tags($_REQUEST["password"]);
			$juser = $Auth->getUser($user_id);

			if($juser != null) {
				$password = hash('SHA256', $juser->salt.$pass_in);
				if (($password == $juser->password) && ($juser->status != 'inactive') && ($juser->status != 'disabled')) {
					$Auth->updateLastLogin($juser->id);
					$_SESSION["logged_in"] = "true";
					$_SESSION["user_id"] = $juser->id;
				} else {
					$_SESSION['msg'] = "Username / Password not valid.";
				}
			}
   			else if ($juser->status == 'inactive') {
				$_SESSION['msg'] = "Username / Password not valid.";
			}
 	 	}

		$destination = $_SESSION['page_target'];
		header("location: /");
	}

	public function logout()
	{
		session_destroy();
		header("location: /");
	}
*/

/*
    public function pwdreset()
    {
        session_start();

        if($_POST != null) {  // New password has been submitted
                        $lnk = $_POST['lnk'];
                $current_user = $this->model->getUserByTemppass($lnk);

                if(($current_user->temppass != $lnk) || ($lnk == '')) {
                        $_SESSION['msg'] = "Unknown Error.";
                        header("location: /error");
                        exit;
                }

                $pass1 = $_POST["pass1"];
                $pass2 = $_POST["pass2"];

                        if (strlen($pass1) < 4) {
                                $_SESSION["msg"] = "The new password was too short.  Password not changed.";
                                header("Location: /home/pwdreset?lnk=".$current_user->temppass);
                                exit;
                        } else if ($pass1 != $pass2) {
                                $_SESSION["msg"] = "The new passwords did not match.  Password not changed.";
                                header("Location: /home/pwdreset?lnk=".$current_user->temppass);
                                exit;
                        }

                        $pass1 = hash('SHA256', $current_user->salt.$pass1);
                        $pass2 = hash('SHA256', $current_user->salt.$pass2);


                        $result =  $this->model->updateUserPassword($current_user->id, $pass1);

                        if($result==true) {
                                $_SESSION["msg"] = "Password has been changed.";
                                $this->model->changeTempPassword($current_user->id, "");
                                $this->model->updateLastLogin($current_user->id);
                                $_SESSION["logged_in"] = "true";
                                $_SESSION["user_id"] = $current_user->id;
                                $hidemenu = false;

                                header("Location: /");
                                exit;
                        }


                        $_SESSION["msg"] = "There was an unknown problem.";
                        header("Location: /home/changepassword");



        } else {

                $lnk = $_GET['lnk'];
                $id = $_GET['id'];
                $current_user = $this->model->getUserById($id);

                if(($current_user->temppass != $lnk) || ($lnk == '')) {
                        $_SESSION['msg'] = "Unknown Error.";
                        header("location: /error");
                        exit;
                }

                $title = "Grind2Energy HSS Password Reset";
                $content["name"] = $title;
                $hidemenu = true;
                // load views
                require APP . 'view/_templates/header.php';
                require APP . 'view/home/pwdreset.php';
                require APP . 'view/_templates/footer.php';
        }

    }

    public function logout()
    {

        session_start();
        unset($_SESSION["logged_in"]);
        unset($_SESSION["id"]);
        $hidemenu = true;
        // load views
        header("location: /");
    }

    public function myprofile()
    {

        require("session_util.php");

        $title = "My Profile";
        $content["name"] = $title;
        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/myprofile.php';
        require APP . 'view/_templates/footer.php';

    }

    public function updateUser() {
        require("session_util.php");

        if($_POST != null) {

                $id = (int) $_POST["id"];
                $name = $_POST['name'];

                $result =  $this->model->updateUserName($id, $name);
                $_SESSION["msg"] = "User Name has been changed.";
                header("Location: /home/myprofile");
                exit;

        }
    }

    public function changepassword()
    {

        require("session_util.php");

        if($_POST != null) {

                $id = (int) $_POST["id"];
                $juser = $this->model->getUserByID($id);

                $currpass = $_POST["currpass"];
                $currpass = hash('SHA256', $juser->salt.$currpass);
                $pass1 = $_POST["pass1"];
                $pass2 = $_POST["pass2"];

                        $currpass_known = $juser->password;

                        if (strlen($pass1) < 4) {
                                $_SESSION["msg"] = "The new password was too short.  Password not changed.";
                                header("Location: /home/changepassword");
                                exit;
                        } else if ($pass1 != $pass2) {
                                $_SESSION["msg"] = "The new passwords did not match.  Password not changed.";
                                header("Location: /home/changepassword");
                                exit;
                        }

                        $pass1 = hash('SHA256', $juser->salt.$pass1);
                        $pass2 = hash('SHA256', $juser->salt.$pass2);

                        if($currpass == $currpass_known) {
                                $result =  $this->model->updateUserPassword($id, $pass1);

                                if($result==true) {
                                        $_SESSION["msg"] = "Password has been changed.";
                                        header("Location: /");
                                        exit;
                                }

                        } else {
                                $_SESSION["msg"] = "Your current password was not correct.  Password not changed.";
                                header("Location: /home/changepassword");
                                exit;
                        }
                        $_SESSION["msg"] = "There was an unknown problem.";
                        header("Location: /home/changepassword");


        }


        $title = "Change Password";
        $content["name"] = $title;
        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/change_password.php';
        require APP . 'view/_templates/footer.php';

    }

    public function login_callback() {
        if(session_id() == '') {
                session_start();
        }

        if($_GET['token'] != null) {

                require_once  APP . 'libs/httpful.phar';
                $uri = "https://login.ins.ink/token.php";
                $body['token'] = $_GET['token'];
                $response = \Httpful\Request::post($uri)
                ->body(http_build_query($body))
                ->addHeader('Content-Type','application/x-www-form-urlencoded')
                ->send();

                //echo "<pre>" . print_r($response, true) . "</pre>";
                //exit;

                $token = json_decode($response->body);

                $juser = $this->model->getUserByEmail($token->unique_name);

                $destination = "/home/logout";

                if($juser != null) {

                        if ($juser->status != 'active') {
                                $_SESSION["msg"] = "We're sorry: this account does not have access to this site.";
                        } else {

                                $this->model->updateLastLogin($juser->id);
                                $_SESSION["logged_in"] = "true";
                                $_SESSION["user_id"] = $juser->id;
                                $hidemenu = false;
                                $destination = $_SESSION['page_target'];
                        }
                } else {
                        $_SESSION['msg'] = "User not found";
                }


                header("Location: $destination");
                exit;
        }

    }


    public function passresetsend()
    {
        session_start();
        $email = $_POST['email'];
                $user = $this->model->getUserByEmail($email);

                $id = $user->id;

                if($user == null) {
                        $_SESSION['msg'] = "$email is not a recognized user.";
                        header("location: /");
                        exit;
                } else {

                        require_once APP . 'libs/email_func.php';

                        $password = md5(rand(10000,20000));  // md5 of a random number is good enough for a temp password.

                        $result = $this->model->changeTempPassword($id, $password);
                        $message = "<div style=\"font-family: sans-serif;\">";
                        $message .= "<img alt=\"Grind2Energy\" src=\"cid:my-attach\"><br><br>";

                        $message .= "To reset your password for the Grind2Energy website, please click the following link or paste it into your browser's address bar.<br><br>";
                        $message .= "Password reset link: https://".$_SERVER['SERVER_NAME']."/home/pwdreset?id=".$id."&lnk=" . $password . "<br>";

                        $message .="<br><hr><span style=\"width: 100%; text-align: center; font-size:8.0pt; font-family:Arial; color:gray;\">InSinkErator  |   4700 21st Street  |  Racine, Wisconsin 53406  |  800-845-8345</span></div>";

                        $from_address = 'isewebmaster@insinkerator.com';
                        $subject = 'Grind2Energy Password Reset';
                        $from_name = 'Grind2Energy';

                        $args['to'] = $email;
                        $args['embedded_images'][] = array('path' => APP . '/libs/logo.jpg', 'cid' => 'my-attach', 'name' => 'logo.jpg');
                        $args['from'] = $from_address;
                        $args['from_name'] = $from_name;
                        $args['subject'] = $subject;
                        $args['message'] = $message;
                        $args['html'] = true;

                        $sent = email_func($args);

                        if (!$sent) {
                                $_SESSION["msg"] = "an error occurred sending the message.";
                                header("Location: /");
                        } else {
                                $_SESSION['msg'] = "An Email with password reset instructions has been sent to " . $_POST['email'];
                                header("Location: /");
                        }

                }


                header("location: /");

    }

    public function appts_h($hauler_id=0)
    {
        require("session_util.php");

        if($_SESSION['logged_in']=='true') {
                $current_user = $this->model->getUserById($_SESSION['user_id']);
        }

        // If user has permission for hauler (or is admin) then load appointments
        if(($hauler_id == $current_user->hauler_id) || ($current_user->role == 'admin')) {
                $hauler = $this->model->getHaulerById($hauler_id);
                $locations = $this->model->getLocations();
                $appointments = $this->model->getAppointmentsByHauler($hauler_id);
                $title = "Appointments";
                $all = true;
        } else {
                $_SESSION['msg'] = "Hauler ID mismatch.";
                header("location: /home/error");
                exit;
        }

        $content["name"] = $title;


        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/appts_h.php';
        require APP . 'view/_templates/footer.php';
    }

    public function appt_h($id=0)
    {
        require("session_util.php");

        if($_SESSION['logged_in']=='true') {
                $current_user = $this->model->getUserById($_SESSION['user_id']);
        }

        $appointment = $this->model->getAppointmentById($id);

        // If user has permission for hauler (or is admin) then load appointments
        if(($appointment->hauler_id == $current_user->hauler_id) || ($current_user->role == 'admin')) {
                $hauler = $this->model->getHaulerById($appointment->hauler_id);
                $location = $this->model->getLocationById($appointment->location_id);
                $title = "Appointment - " . $location->name;
                $all = true;
        } else {
                $_SESSION['msg'] = " ID mismatch.";
                header("location: /home/error");
                exit;
        }

        $content["name"] = $title;


        // load views
        require APP . 'view/_templates/header.php';
        require APP . 'view/home/appt_h.php';
        require APP . 'view/_templates/footer.php';
    }

    public function dashboard_down() {
        require APP . 'view/error/maintenance.php';
    }

*/

}

?>
