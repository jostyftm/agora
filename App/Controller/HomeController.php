<?php

namespace App\Controller;

use App\Model\InstitutionModel as Institution;
use App\Config\View as View;

class HomeController
{
	
	function __construct()
	{
		
	}

	function indexAction(){

		$institution = new Institution(DB);
		
		$view = new View(
			'home',
			'home'
		);

		$view->execute();
	}
}