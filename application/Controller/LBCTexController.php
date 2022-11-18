<?php

namespace Mini\Controller;

use Mini\Model\Auth;

class LBCTexController
{
	public function index()
	{
		require APP . 'view/_templates/header.php';
		require APP . 'view/LBCTex/index.php';
		require APP . 'view/_templates/footer.php';
	}

}
