<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\GroupModel as Group;
use App\Model\TeacherModel as Teacher;
use App\Model\AsignatureModel as Asignature;
use App\Model\EvaluationPeriodModel as EvaluationPeriodModel;
/**
* 
*/
class TeacherController
{
	
	function __construct()
	{
		
	}

	public function indexAction($db='', $idTeacher='')
	{
		$teacher = new Teacher(DB);

		$asginatures = $teacher->getAsignaturesAndGroups(TC)['data'];

		$view = new View(
			'teacher',
			'index',
			[
				'include'	=>	'partials/home.tpl.php'
			]
		);

		$view->execute();
	}

	public function showFormEvaluatePeriodAction($db='', $id_asginature, $id_group)
	{

		$evaluation = new EvaluationPeriodModel(DB);

		$response = $evaluation->getPeriodWithOutEvaluating($id_group, $id_asginature)['data'];

		$info = $response[0];
		$view = new View(
			'teacher',
			'formEvaluatePeriod',
			[
				'info'	=> $info
			]
		);

		$view->execute();	
	}

	public function showFormEvaluationSheetAction($id_asginature, $id_group)
	{
		$group = new Group(DB);
		$asignature = new Asignature(DB);

		$infoGroup = $group->getInfo($id_group)['data'][0];
		$infoAsignature = $asignature->getInfo($id_asginature)['data'][0];

		// 	logica para determinar el tipo de modelo de evaluacion y mandar el modelo correspondiente


		$view = new View(
			'reportPDF',
			'formEvaluationSheet', //.$model,
			[
				'asignature'	=> 	$infoAsignature,
				'group'			=>	$infoGroup,
				// 'model'			=> 	$model
			]
		);

		$view->execute();		
	}
}
?>