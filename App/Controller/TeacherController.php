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
	
	private $_teacher;
	private $_periods;

	function __construct()
	{
		$this->_teacher = new Teacher(DB);	
		$this->_periods = new Period(DB);
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
					'title'	=>	'Evaluar',
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
				'link'	=>	'/generalReportPeriod/index/teacher',
				'active' =>	''
			));
		}

		$view = new View(
			'teacher',
			'index',
			[
				'tittle_panel'		=>	'',
				'include'			=>	'partials/evaluation/home.tpl.php',
				'subheader'			=>	$subheader,
				'groupsAndAsign'	=>	$groupsAndAsign
			]
		);

		$view->execute();
	}

	// 
	public function sheetsAction()
	{
		// Validamos Sesion
		if(true):

			$periods = $this->_periods->getPeriods()['data'];

			$asignatures = $this->_teacher->getAsignaturesAndGroups(TC)['data'];
			$subheader = array(
				'title'	=>	'Planillas',
				'icon'	=>	'fa fa-file-text-o',
				'items'	=>	array()
			);

			$view = new View(
				'teacher',
				'index',
				[
					'include'		=>	'partials/sheets/home.tpl.php',
					'tittle_panel'	=>	'Planillas',
					'subheader'		=>	$subheader,
					'asignatures'	=>	$asignatures,
					'periods'		=>	$periods

				]
			);

			$view->execute();
		endif;
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
			'teacher/partials/sheets',
			'evaluation', //.$model,
			[	
				'tittle_panel'	=>	'Planilla de Evaluación',
				'asignature'	=> 	$infoAsignature,
				'group'			=>	$infoGroup,
				// 'model'			=> 	$model
			]
		);

		$view->execute();		
	}
}
?>