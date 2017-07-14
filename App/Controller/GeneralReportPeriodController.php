<?php
namespace App\Controller;

use App\Config\View as View;
use App\Model\PeriodModel as Period;
use App\Model\TeacherModel as Teacher;
use App\Model\GeneralReportPeriodModel as GeneralReportPeriod;
/**
* 
*/
class GeneralReportPeriodController
{
	
	private $_generalReportPeriod;

	function __construct()
	{
		$this->_generalReportPeriod = new GeneralReportPeriod(DB);
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function indexAction($role)
	{
		// Validanos la Sesion
		if(true):

			// Validamos el rol
			if(isset($role) && $role == 'teacher'):

				$reports = $this->_generalReportPeriod->getGeneralReportPeriodByTeacher(TC)['data'];

				$view = new View(
					'teacher/partials/evaluation/generalReport',
					'home',
					[
						'tittle_panel'	=>	'Informe General de Periodo',
						'reports'	=>	$reports,
						'history'		=>	array(
							'current'	=> '/GeneralReportPeriod/index/teacher'
						)
					]
				);

				$view->execute();

			endif;
		endif;
	}

	/*
	 *
	 * @param $role
	 * @return
	 *
	*/
	public function createAction()
	{
		// Validamos la session
		if(true):
			// Validamos la peticion GET
			if(isset($_GET['rol']) && $_GET['rol'] == 'teacher'):

				$teacher = new Teacher(DB);
				$period = new Period(DB);

				// Pendiende actualizar
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
			endif;
		endif;
	}


	/*
	 *
	 * @param role
	 * @return
	 *
	*/
	public function storeAction()
	{	
		$response = array();
		foreach($_POST['students'] as $key => $id):
					
			$data = array(
				'id_student'		=> $id,
				'id_group'			=> $_POST['group'],
				'id_period'			=> $_POST['period'],
				'id_group_director'	=>	TC,
				'observations'		=> $_POST['observation']
			);

			array_push($response, $this->_generalReportPeriod->save($data));
		endforeach;

		echo json_encode($response);
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function editAction($id_report)
	{
		// Validamos la Sesion
		if(true):
			$response = $this->_generalReportPeriod->find($id_report)['data'][0];

			// Validamos el tipo de usuario
			if(isset($_GET['rol']) && $_GET['rol'] == 'teacher'):

				$view = new View(
					'teacher/partials/evaluation/generalReport',
					'edit',
					[
						'tittle_panel'	=>	'Editar Informe Generale de Periodo',
						'report'		=>	$response,
						'back'			=>	$_GET['options']['back']
					]
				);

				$view->execute();
			elseif($_GET['rol'] == 'institution'):
				echo "institution";
			else:
				echo "404 no se puede mostrar el contenido";
			endif;
		endif;
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function updateAction()
	{
		
		$data = array(
			'id_report'	=>	$_POST['id_report'],
			'observations'		=>	$_POST['observation']
		);

		echo json_encode($this->_generalReportPeriod->update($data));
	}

	/*
	 *
	 * @param
	 * @return
	 *
	*/
	public function deleteAction()
	{
		if($this->_generalReportPeriod->delete($_POST['id_report'])['state']):
			$reports = $this->_generalReportPeriod->getGeneralReportPeriodByTeacher(TC)['data'];

				$view = new View(
					'teacher/partials/evaluation/generalReport',
					'home',
					[
						'tittle_panel'	=>	'Informe General de Periodo',
						'reports'	=>	$reports,
						'history'		=>	array(
							'current'	=> '/GeneralReportPeriod/index/teacher'
						)
					]
				);

				$view->execute();

			$view->execute();
		endif;
	}
}
?>