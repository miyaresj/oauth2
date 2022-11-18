<?

namespace Mini\Model;

use Mini\Core\AuthModel;

class Auth extends AuthModel
{

	public function getConfig($url)
	{
		if (strpos($url,"?"))
		{
			$url=substr($url,0,strpos($url,"?"));
		}
		$sql = "SELECT * from config where url=:url";
		$query = $this->db->prepare($sql);
		$parameters = array(':url' => $url);
		$query->execute($parameters);
		return $query->fetch();
	}

	public function getConfig2($site)
	{
		if (strpos($url,"?"))
		{
			$url=substr($url,0,strpos($url,"?"));
		}
		$sql = "SELECT * from config where site=:site";
		$query = $this->db->prepare($sql);
		$parameters = array(':site' => $site);
		$query->execute($parameters);
		return $query->fetch();
	}

	public function getUser($user_id)
	{
		$sql = "SELECT id, user_id, email, salt, password, temppass, name, status, lastlogin, pwdset, created, updated, admin from users where name = :user_id or user_id = :user_id or email = :user_id";
		$query = $this->db->prepare($sql);
		$parameters = array(':user_id' => $user_id);
		$query->execute($parameters);
		return $query->fetch();
	}

	public function getUserById($id)
	{
		$sql = "SELECT user_id, email, salt, password, temppass, name, status, lastlogin, pwdset, created, updated from users where id = :id";
//		$sql = "SELECT * from users where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		return $query->fetch();
	}

	public function isOauthSet($id)
	{
		$sql = "SELECT LENGTH(access_token) as access_token, LENGTH(refresh_token) as refresh_token from users where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		$result=$query->fetch();
		if (($result->access_token==0) || ($result->refresh_token==0))
                {
			return false;
		}
		else
		{
			return true;
		}
	}

	public function getUserOauth($id)
	{
		$sql = "SELECT access_token, refresh_token, twitch_login, twitch_id from users where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		return $query->fetch();
	}

	public function getUserId($twitch_login)
	{
		$sql = "SELECT id from users where twitch_login = :twitch_login";
		$query = $this->db->prepare($sql);
		$parameters = array(':twitch_login' => $twitch_login);
		$query->execute($parameters);
		$queryRet = $query->fetch();
		if (isset($queryRet->id)) {
			return $queryRet->id;
		} else {
			return NULL;
		}
	}

	public function getUserId2($login,$sec_key,$site)
	{
		$sql = "SELECT A.id,B.sec_key from users A left join auths B on A.id=B.user_id ";
		$sql .= "where (A.user_id = :login1 or A.name = :login2) and B.sec_key = :sec_key and B.site = :site";
		$query = $this->db->prepare($sql);
		$parameters = array(':login1' => $login, ':login2' => $login, ':sec_key' => $sec_key, ':site' => $site);
		$query->execute($parameters);
		$queryRet = $query->fetch();
		if (isset($queryRet->id)) {
			return $queryRet->id;
		} else {
			return NULL;
		}
	}

	public function getUserOauthByLogin($twitch_login)
	{
		$sql = "SELECT access_token, refresh_token, twitch_login, twitch_id from users where twitch_login = :twitch_login";
		$query = $this->db->prepare($sql);
		$parameters = array(':twitch_login' => $twitch_login);
		$query->execute($parameters);
		return $query->fetch();
	}

/*
	public function getUserMinById($id)
	{
		$sql = "SELECT id, user_id, name, status from users where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		return $query->fetch();
	}
*/

	public function updateLastLogin($id)
	{
		$sql = "UPDATE users set lastlogin=CURRENT_TIMESTAMP where id = :user_id";
		$query = $this->db->prepare($sql);
		$parameters = array(':user_id' => $id);
		$query->execute($parameters);
	}

	public function setStateCode($id)
	{
		$STATECODE=hash('SHA256',time());
                $sql = "UPDATE users set statecode='$STATECODE' where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		return $STATECODE;
	}

	public function checkStateCode($id,$statecode)
	{
                $sql = "select statecode from users where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
		return ($query->fetch()->statecode == $statecode);
	}

	public function storeAccessToken($access_token,$refresh_token,$id,$twitchLogin,$twitch_id)
	{
                $sql = "update users set access_token=:access_token, refresh_token=:refresh_token, twitch_login=:twitch_login, twitch_id=:twitch_id  where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':access_token' => $access_token, ':refresh_token' => $refresh_token, ':id' => $id, ':twitch_login' => $twitchLogin, ':twitch_id' => $twitch_id);
		$query->execute($parameters);
	}

	public function storeAccessTokenNoTwitch($access_token,$refresh_token,$id)
	{
                $sql = "update users set access_token=:access_token, refresh_token=:refresh_token where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':access_token' => $access_token, ':refresh_token' => $refresh_token, ':id' => $id );
		$query->execute($parameters);
	}

	public function clearAccessToken($id)
	{
                $sql = "update users set access_token='', refresh_token='' where id = :id";
		$query = $this->db->prepare($sql);
		$parameters = array(':id' => $id);
		$query->execute($parameters);
	}

/////// New

	public function isOauthSet2($user_id,$site)
	{
		$sql = "SELECT LENGTH(access_token) as access_token, LENGTH(refresh_token) as refresh_token from auths ";
		$sql .= "where user_id = :user_id and site=:site";
		$query = $this->db->prepare($sql);
		$parameters = array(':user_id' => $user_id, ':site' => $site);
		$query->execute($parameters);
		$result=$query->fetch();
//print_r($result);
		if (!$result) {
			return false;
		}
		if (($result->access_token==0) || ($result->refresh_token==0))
                {
			return false;
		}
		else
		{
			return true;
		}
	}



}
?>
