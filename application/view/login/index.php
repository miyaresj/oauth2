<title>LBC Login</title>
	<h1>PLEASE LOGIN TO CONTINUE:</h1>

	<?
		if (@$_GET["e"] == "invalid") {
			echo "<span style=\"color:#ffffff\"><b>We're sorry: those credentials were not correct.  Please try again.</b></span><BR><BR>";
		}
	?>

	<form action="/login" method="post">
		<b>User ID:</b><BR>
		<input type="text" name="username" size="35" value="<? echo @$_SESSION['user'];?>">
		<BR><BR>
		<b>Password:</b><BR>
		<input type="password" name="password" size="20" value="<? echo @$_SESSION['pass'];?>">
		<BR><BR>
		<input type="submit" value="Login">
	</form>
	<br>
	<a href="/help#login">Trouble Logging In?</a>
<?
/*
	<br>
	<br>
	<br>
	<br>
	<span onClick="javascript:show_block('forgot_password');" class="small" style="color: #005294; cursor: pointer;">
		<b>Forgot Your Password?</b></span>
	<br>
	<br>
	<div id="forgot_password" class="text"
		<?
			if ($_GET["s"] != 2) {
				echo "style=\"display:none; visibility:hidden;\";";
			}
			else
			{
				echo "style=\"display:block; visibility:visible;\";";
			}
		?>
		<br>
		<?
			if ($_GET["msg"] != "") {
				$msg = $_GET["msg"];
				$msg = str_replace("_", " ", $msg);
				echo "<span style=\"color:#CC0000\">" . $msg . ".</span><BR><BR>";
			}
		?>
		<form action="action/password_reset.php" method="post">
			<fieldset>
			<legend>Please enter your Email address to reset your password</legend>
			<b>Email Address:</b><BR>
			<input type="text" name="email" size="40" />
			<br>
			<br>
			<input type="submit" value="Send Reset Email" />
			</fieldset>
		</form>
	</div>
	<br>
*/
?>
