<?php

namespace Mini\Controller;

use Mini\Model\Auth;

class TwitchController
{

	public function index()
	{
 		require("SessionUtil.php");
		$Auth=new Auth();
		$juser = $Auth->getUserById($_SESSION['user_id']);
		$twitch=array();

		if (!$Auth->isOauthSetRef($_SESSION['user_id'],$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'])) // oauth not set
		{
			if (!isset($_GET["code"])) // redirect to twitch
			{
				$STATECODE=$Auth->setStateCode($_SESSION['user_id'],$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
				$twitch=$Auth->getConfigUrl($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
				require APP . 'view/_templates/header.php';
				require APP . 'view/twitch/login.php';
				require APP . 'view/_templates/footer.php';
				exit();
			}
			else // receiving code from twitch
			{
				if (! $Auth->checkStateCode($_SESSION['user_id'],$_GET["state"]))	// failed oauth authentication
				{
					exit();
				}
				else	// State OK, Oauth2 login successful
				{
					$_SESSION['page_target']=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?'));
					$this->getAccessToken($_GET['code']);	// this function assumes this passes
					header('Location: ' . $_SESSION['page_target']);
				}
			}

		}
		else	// oauth already logged in
		{
			$twitch=$Auth->getConfigUrl($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
			require APP . 'view/_templates/header.php';
			require APP . 'view/twitch/index.php';
			require APP . 'view/_templates/footer.php';
		}

	}

	public function logout()
	{
		require("SessionUtil.php");
                $Auth=new Auth();

/*
echo $_SERVER['SERVER_NAME'] . substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'/',1)) . '<br>';
echo $_SERVER['HTTP_HOST'] . '<br>';
echo $_SERVER['PHP_SELF'] . '<br>';
exit();
*/

		$Auth->clearAccessToken($_SESSION['user_id'],$_SERVER['SERVER_NAME'] . substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'/',1)));

		require APP . 'view/_templates/header.php';
		require APP . 'view/twitch/logout.php';
		require APP . 'view/_templates/footer.php';
	}

	private function getAccessToken($code=0)
	{
                $Auth=new Auth();
		$twitch=$Auth->getConfigUrl($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
		$url="https://id.twitch.tv/oauth2/token?client_id=" . $twitch->client_id .
			"&client_secret=" . $twitch->secret .
			"&code=" . $code .
			"&grant_type=authorization_code" .
			"&redirect_uri=https://" . $twitch->url;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
echo "storing";
		$Auth->storeAccessToken($response->access_token,$response->refresh_token,$_GET['state']);
	}



/*
	private function validateAccessToken($user_id=0,$sec_key=0)
	{
		require("SessionUtil.php");
		$Auth=new Auth();
                if ($Auth->isOauthSet($_SESSION['user_id']))
		{
			$authCodes=$Auth->getUserOauth($_SESSION['user_id']);
			$url="https://id.twitch.tv/oauth2/validate";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: OAuth ' . $authCodes->access_token));
			$response = json_decode(curl_exec($ch));
			curl_close($ch);
			if (array_key_exists('status',$response))
			{
				if ($response->status=="401")
				{
					return $this->refreshAccessToken();
				}
			}
			else
			{
				if ($response->expires_in<600)
				{
					return $this->refreshAccessToken();
				}
			}
			return $response->expires_in;
		}
		else
		{
			return false;
		}
	}
*/










        private function validateAccessToken($user_id=0,$config_id=0)
        {

//echo $user_id . '<br>';
//echo $config_id . '<br>';
//exit();
                $Auth=new Auth();
                if ($Auth->isOauthSetConf($user_id,$config_id))
                {
                        $authCodes=$Auth->getUserOauthConf($user_id,$config_id);

//var_dump($authCodes);
//exit();


                        $url="https://id.twitch.tv/oauth2/validate";
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Authorization: OAuth ' . $authCodes->access_token));
                        $response = json_decode(curl_exec($ch));
                        curl_close($ch);

//var_dump($response);
//exit();

                        if (array_key_exists('status',$response))
                        {
                                if ($response->status=="401")
                                {
                                        return $this->refreshAccessToken($user_id);
                                }
                        }
                        else
                        {
                                if ($response->expires_in<600)
                                {
                                        return $this->refreshAccessToken($user_id);
                                }
                        }
                        return $response->expires_in;
                }
                else
                {
                        return false;
                }
        }




	private function getTwitchId($token)
	{
		$Auth=new Auth();
		$twitch=$Auth->getConfig($_SERVER['SERVER_NAME'] . "/twitch");
		$payload = [
			'Authorization: Bearer ' . $token,
			'Client-id: ' . $twitch->client_id
		];
		$url="https://api.twitch.tv/helix/users";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $payload);
		$response = curl_exec($ch);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		return $response->data[0];
	}

	private function refreshAccessToken()
	{
		require("SessionUtil.php");
		$Auth=new Auth();
                if ($Auth->isOauthSet($_SESSION['user_id']))
		{
			$authCodes=$Auth->getUserOauth($_SESSION['user_id']);
			$twitch=$Auth->getConfig($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

			$payload = [
				'grant_type'=>'refresh_token',
				'refresh_token'=>$authCodes->refresh_token,
				'client_id'=>$twitch->client_id,
				'client_secret'=>$twitch->secret
			];
			$url="https://id.twitch.tv/oauth2/token";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
			$response = curl_exec($ch);
//var_dump($response);
//exit();
			$response = json_decode(curl_exec($ch));
			curl_close($ch);
			$twitchIdInfo=$this->getTwitchId($response->access_token);
			$twitchLogin=$twitchIdInfo->login;
			$twitchId=$twitchIdInfo->id;
			$Auth->storeAccessToken($response->access_token,$response->refresh_token,$_SESSION['user_id'],$twitchLogin,$twitchId);
			return $response->expires_in;
		}
		else
		{
			return false;
		}
	}

}
