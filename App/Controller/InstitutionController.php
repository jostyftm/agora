<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\InstitutionModel as Institution;
/**
* 
*/
class InstitutionController
{
	
	function __construct()
	{
		
	}

	function indexAction()
	{

	}

	public function showFormgradeBookAction($db='')
	{
		$institution = new Institution(DB);

		$sedes = $institution->getSedes()['data'];

		$view = new View(
			'gradeBook',
			'formGradeBook',
			[
				'sedes'	=>	$sedes
			]
		);

		$view->execute();
	}

}

?>