<?

	if (!isset($_GET["code"]))
	{
		echo	"<a href='https://id.twitch.tv/oauth2/authorize?client_id=" . $twitch->client_id .
			"&redirect_uri=https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] .
			"&response_type=code&scope=" . $twitch->scope .
			"&force_verify=true&state=" . $STATECODE . "'" .
			">Click Here To Link Twitch</a>";
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
