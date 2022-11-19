<?php

namespace Mini\Controller;

use Mini\Model\Auth;

class InternalController
{
	public function index($user_id=0,$sec_key=0)
	{
//		require("SecurityUtil.php");
//		require APP . 'view/test/index.php';
		exit();
	}

	function getkeys($twitch_login=0,$sec_key=0)
	{
//		require("SecurityUtil.php");
		$Auth=new Auth();
		$user_id=$Auth->getUserId($twitch_login);
		if (!$user_id) {
			exit();
		}

		$expires_in=$this->validateAccessToken($user_id,$sec_key);
		$keys=json_encode($Auth->getUserOauthByLogin($user_id,$sec_key));
		$twitch=$Auth->getConfig($user_id,$sec_key);
		$keys=json_decode($keys,true);
		$keys["expires_in"]=$expires_in;
		$keys["client_id"]=$twitch->client_id;
		$keys["client_secret"]=$twitch->secret;
		echo json_encode($keys);
	}

	function getkeys2($site=0,$login=0,$sec_key)
	{
		$Auth=new Auth();
		$user_id=$Auth->getUserId2($login,$sec_key,$site);
		if (!$user_id) {
			exit();
		}

/*
		$expires_in=$this->validateAccessToken($user_id);
		$keys=json_encode($Auth->getUserOauthByLogin($twitch_login));
		$twitch=$Auth->getConfig2($site);
		$keys=json_decode($keys,true);
		$keys["expires_in"]=$expires_in;
		$keys["client_id"]=$twitch->client_id;
		$keys["client_secret"]=$twitch->secret;
		echo json_encode($keys);
*/
	}

	private function validateAccessToken($user_id=0,$sec_key=0)
	{

		$Auth=new Auth();
                if ($Auth->isOauthSet($user_id,$sec_key))
		{
			$authCodes=$Auth->getUserOauth($user_id,$sec_key);
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

	private function refreshAccessToken($user_id=0,$config_id=0)
	{
		$Auth=new Auth();
                if ($Auth->isOauthSet($user_id))
		{
			$authCodes=$Auth->getUserOauth($user_id);
			$twitch=$Auth->getConfig("pi4b.lisabadcat.com/twitch");

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
			$response = json_decode(curl_exec($ch));
			curl_close($ch);
			$Auth->storeAccessTokenNoTwitch($response->access_token,$response->refresh_token,$user_id);
			return $response->expires_in;
		}
		else
		{
			return false;
		}
	}

}

?>
