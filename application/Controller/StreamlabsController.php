<?php

namespace Mini\Controller;

use Mini\Model\Auth;

class StreamlabsController
{

	public function index()
	{
		require("SessionUtil.php");
		$Auth=new Auth();
		$juser = $Auth->getUserById($_SESSION['user_id']);
		$streamlabs=array();

		if (!$Auth->isOauthSet2($_SESSION['user_id'],'streamlabs'))
		{
			if (!isset($_GET["code"]))
			{
				$STATECODE=$Auth->setStateCode($_SESSION['user_id']);
				$streamlabs=$Auth->getConfig($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
				require APP . 'view/_templates/header.php';
				require APP . 'view/streamlabs/login.php';
				require APP . 'view/_templates/footer.php';
				exit();
			}
			else
			{
				if (! $Auth->checkStateCode($_SESSION['user_id'],$_GET["state"]))
				{
					header("location: /");
					exit();
				}
				$this->getAccessToken($_GET["code"]);
			}
		}
		else
		{
			print_r($this->validateAccessToken());
		}
		require APP . 'view/_templates/header.php';
		require APP . 'view/twitch/index.php';
		require APP . 'view/_templates/footer.php';
	}

	public function logout()
	{
		require("SessionUtil.php");
                $Auth=new Auth();
		$Auth->clearAccessToken($_SESSION['user_id']);
		require APP . 'view/_templates/header.php';
		require APP . 'view/twitch/logout.php';
		require APP . 'view/_templates/footer.php';
	}

	private function getAccessToken($code)
	{
                $Auth=new Auth();
		$streamlabs=$Auth->getConfig($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
		$url="https://id.twitch.tv/oauth2/token?client_id=" . $streamlabs->client_id .
			"&client_secret=" . $streamlabs->secret .
			"&code=" . $code .
			"&grant_type=authorization_code" .
			"&redirect_uri=https://" . $streamlabs->url;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		$streamlabsIdInfo=$this->getTwitchId($response->access_token);
		$streamlabsLogin=$streamlabsIdInfo->login;
		$streamlabsId=$streamlabsIdInfo->id;
		$Auth->storeAccessToken($response->access_token,$response->refresh_token,$_SESSION['user_id'],$streamlabsLogin,$streamlabsId);
	}

	private function validateAccessToken()
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

	private function getTwitchId($token)
	{
		$Auth=new Auth();
		$streamlabs=$Auth->getConfig($_SERVER['SERVER_NAME'] . "/twitch");
		$payload = [
			'Authorization: Bearer ' . $token,
			'Client-id: ' . $streamlabs->client_id
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
			$streamlabs=$Auth->getConfig($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);

			$payload = [
				'grant_type'=>'refresh_token',
				'refresh_token'=>$authCodes->refresh_token,
				'client_id'=>$streamlabs->client_id,
				'client_secret'=>$streamlabs->secret
			];
			$url="https://id.twitch.tv/oauth2/token";
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
			$response = curl_exec($ch);
			$response = json_decode(curl_exec($ch));
			curl_close($ch);
			$streamlabsIdInfo=$this->getTwitchId($response->access_token);
			$streamlabsLogin=$streamlabsIdInfo->login;
			$streamlabsId=$streamlabsIdInfo->id;
			$Auth->storeAccessToken($response->access_token,$response->refresh_token,$_SESSION['user_id'],$streamlabsLogin,$streamlabsId);
			return $response->expires_in;
		}
		else
		{
			return false;
		}
	}

}
