<?php

namespace App\Controller;

use App\Config\View as View;
use App\Model\GroupModel as Group;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\AsignatureModel as Asignature;
use App\Model\EvaluationPeriodModel as Evaluation;
/**
* 
*/
class TeacherController
{
	
	function __construct()
	{
		
	}
	// 
	public function HomeAction($db='', $idTeacher='')
	{
		$teacher = new Teacher(DB);

		$asginatures = $teacher->getAsignaturesAndGroups(TC)['data'];

		$view = new View(
			'teacher',
			'home',
			[
				'asginatures'	=>	$asginatures
			]
		);

		$view->execute();
	}

	// Metodo index
	public function indexAction($db='', $idTeacher='')
	{
		$subheader = array(
			'title'	=>	'Inicio',
			'icon'	=>	'fa fa-home',
			'items'	=>	array()
		);

		$view = new View(
			'teacher',
			'index',
			[
				'include'	=>	'partials/home.tpl.php',
				'subheader'	=>	$subheader
			]
		);

		$view->execute();
	}

	// 
	public function evaluationAction()
	{
		$teacher = new Teacher(DB);
		$groupsAndAsign = $teacher->getAsignaturesAndGroups(TC)['data'];
		
		// Creamos el subheader para los menus horizontal
		$subheader = array(
			'title'	=>	'Evaluación',
			'icon'	=>	'fa fa-check',
			'items'	=>	array(
				1	=>	array(
					'title'	=>	'Evaluar Periodo',
					'link'	=>	'/teacher/evaluationPeriod',
					'active' =>	'active'
				)
			),
		);

		// Preguntamos si el docente es director de algun grupo
		if($teacher->isDirector(TC))
		{
			array_push($subheader['items'], array(
				'title'	=>	'Observaciones Generales',
				'link'	=>	'/generalObservation/index/teacher',
				'active' =>	''
			));
			array_push($subheader['items'], array(
				'title'	=>	'Informe General de Periodo',
				'link'	=>	'/teacher/generalReportPeriod',
				'active' =>	''
			));
		}

		$view = new View(
			'teacher',
			'index',
			[
				'tittle_panel'		=>	'Evaluar Periodo',
				'include'			=>	'partials/evaluation/home.tpl.php',
				'subheader'			=>	$subheader,
				'groupsAndAsign'	=>	$groupsAndAsign
			]
		);

		$view->execute();
	}

	/*
	 * FUNCIONES QUE SE RENDERIZAN MEDIANTE AJAX
	*/

	// 
	public function evaluationPeriodAction()
	{
		$teacher = new Teacher(DB);
		$groupsAndAsign = $teacher->getAsignaturesAndGroups(TC)['data'];

		$view = new View(
			'teacher/partials/evaluation',
			'home',
			[
				'tittle_panel'		=>	'Evaluar Periodo',
				'groupsAndAsign'	=>	$groupsAndAsign
			]
		);

		$view->execute();
	}

	// 
	public function generalReportPeriodAction()
	{
		$teacher = new Teacher(DB);
		$reports = $teacher->getGeneralReportPeriod(TC)['data'];

		$view = new View(
			'teacher/partials/evaluation/generalReport',
			'home',
			[
				'tittle_panel'	=>	'Informe General de Periodo',
				'reports'	=>	$reports
			]
		);

		$view->execute();
	}

	// 
	public function createGeneralReportPeriodAction()
	{	
		// Validamos la session
		if(true)
		{
			// Validamos la peticion GET
			if(isset($_GET['options']['request']) && $_GET['options']['request']== 'spa')
			{
				$teacher = new Teacher(DB);
				$period = new Period(DB);

				$myGroups = $teacher->getGroupByDirector(TC)['data'];
				$periods = $period->getPeriods()['data'];

				$view = new View(
					'teacher/partials/evaluation/generalReport',
					'create',
					[
						'tittle_panel'		=>	'Crear Informe General de Periodo',
						'myGroups'		=>	$myGroups,
						'periods'		=>	$periods,
						'back'			=>	$_GET['options']['back']
					]
				);

				$view->execute();		
			}
			else
			{
				echo "404 no se puede mostrar esta pagina";
			}
		}
		else
		{

		}
		
	}

	// 




	// 
	public function showFormEvaluatePeriodAction()
	{
		// Validamos la session
		if(true)
		{
			// Validamos la peticion GET
			if(isset($_GET['options']['request']) && $_GET['options']['request']== 'spa')
			{
				$group = new Group(DB);
				$asignature = new Asignature(DB);

				$infoGroup = $group->find($_GET['id_group'])['data'][0];
				$infoAsignature = $asignature->find($_GET['id_asignature'])['data'][0];

				$view = new View(
					'teacher/partials/evaluation',
					'formEvaluatePeriod',
					[
						'tittle_panel'	=>	'Evaluar periodo pendiente',
						'group'			=> 	$infoGroup,
						'asignature'	=>	$infoAsignature,
						'back'			=>	$_GET['options']['back']
					]
				);

				$view->execute();

			}else{
				echo "404 no se puede mostrar esta pagina";
			}
		}else{

		}
	}


	// 
	public function getStudentWithoutPeriodEvaluationAction(
		$column,
		$id_asignature, 
		$id_group
	){

		$evaluation = new Evaluation(DB);

		$students = $evaluation->getPeriodsWithOutEvaluating($column, $id_asignature, $id_group)['data'];

		// print_r($students);
		$view = new View(
			'teacher/partials/evaluation',
			'formEvaluatePeriodRender',
			[
				'students'	=> $students,
				'periodo'	=>	$column
			]
		);

		$view->execute();
	}

	// 
	public function updatePeriodAction($period, $id_student, $id_asignature, $value)
	{
		$evaluation = new Evaluation(DB);

		echo $evaluation->updatePeriod($period, $id_student, $id_asignature, $value);
	}



	// PENDIENTE ESPERANDO LA SUBCONSULTA 
	public function showFormEvaluationSheetAction($id_asginature, $id_group)
	{
		$group = new Group(DB);
		$asignature = new Asignature(DB);

		$infoGroup = $group->getInfo($id_group)['data'][0];
		$infoAsignature = $asignature->find($id_asginature)['data'][0];

		


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