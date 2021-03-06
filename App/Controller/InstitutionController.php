<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\EvaluationPeriodModel as Evaluation;
use App\Model\InstitutionModel as Institution;
use App\Model\PeriodModel as Period;
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

	// 
	public function sheetAction($db= '')
	{
		$institution = new Institution($db);
		$sedes = $institution->getSedes()['data'];
		$grados = $institution->getGrades()['data'];
		$jornadas = $institution->getJourneys()['data'];
		$period = new Period($db);

		$view = new View(
			'institution/sheet',
			'index',
			[
				'db' 	=>	$db,
				'sedes'	=>	$sedes,
				'grados'	=> $grados,
				'journeys'	=>	$jornadas,
				'periods'	=>	$period->all()['data'],
			]
		);

		$view->execute();
	}	

	public function showFormgradeBookAction($db='')
	{
		$institution = new Institution($db);
		$sedes = $institution->getSedes()['data'];

		$view = new View(
			'gradeBook',
			'formGradeBook',
			[
				'sedes'	=>	$sedes,
				'periods'	=>	$institution->getPeriods()['data'],
				'db'	=>	$db
			]
		);

		$view->execute();
	}

	// 
	public function showFormEvaluationSheetAction($db)
	{
		$institution = new Institution($db);
		$period = new Period($db);

		$sedes = $institution->getSedes()['data'];

		$view = new View(
			'institution',
			'formEvaluationSheet',
			[
				'db' 	=>	$db,
				'sedes'	=>	$sedes,
				'periods'	=>	$period->all()['data']
			]
		);

		$view->execute();
	}
	
}

?>