<?php

namespace Mini\Controller;

//use Mini\Model\Auth;

class HelpController
{
	public function index()
	{
		require APP . 'view/_templates/header.php';
		require APP . 'view/help/index.php';
	        require APP . 'view/_templates/footer.php';
	}
}

?>
