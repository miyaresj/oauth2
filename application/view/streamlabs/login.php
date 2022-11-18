<?
	if (!isset($_GET["code"]))
	{

//		echo	"<a href='https://id.twitch.tv/oauth2/authorize?client_id=" . $twitch->client_id .
		echo	"<a href='https://www.streamlabs.com/api/v1.0/authorize?client_id=" . $streamlabs->client_id .
			"&redirect_uri=https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] .
			"&response_type=code&scope=" . $streamlabs->scope . "'" .
//			"&force_verify=true&state=" . $STATECODE . "'" .
			">Click Here To Link Streamlabs</a>";

	}
	else
	{
		echo $_GET["code"];
		echo "<br>";
		echo $_GET["state"];
		echo "<br>";
		echo $_GET["scope"];
		echo "<br>";
	}
?>
